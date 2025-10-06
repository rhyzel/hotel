<?php
namespace CRM\Lib;

class LoyaltyProgramRepository {
    private \PDO $db;

    public function __construct(\PDO $db) {
        $this->db = $db;
    }

    public function getAll(): array {
        $stmt = $this->db->query("SELECT * FROM loyalty_programs ORDER BY created_at DESC");
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return array_map(fn($row) => new LoyaltyProgram($row), $rows);
    }

    public function getById(int $id): ?LoyaltyProgram {
        $stmt = $this->db->prepare("SELECT * FROM loyalty_programs WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ? new LoyaltyProgram($row) : null;
    }

    public function create(array $data): LoyaltyProgram {
        $sql = "INSERT INTO loyalty_programs (name, tier, points_rate, benefits, description, members_count, status, discount_rate, created_at)
                VALUES (:name, :tier, :points_rate, :benefits, :description, :members_count, :status, :discount_rate, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':name' => $data['name'],
            ':tier' => $data['tier'],
            ':points_rate' => $data['points_rate'],
            ':benefits' => $data['benefits'] ?? '',
            ':description' => $data['description'] ?? '',
            ':members_count' => $data['members_count'] ?? 0,
            ':status' => $data['status'] ?? 'active',
            ':discount_rate' => $data['discount_rate'] ?? 0.0
        ]);
        $id = $this->db->lastInsertId();
        return $this->getById($id);
    }

    public function update(array $data): bool {
        if (empty($data['id'])) return false;
        $fields = [];
        $params = [':id' => $data['id']];
        foreach (['name','tier','points_rate','benefits','description','members_count','status','discount_rate'] as $field) {
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

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM loyalty_programs WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    // Sync members_count, points_redeemed, rewards_given, revenue_impact for each tier
    public function syncMembersCount(): void {
        $tiers = ['bronze', 'silver', 'gold', 'platinum'];
        foreach ($tiers as $tier) {
            // Members count
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM guests WHERE loyalty_tier = :tier");
            $stmt->execute([':tier' => $tier]);
            $count = (int)$stmt->fetchColumn();

            // Points rate for this tier
            $stmt = $this->db->prepare("SELECT points_rate FROM loyalty_programs WHERE tier = :tier LIMIT 1");
            $stmt->execute([':tier' => $tier]);
            $points_rate = (float)($stmt->fetchColumn() ?: 1);

            // Points earned from lounge_orders for this tier
            $sql = "SELECT SUM(lo.total_amount * :points_rate) 
                    FROM lounge_orders lo
                    INNER JOIN guests g ON lo.guest_id = g.guest_id
                    WHERE g.loyalty_tier = :tier";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':points_rate' => $points_rate, ':tier' => $tier]);
            $points_earned = (int)($stmt->fetchColumn() ?: 0);

            // Points redeemed from reward_transactions for this tier
            $sql = "SELECT SUM(rt.points_amount)
                    FROM reward_transactions rt
                    INNER JOIN guests g ON rt.guest_id = g.guest_id
                    WHERE rt.transaction_type = 'points_redeemed' AND g.loyalty_tier = :tier";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':tier' => $tier]);
            $points_redeemed = (int)($stmt->fetchColumn() ?: 0);

            // Rewards given from reward_transactions for this tier
            $sql = "SELECT COUNT(*) 
                    FROM reward_transactions rt
                    INNER JOIN guests g ON rt.guest_id = g.guest_id
                    WHERE rt.transaction_type = 'reward_given' AND g.loyalty_tier = :tier";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':tier' => $tier]);
            $rewards_given = (int)($stmt->fetchColumn() ?: 0);

            // Revenue impact: sum total_amount from lounge_orders, giftshop_sales, restaurant_orders, room_dining_orders for this tier
            $revenue_impact = 0.0;

            // Lounge Orders
            $sql = "SELECT SUM(lo.total_amount) 
                    FROM lounge_orders lo
                    INNER JOIN guests g ON lo.guest_id = g.guest_id
                    WHERE g.loyalty_tier = :tier";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':tier' => $tier]);
            $revenue_impact += (float)($stmt->fetchColumn() ?: 0);

            // Giftshop Sales
            $sql = "SELECT SUM(gs.total_amount)
                    FROM giftshop_sales gs
                    INNER JOIN guests g ON gs.guest_id = g.guest_id
                    WHERE g.loyalty_tier = :tier";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':tier' => $tier]);
            $revenue_impact += (float)($stmt->fetchColumn() ?: 0);

            // Restaurant Orders
            $sql = "SELECT SUM(ro.total_amount)
                    FROM restaurant_orders ro
                    INNER JOIN guests g ON ro.guest_id = g.guest_id
                    WHERE g.loyalty_tier = :tier";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':tier' => $tier]);
            $revenue_impact += (float)($stmt->fetchColumn() ?: 0);

            // Room Dining Orders
            $sql = "SELECT SUM(rd.total_amount)
                    FROM room_dining_orders rd
                    INNER JOIN guests g ON rd.guest_id = g.guest_id
                    WHERE g.loyalty_tier = :tier";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':tier' => $tier]);
            $revenue_impact += (float)($stmt->fetchColumn() ?: 0);

            // Update loyalty_programs table
            $this->db->prepare("UPDATE loyalty_programs 
                SET members_count = :count, 
                    points_redeemed = :points_redeemed, 
                    rewards_given = :rewards_given, 
                    revenue_impact = :revenue_impact
                WHERE tier = :tier")
                ->execute([
                    ':count' => $count,
                    ':points_redeemed' => $points_redeemed,
                    ':rewards_given' => $rewards_given,
                    ':revenue_impact' => $revenue_impact,
                    ':tier' => $tier
                ]);
        }
    }

    // Get total points earned from lounge_orders for a guest or all guests
    public function getLoungeOrderPoints($guest_id = null): float {
        // You may want to join with guests to get the tier and multiply by points_rate if needed
        // For now, just sum total_amount for lounge_orders (optionally filter by guest)
        $sql = "SELECT SUM(total_amount) as total_spent FROM lounge_orders";
        $params = [];
        if ($guest_id) {
            $sql .= " WHERE guest_id = :guest_id";
            $params[':guest_id'] = $guest_id;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (float)($row['total_spent'] ?? 0);
    }
}
