<?php
namespace CRM\Lib;

class Campaign {
    public ?int $id = null;
    public string $name = '';
    public ?string $description = null;
    public string $type = 'email';
    public string $target_audience = '';
    public string $message = '';
    public string $status = 'draft';
    public ?string $schedule = null;
    public int $sent_count = 0;
    public float $open_rate = 0.0;
    public float $click_rate = 0.0;
    public ?string $created_by_user = null;
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
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'target_audience' => $this->target_audience,
            'message' => $this->message,
            'status' => $this->status,
            'schedule' => $this->schedule,
            'sent_count' => $this->sent_count,
            'open_rate' => $this->open_rate,
            'click_rate' => $this->click_rate,
            'created_by_user' => $this->created_by_user,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
