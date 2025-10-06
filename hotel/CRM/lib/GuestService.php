<?php
namespace CRM\Lib;

class GuestService {
    private GuestRepository $repo;

    public function __construct(GuestRepository $repo) {
        $this->repo = $repo;
    }

    public function listGuests(string $search = ''): array {
        return $this->repo->getAll($search);
    }

    public function createGuest(array $data): array {
        // basic validation
        if (empty($data['first_name']) || empty($data['last_name']) || empty($data['email']) || empty($data['first_phone'])) {
            throw new \InvalidArgumentException('Missing required fields: first_name, last_name, email, first_phone');
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email');
        }
        // ensure unique email (left to repository or DB unique constraint)
        return $this->repo->create($data);
    }

    public function updateGuest(array $data): bool {
        return $this->repo->update($data);
    }

    public function deleteGuest(int $id): bool {
        return $this->repo->delete($id);
    }

    public function getLoungeOrderHistory(int $guest_id): array {
        return $this->repo->getLoungeOrdersByGuest($guest_id);
    }

    public function getGuestLoungeOrders(int $guest_id): array {
        return $this->repo->getLoungeOrdersByGuest($guest_id);
    }

    public function getGuestGiftshopSales(int $guest_id): array {
        return $this->repo->getGiftshopSalesByGuest($guest_id);
    }

    public function getGuestRoomDiningOrders(int $guest_id): array {
        return $this->repo->getRoomDiningOrdersByGuest($guest_id);
    }

    public function getGuestRestaurantOrders(int $guest_id): array {
        return $this->repo->getRestaurantOrdersByGuest($guest_id);
    }

    public function getGuestAllOrders(int $guest_id): array {
        return $this->repo->getAllPurchaseHistoryByGuest($guest_id);
    }
}
