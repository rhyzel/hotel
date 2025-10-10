<?php
namespace CRM\Lib;

class GuestRepository {
    private \PDO $db;

    public function __construct(\PDO $db) {
        $this->db = $db;
    }

    public function getAll(string $search = ''): array {
        $sql = "SELECT * FROM guests";
        $params = [];
        if (!empty($search)) {
            $sql .= " WHERE (CONCAT(IFNULL(first_name,''), ' ', IFNULL(last_name,'')) LIKE :search OR email LIKE :search)";
            $params[':search'] = "%$search%";
        }
        $sql .= " ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $guests = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $guests;
    }

    public function create(array $data): array {
        $fields = ['first_name', 'last_name', 'email', 'first_phone', 'second_phone', 'status', 'created_at'];
        $placeholders = [':first_name', ':last_name', ':email', ':first_phone', ':second_phone', ':status', 'NOW()'];
        $params = [
            ':first_name' => $data['first_name'] ?? '',
            ':last_name' => $data['last_name'] ?? '',
            ':email' => $data['email'],
            ':first_phone' => $data['first_phone'] ?? '',
            ':second_phone' => $data['second_phone'] ?? null,
            ':status' => $data['status'] ?? 'active',
        ];
        $sql = "INSERT INTO guests (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $id = $this->db->lastInsertId();
        $stmt = $this->db->prepare("SELECT * FROM guests WHERE guest_id = :id");
        $stmt->execute([':id' => $id]);
        $new = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $new ?: [];
    }

    public function update(array $data): bool {
        $id = $data['guest_id'] ?? null; if (!$id) return false;
        $update = [];
        $params = [':id' => $id];
        if (isset($data['first_name'])) { $update[] = 'first_name = :first_name'; $params[':first_name'] = $data['first_name']; }
        if (isset($data['last_name'])) { $update[] = 'last_name = :last_name'; $params[':last_name'] = $data['last_name']; }
        if (isset($data['email'])) { $update[] = 'email = :email'; $params[':email'] = $data['email']; }
        if (isset($data['first_phone'])) { $update[] = 'first_phone = :first_phone'; $params[':first_phone'] = $data['first_phone']; }
        if (isset($data['second_phone'])) { $update[] = 'second_phone = :second_phone'; $params[':second_phone'] = $data['second_phone']; }
        if (isset($data['status'])) { $update[] = 'status = :status'; $params[':status'] = $data['status']; }
        if (empty($update)) return false;
        $sql = "UPDATE guests SET " . implode(', ', $update) . " WHERE guest_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return true;
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM guests WHERE guest_id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    public function getLoungeOrdersByGuest(int $guest_id): array {
        $sql = "SELECT * FROM lounge_orders WHERE guest_id = :guest_id ORDER BY order_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':guest_id' => $guest_id]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getGiftshopSalesByGuest(int $guest_id): array {
        $sql = "SELECT * FROM giftshop_sales WHERE guest_id = :guest_id ORDER BY sale_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':guest_id' => $guest_id]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getRoomDiningOrdersByGuest(int $guest_id): array {
        $sql = "SELECT * FROM room_dining_orders WHERE guest_id = :guest_id ORDER BY order_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':guest_id' => $guest_id]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getRestaurantOrdersByGuest(int $guest_id): array {
        $sql = "SELECT * FROM restaurant_orders WHERE guest_id = :guest_id ORDER BY order_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':guest_id' => $guest_id]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getAllPurchaseHistoryByGuest(int $guest_id): array {
        return [
            'lounge' => $this->getLoungeOrdersByGuest($guest_id),
            'giftshop' => $this->getGiftshopSalesByGuest($guest_id),
            'room_dining' => $this->getRoomDiningOrdersByGuest($guest_id),
            'restaurant' => $this->getRestaurantOrdersByGuest($guest_id),
        ];
    }
}
