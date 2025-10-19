<?php
namespace CRM\Lib;

class Complaint {
    public ?int $id = null;
    public ?int $guest_id = null;
    public string $guest_name = '';
    public string $comment = '';
    public string $type = 'complaint'; // Now supports any string (service, facility, staff, other, etc.)
    public string $status = 'pending';
    public ?string $reply = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    public function __construct(array $data = []) {
        foreach ($data as $k => $v) {
            if (property_exists($this, $k)) $this->{$k} = $v;
        }
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'guest_id' => $this->guest_id,
            'guest_name' => $this->guest_name,
            'comment' => $this->comment,
            'type' => $this->type,
            'status' => $this->status,
            'reply' => $this->reply,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
