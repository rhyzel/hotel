<?php
namespace CRM\Lib;

class ComplaintService {
    private ComplaintRepository $repo;

    public function __construct(ComplaintRepository $repo) {
        $this->repo = $repo;
    }

    public function listComplaints(array $filters = []): array {
        return $this->repo->getAll($filters);
    }

    public function getComplaint(int $id): ?Complaint {
        return $this->repo->getById($id);
    }

    public function createComplaint(array $data): Complaint {
        return $this->repo->create($data);
    }

    public function updateComplaint(array $data): bool {
        return $this->repo->update($data);
    }

    public function deleteComplaint(int $id): bool {
        return $this->repo->delete($id);
    }
}
