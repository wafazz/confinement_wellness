<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionRule extends Model
{
    protected $fillable = [
        'service_category', 'work_days', 'service_type', 'description', 'price',
        'therapist_commission', 'therapist_commission_type',
        'leader_override', 'leader_override_type',
        'affiliate_commission', 'affiliate_commission_type',
        'customer_referral_points',
        'points_per_job', 'requires_review', 'status',
    ];

    protected function casts(): array
    {
        return [
            'therapist_commission' => 'decimal:2',
            'leader_override' => 'decimal:2',
            'affiliate_commission' => 'decimal:2',
            'price' => 'decimal:2',
            'requires_review' => 'boolean',
        ];
    }

    public function getTherapistAmount($price): float
    {
        if ($this->therapist_commission_type === 'percentage') {
            return round(($this->therapist_commission / 100) * $price, 2);
        }
        return (float) $this->therapist_commission;
    }

    public function getLeaderAmount($price): float
    {
        if ($this->leader_override_type === 'percentage') {
            return round(($this->leader_override / 100) * $price, 2);
        }
        return (float) $this->leader_override;
    }

    public function getAffiliateAmount($price): float
    {
        if ($this->affiliate_commission_type === 'percentage') {
            return round(($this->affiliate_commission / 100) * $price, 2);
        }
        return (float) $this->affiliate_commission;
    }
}
