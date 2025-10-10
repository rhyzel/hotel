<?php
require_once('../db.php');
header('Content-Type: application/json');

$guest = null;

if(!empty($_POST['guest_id']) || !empty($_POST['guest_name']) || !empty($_POST['order_id'])){
    if(!empty($_POST['guest_id'])){
        $stmt = $conn->prepare("
            SELECT g.guest_id, g.first_name, g.last_name,
                   rm.room_number
            FROM guests g
            LEFT JOIN reservations r ON g.guest_id = r.guest_id
            LEFT JOIN rooms rm ON r.room_id = rm.room_id
            WHERE g.guest_id = ?
            ORDER BY r.status='checked_in' DESC, r.check_in DESC
            LIMIT 1
        ");
        $stmt->execute([$_POST['guest_id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    } elseif(!empty($_POST['order_id'])) {
        $stmt = $conn->prepare("
            SELECT gb.guest_id, gb.guest_name, rm.room_number
            FROM guest_billing gb
            LEFT JOIN reservations r ON gb.guest_id = r.guest_id
            LEFT JOIN rooms rm ON r.room_id = rm.room_id
            WHERE gb.order_id = ?
            ORDER BY r.status='checked_in' DESC, r.check_in DESC
            LIMIT 1
        ");
        $stmt->execute([$_POST['order_id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    } elseif(!empty($_POST['guest_name'])) {
        $stmt = $conn->prepare("
            SELECT g.guest_id, g.first_name, g.last_name,
                   rm.room_number
            FROM guests g
            LEFT JOIN reservations r ON g.guest_id = r.guest_id
            LEFT JOIN rooms rm ON r.room_id = rm.room_id
            WHERE CONCAT(g.first_name,' ',g.last_name) LIKE ?
            ORDER BY r.status='checked_in' DESC, r.check_in DESC
            LIMIT 1
        ");
        $stmt->execute(["%".$_POST['guest_name']."%"]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if($row){
        $guest = [
            'guest_id' => $row['guest_id'],
            'guest_name' => $row['guest_name'] ?? trim($row['first_name'].' '.$row['last_name']),
            'room_number' => $row['room_number'] ?? '-'
        ];
    }
}

echo json_encode(array_merge($guest ?? [], ['success' => $guest ? true : false]));
