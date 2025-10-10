<?php
namespace CRM\Lib;

class Guest {
    public ?int $guest_id = null;
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $first_phone = '';
    public ?string $second_phone = null;
    public string $status = 'active';
    public ?string $created_at = null;
    public ?string $updated_at = null;

    public function __construct(array $data = []) {
        foreach ($data as $k => $v) {
            if (property_exists($this, $k)) $this->{$k} = $v;
        }
    }

    public function toArray(): array {
        return [
            'guest_id' => $this->guest_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'first_phone' => $this->first_phone,
            'second_phone' => $this->second_phone,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
