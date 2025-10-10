<?php
namespace CRM\Lib;

class GuestController {
    private GuestService $service;

    public function __construct(GuestService $service) {
        $this->service = $service;
    }

    public function list(array $queryParams) {
        $search = $queryParams['search'] ?? '';
        $guests = $this->service->listGuests($search);
        // ensure loyalty_tier presence
        $guests = array_map(function($g) {
            if (!isset($g['loyalty_tier'])) $g['loyalty_tier'] = 'bronze';
            return $g;
        }, $guests);
        return ['success' => true, 'data' => $guests];
    }

    public function getHistory(int $guest_id, string $type) {
        switch ($type) {
            case 'lounge': return ['success' => true, 'data' => $this->service->getGuestLoungeOrders($guest_id)];
            case 'giftshop': return ['success' => true, 'data' => $this->service->getGuestGiftshopSales($guest_id)];
            case 'room_dining': return ['success' => true, 'data' => $this->service->getGuestRoomDiningOrders($guest_id)];
            case 'restaurant': return ['success' => true, 'data' => $this->service->getGuestRestaurantOrders($guest_id)];
            case 'all': return ['success' => true, 'data' => $this->service->getGuestAllOrders($guest_id)];
            default:
                return ['success' => false, 'error' => 'Invalid history_type'];
        }
    }

    public function create(array $input, LoyaltyProgramService $loyaltyService) {
        $newGuest = $this->service->createGuest($input);
        $loyaltyService->syncMembersCount();
        return ['success' => true, 'data' => $newGuest];
    }

    public function update(array $input, LoyaltyProgramService $loyaltyService) {
        $ok = $this->service->updateGuest($input);
        if ($ok) {
            $loyaltyService->syncMembersCount();
            return ['success' => true, 'message' => 'Guest updated successfully'];
        }
        return ['success' => false, 'error' => 'No fields to update or guest not found'];
    }

    public function delete(int $guestId, LoyaltyProgramService $loyaltyService) {
        $deleted = $this->service->deleteGuest($guestId);
        if ($deleted) {
            $loyaltyService->syncMembersCount();
            return ['success' => true, 'message' => 'Guest deleted successfully'];
        }
        return ['success' => false, 'error' => 'Guest not found'];
    }
}
