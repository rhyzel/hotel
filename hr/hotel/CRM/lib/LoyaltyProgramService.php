<?php
namespace CRM\Lib;

class LoyaltyProgramService {
    private LoyaltyProgramRepository $repo;

    public function __construct(LoyaltyProgramRepository $repo) {
        $this->repo = $repo;
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
        // discount_rate is optional, handled by repository
        return $this->repo->create($data);
    }

    public function updateProgram(array $data): bool {
        return $this->repo->update($data);
    }

    public function deleteProgram(int $id): bool {
        return $this->repo->delete($id);
    }

    // Add this to allow API to sync member counts
    public function syncMembersCount(): void {
        $this->repo->syncMembersCount();
    }

    public function getLoungeOrderPoints($guest_id = null): float {
        return $this->repo->getLoungeOrderPoints($guest_id);
    }
}
