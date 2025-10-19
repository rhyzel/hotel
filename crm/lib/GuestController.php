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
            case 'billing':
                return ['success' => true, 'data' => $this->service->getGuestPurchaseHistory($guest_id)];
            default:
                // Always return data array for unknown types
                return ['success' => true, 'data' => []];
        }
    }

    public function create(array $input, LoyaltyProgramService $loyaltyService) {
        // Validate loyalty tier
        if (isset($input['loyalty_tier'])) {
            $validTiers = $loyaltyService->getValidTiers();
            if (!in_array(strtolower($input['loyalty_tier']), $validTiers)) {
                return ['success' => false, 'error' => 'Invalid loyalty tier. Allowed: ' . implode(', ', $validTiers)];
            }
        }
        $newGuest = $this->service->createGuest($input);
        $loyaltyService->syncMembersCount();
        return ['success' => true, 'data' => $newGuest];
    }

    public function update(array $input, LoyaltyProgramService $loyaltyService) {
        // Validate loyalty tier
        if (isset($input['loyalty_tier'])) {
            $validTiers = $loyaltyService->getValidTiers();
            if (!in_array(strtolower($input['loyalty_tier']), $validTiers)) {
                return ['success' => false, 'error' => 'Invalid loyalty tier. Allowed: ' . implode(', ', $validTiers)];
            }
        }
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
