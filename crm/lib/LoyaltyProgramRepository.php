<?php
namespace CRM\Lib;

class LoyaltyProgramRepository {
    private \PDO $db;

    public function __construct(\PDO $db) {
        $this->db = $db;
    }

    /** ✅ Get all loyalty programs (with latest first) */
    public function getAll(): array {
        $stmt = $this->db->query("SELECT * FROM loyalty_programs ORDER BY created_at DESC");
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return array_map(fn($row) => new LoyaltyProgram($row), $rows);
    }

    /** ✅ Get single loyalty program by ID */
    public function getById(int $id): ?LoyaltyProgram {
        $stmt = $this->db->prepare("SELECT * FROM loyalty_programs WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ? new LoyaltyProgram($row) : null;
    }

    /** ✅ Create new loyalty program */
    public function create(array $data): LoyaltyProgram {
        $sql = "INSERT INTO loyalty_programs 
                (name, tier, points_rate, benefits, description, members_count, status, discount_rate, points_earned, points_redeemed, created_at)
                VALUES (:name, :tier, :points_rate, :benefits, :description, :members_count, :status, :discount_rate, :points_earned, :points_redeemed, NOW())";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':name' => $data['name'],
            ':tier' => $data['tier'],
            ':points_rate' => $data['points_rate'],
            ':benefits' => $data['benefits'] ?? '',
            ':description' => $data['description'] ?? '',
            ':members_count' => $data['members_count'] ?? 0,
            ':status' => $data['status'] ?? 'active',
            ':discount_rate' => $data['discount_rate'] ?? 0.0,
            ':points_earned' => $data['points_earned'] ?? 0.0,
            ':points_redeemed' => $data['points_redeemed'] ?? 0.0
        ]);

        $id = $this->db->lastInsertId();
        return $this->getById($id);
    }

    /** ✅ Update existing loyalty program */
    public function update(array $data): bool {
        if (empty($data['id'])) return false;
        $fields = [];
        $params = [':id' => $data['id']];

        foreach (['name','tier','points_rate','benefits','description','members_count','status','discount_rate','points_earned','points_redeemed'] as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = :$field";
                $params[":$field"] = $data[$field];
            }
        }

        if (!$fields) return false;

        $sql = "UPDATE loyalty_programs SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /** ✅ Delete loyalty program by ID */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM loyalty_programs WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    // -------------------------------------------------------------------------
    // 🧩 AUTOMATIC SYNCING AND VALIDATION LOGIC
    // -------------------------------------------------------------------------

    /** ✅ Validate guest tiers and sync counts, points, and revenue */
    public function syncMembersCount(): void {
        // Get all existing loyalty tiers from guests
        $stmt = $this->db->query("SELECT DISTINCT loyalty_tier FROM guests WHERE loyalty_tier IS NOT NULL");
        $guestTiers = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        // Reset members count to zero (clean state)
        $this->db->exec("UPDATE loyalty_programs SET members_count = 0");

        foreach ($guestTiers as $tier) {
            // Count members under this tier
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM guests WHERE loyalty_tier = :tier");
            $stmt->execute([':tier' => $tier]);
            $count = (int)$stmt->fetchColumn();

            // Get the tier’s point rate
            $stmt = $this->db->prepare("SELECT points_rate FROM loyalty_programs WHERE tier = :tier LIMIT 1");
            $stmt->execute([':tier' => $tier]);
            $points_rate = (float)($stmt->fetchColumn() ?: 1.0);

            // Calculate total points earned (from billing + lounge orders)
            $sql = "SELECT SUM(gb.total_amount * :rate)
                    FROM guest_billing gb
                    INNER JOIN guests g ON gb.guest_id = g.guest_id
                    WHERE g.loyalty_tier = :tier";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':rate' => $points_rate, ':tier' => $tier]);
            $points_earned = (float)($stmt->fetchColumn() ?: 0);

            // Calculate total points redeemed
            $sql = "SELECT SUM(rt.points_amount)
                    FROM reward_transactions rt
                    INNER JOIN guests g ON rt.guest_id = g.guest_id
                    WHERE rt.transaction_type = 'points_redeemed' AND g.loyalty_tier = :tier";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':tier' => $tier]);
            $points_redeemed = (float)($stmt->fetchColumn() ?: 0);

            // Count how many rewards given
            $sql = "SELECT COUNT(*)
                    FROM reward_transactions rt
                    INNER JOIN guests g ON rt.guest_id = g.guest_id
                    WHERE rt.transaction_type = 'reward_given' AND g.loyalty_tier = :tier";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':tier' => $tier]);
            $rewards_given = (int)$stmt->fetchColumn();

            // Compute total revenue (from multiple POS tables)
            $revenue_impact = 0.0;
            $tables = [
                'guest_billing gb',
                'lounge_orders lo',
                'giftshop_sales gs',
                'restaurant_orders ro',
                'room_dining_orders rd'
            ];

            foreach ($tables as $table) {
                $alias = explode(' ', $table)[1];
                $sql = "SELECT SUM($alias.total_amount)
                        FROM $table
                        INNER JOIN guests g ON $alias.guest_id = g.guest_id
                        WHERE g.loyalty_tier = :tier";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([':tier' => $tier]);
                $revenue_impact += (float)($stmt->fetchColumn() ?: 0);
            }

            // Update loyalty program record
            $sql = "UPDATE loyalty_programs 
                    SET members_count = :count,
                        points_earned = :points_earned,
                        points_redeemed = :points_redeemed,
                        rewards_given = :rewards_given,
                        revenue_impact = :revenue_impact
                    WHERE tier = :tier";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':count' => $count,
                ':points_earned' => $points_earned,
                ':points_redeemed' => $points_redeemed,
                ':rewards_given' => $rewards_given,
                ':revenue_impact' => $revenue_impact,
                ':tier' => $tier
            ]);
        }
    }

    /** ✅ Update points earned in loyalty_programs (for POS integration) */
    public function syncBillingPointsToLoyalty(): void {
        $tiers = ['bronze', 'silver', 'gold', 'platinum'];

        foreach ($tiers as $tier) {
            $sql = "SELECT SUM(gb.total_amount)
                    FROM guest_billing gb
                    INNER JOIN guests g ON gb.guest_id = g.guest_id
                    WHERE g.loyalty_tier = :tier";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':tier' => $tier]);
            $points_earned = (float)($stmt->fetchColumn() ?: 0);

            $sql = "UPDATE loyalty_programs SET points_earned = :points_earned WHERE tier = :tier";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':points_earned' => $points_earned,
                ':tier' => $tier
            ]);
        }
    }

    /** ✅ Get list of guests and their total earned points (by tier) */
    public function getPointsEarnedPerGuestByTier(string $tier): array {
        $sql = "
            SELECT 
                g.guest_id,
                CONCAT(IFNULL(g.first_name,''), ' ', IFNULL(g.last_name,'')) AS guest_name,
                g.email,
                SUM(gb.total_amount) AS total_points_earned
            FROM guests g
            LEFT JOIN guest_billing gb ON gb.guest_id = g.guest_id
            WHERE g.loyalty_tier = :tier
            GROUP BY g.guest_id, g.email, g.first_name, g.last_name
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':tier' => $tier]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
