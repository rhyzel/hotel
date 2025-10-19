<?php
namespace CRM\Lib;

class GuestService {
    private GuestRepository $repo;

    // Loyalty tier thresholds (adjust as needed)
    private array $tierThresholds = [
        'platinum' => 20000,
        'gold' => 10000,
        'silver' => 5000,
        'bronze' => 0
    ];

    public function __construct(GuestRepository $repo) {
        $this->repo = $repo;
    }

    public function listGuests(string $search = ''): array {
        return $this->repo->getAll($search);
    }

    // Make this public for use by LoyaltyProgramService
    public function getValidTiers(): array {
        $stmt = $this->repo->getDb()->query("SELECT DISTINCT tier FROM loyalty_programs WHERE status = 'active'");
        return array_map('strtolower', $stmt->fetchAll(\PDO::FETCH_COLUMN));
    }

    // Auto-assign tier based on total purchase amount
    private function autoAssignTier(int $guestId): string {
        $db = $this->repo->getDb();
        $stmt = $db->prepare("SELECT SUM(total_amount) FROM guest_billing WHERE guest_id = :guest_id");
        $stmt->execute([':guest_id' => $guestId]);
        $total = (float)$stmt->fetchColumn();

        // Check thresholds from highest to lowest
        foreach ($this->tierThresholds as $tier => $amount) {
            if ($total >= $amount) return $tier;
        }
        return 'bronze';
    }

    public function createGuest(array $data): array {
        // Build name and phone if not present (for compatibility with JS)
        if (empty($data['name']) && !empty($data['first_name'])) {
            $data['name'] = trim($data['first_name'] . ' ' . ($data['last_name'] ?? ''));
        }
        if (empty($data['phone']) && !empty($data['first_phone'])) {
            $data['phone'] = $data['first_phone'];
        }
        // basic validation
        if (empty($data['name']) || empty($data['email']) || empty($data['phone'])) {
            throw new \InvalidArgumentException('Missing required fields: name, email, phone');
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email');
        }
        $validTiers = $this->getValidTiers();
        $manual = isset($data['auto_loyalty_tier']) && $data['auto_loyalty_tier'] === false;
        if ($manual) {
            $tier = strtolower($data['loyalty_tier'] ?? 'bronze');
            if (!in_array($tier, $validTiers)) {
                throw new \InvalidArgumentException('Invalid loyalty tier. Allowed: ' . implode(', ', $validTiers));
            }
        }
        // ensure unique email
        // (left to repository or DB unique constraint)
        // After creating guest, auto-assign tier
        $newGuest = $this->repo->create($data);
        $guestId = $newGuest['id'] ?? $newGuest['guest_id'] ?? 0;
        // Assign tier: auto or manual
        if ($manual) {
            $tier = strtolower($data['loyalty_tier'] ?? 'bronze');
        } else {
            $tier = $this->autoAssignTier($guestId);
        }
        $newGuest['loyalty_tier'] = $tier;
        $this->repo->update(['id' => $guestId, 'loyalty_tier' => $tier]);
        return $newGuest;
    }

    public function updateGuest(array $data): bool {
        // Build name and phone if not present (for compatibility with JS)
        if (empty($data['name']) && !empty($data['first_name'])) {
            $data['name'] = trim($data['first_name'] . ' ' . ($data['last_name'] ?? ''));
        }
        if (empty($data['phone']) && !empty($data['first_phone'])) {
            $data['phone'] = $data['first_phone'];
        }
        $manual = isset($data['auto_loyalty_tier']) && $data['auto_loyalty_tier'] === false;
        if ($manual && isset($data['loyalty_tier'])) {
            $validTiers = $this->getValidTiers();
            $tier = strtolower($data['loyalty_tier']);
            if (!in_array($tier, $validTiers)) {
                throw new \InvalidArgumentException('Invalid loyalty tier. Allowed: ' . implode(', ', $validTiers));
            }
        }
        $ok = $this->repo->update($data);
        if ($ok && isset($data['id'])) {
            $tier = $manual && isset($data['loyalty_tier'])
                ? strtolower($data['loyalty_tier'])
                : $this->autoAssignTier($data['id']);
            $this->repo->update(['id' => $data['id'], 'loyalty_tier' => $tier]);
        }
        return $ok;
    }

    public function deleteGuest(int $id): bool {
        return $this->repo->delete($id);
    }

    public function getGuestPurchaseHistory(int $guest_id): array {
        // Query guest_billing table for this guest_id
        $stmt = $this->repo->getDb()->prepare("SELECT * FROM guest_billing WHERE guest_id = :guest_id ORDER BY created_at DESC");
        $stmt->execute([':guest_id' => $guest_id]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Patch: Ensure all fields are present and numbers are valid
        foreach ($rows as &$row) {
            $row['order_id'] = $row['order_id'] ?? '—';
            $row['order_type'] = $row['order_type'] ?? '-';
            $row['item_name'] = $row['item_name'] ?? '-';
            $row['total_amount'] = isset($row['total_amount']) ? floatval($row['total_amount']) : 0.0;
            $row['payment_option'] = $row['payment_option'] ?? '-';
            $row['payment_method'] = $row['payment_method'] ?? '-';
            $row['partial_payment'] = isset($row['partial_payment']) ? floatval($row['partial_payment']) : 0.0;
            $row['remaining_amount'] = isset($row['remaining_amount']) ? floatval($row['remaining_amount']) : 0.0;
            $row['created_at'] = $row['created_at'] ?? null;
        }
        return $rows;
    }

    public function getGuestLoyaltyTier(int $guestId): ?string {
        $stmt = $this->repo->getDb()->prepare("SELECT loyalty_tier FROM guests WHERE guest_id = :guest_id");
        $stmt->execute([':guest_id' => $guestId]);
        return $stmt->fetchColumn() ?: null;
    }

    public function syncFromReservations(): int {
        $db = $this->repo->getDb();
        
        // Get guests from reservations
        $sql = "SELECT DISTINCT 
                  g.guest_id as reservation_guest_id,
                  g.first_name,
                  g.last_name, 
                  g.email,
                  g.phone as first_phone,
                  g.created_at
                FROM reservations.guests g
                WHERE g.email IS NOT NULL";
                
        $stmt = $db->query($sql);
        $reservationGuests = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $synced = 0;
        foreach ($reservationGuests as $rg) {
            // Check if guest exists
            $exists = $db->prepare("SELECT guest_id FROM guests WHERE email = ?");
            $exists->execute([$rg['email']]);
            
            if (!$exists->fetch()) {
                // Add new guest
                $data = [
                    'first_name' => $rg['first_name'],
                    'last_name' => $rg['last_name'],
                    'email' => $rg['email'],
                    'first_phone' => $rg['first_phone'],
                    'source' => 'reservation',
                    'loyalty_tier' => 'bronze',
                    'auto_loyalty_tier' => true
                ];
                
                try {
                    $this->createGuest($data);
                    $synced++;
                } catch (\Exception $e) {
                    // Log error but continue with next guest
                    error_log("Failed to sync guest {$rg['email']}: " . $e->getMessage());
                }
            }
        }
        
        return $synced;
    }
}
