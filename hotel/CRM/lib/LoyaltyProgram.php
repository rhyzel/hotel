<?php
namespace CRM\Lib;

class LoyaltyProgram {
    public ?int $id = null;
    public string $name = '';
    public string $tier = '';
    public float $points_rate = 0.0;
    public ?string $benefits = null;
    public ?string $description = null;
    public int $members_count = 0;
    public string $status = 'active';
    public ?string $created_at = null;
    public int $points_redeemed = 0;
    public int $rewards_given = 0;
    public float $revenue_impact = 0.0;
    public float $discount_rate = 0.0;

    public function __construct(array $data = []) {
        foreach ($data as $k => $v) {
            if (property_exists($this, $k)) $this->{$k} = $v;
        }
        if (isset($data['discount_rate'])) $this->discount_rate = (float)$data['discount_rate'];
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'tier' => $this->tier,
            'points_rate' => $this->points_rate,
            'benefits' => $this->benefits,
            'description' => $this->description,
            'members_count' => $this->members_count,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'points_redeemed' => $this->points_redeemed,
            'rewards_given' => $this->rewards_given,
            'revenue_impact' => $this->revenue_impact,
            'discount_rate' => $this->discount_rate
        ];
    }
}
