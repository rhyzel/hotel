<?php
namespace CRM\Lib;

class LoyaltyProgramService {
    private LoyaltyProgramRepository $repo;
    private ?GuestService $guestService = null;

    public function __construct(LoyaltyProgramRepository $repo, ?GuestService $guestService = null) {
        $this->repo = $repo;
        $this->guestService = $guestService;
    }

    // Use GuestService for loyalty tier validation
    public function getValidTiers(): array {
        if ($this->guestService) {
            // Use GuestService's getValidTiers method (make it public)
            return $this->guestService->getValidTiers();
        }
        // fallback: query directly
        $stmt = $this->repo->getDb()->query("SELECT DISTINCT tier FROM loyalty_programs WHERE status = 'active'");
        return array_map('strtolower', $stmt->fetchAll(\PDO::FETCH_COLUMN));
    }

    public function listPrograms(): array {
        return $this->repo->getAll();
    }

    public function getProgram(int $id): ?LoyaltyProgram {
        return $this->repo->getById($id);
    }

    public function createProgram(array $data): LoyaltyProgram {
        if (empty($data['name']) || empty($data['tier']) || !isset($data['points_rate'])) {
            throw new \InvalidArgumentException('Missing required fields: name, tier, points_rate');
        }
        // Validate loyalty tier using GuestService
        $validTiers = $this->getValidTiers();
        $tier = strtolower($data['tier']);
        if (!in_array($tier, $validTiers)) {
            throw new \InvalidArgumentException('Invalid loyalty tier. Allowed: ' . implode(', ', $validTiers));
        }
        // Validate loyalty tier using GuestService
        if (isset($data['guest_id'])) {
            $guestTier = $this->guestService->getGuestLoyaltyTier($data['guest_id']);
            if ($guestTier !== null && !in_array(strtolower($guestTier), $this->getValidTiers())) {
                throw new \InvalidArgumentException('Guest loyalty tier is invalid.');
            }
        }
        $data['points_earned'] = $data['points_earned'] ?? 0.0; // Set default value
        $data['points_redeemed'] = $data['points_redeemed'] ?? 0; // Set default value
        // discount_rate is optional, handled by repository
        return $this->repo->create($data);
    }

    public function updateProgram(array $data): bool {
        if (isset($data['tier'])) {
            $validTiers = $this->getValidTiers();
            $tier = strtolower($data['tier']);
            if (!in_array($tier, $validTiers)) {
                throw new \InvalidArgumentException('Invalid loyalty tier. Allowed: ' . implode(', ', $validTiers));
            }
        }
        return $this->repo->update($data);
    }

    public function deleteProgram(int $id): bool {
        return $this->repo->delete($id);
    }

    // Add this to allow API to sync member counts
    public function syncMembersCount(): void {
        $this->repo->syncMembersCount();
    }

    // Get count of guests per loyalty tier
    public function getTierCounts(): array {
        $db = $this->repo->getDb();
        $stmt = $db->query("SELECT loyalty_tier, COUNT(*) AS count FROM guests GROUP BY loyalty_tier");
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $counts = [];
        foreach ($rows as $row) {
            $tier = strtolower($row['loyalty_tier']);
            $counts[$tier] = (int)$row['count'];
        }
        return $counts;
    }

    // Get points earned per guest for a tier
    public function getPointsEarnedPerGuestByTier(string $tier): array {
        return $this->repo->getPointsEarnedPerGuestByTier($tier);
    }
}