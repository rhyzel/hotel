<?php
namespace CRM\Lib;

class CampaignService {
    private CampaignRepository $repo;

    public function __construct(CampaignRepository $repo) {
        $this->repo = $repo;
    }

    public function listCampaigns(): array {
        return $this->repo->getAll();
    }

    public function getCampaign(int $id): ?Campaign {
        return $this->repo->getById($id);
    }

    public function createCampaign(array $data): Campaign {
        return $this->repo->create($data);
    }

    public function updateCampaign(array $data): bool {
        return $this->repo->update($data);
    }

    public function deleteCampaign(int $id): bool {
        return $this->repo->delete($id);
    }
}
