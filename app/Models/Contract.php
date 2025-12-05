<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contract extends Model
{
    protected $fillable = [
        'organization_id',
        'employee_id',
        'payment_type',
        'agreed_amount',
        'start_date',
        'end_date',
        'notes',
        'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'agreed_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getPaymentTypeLabelAttribute()
    {
        return match($this->payment_type) {
            'hourly' => 'بالوقت',
            'project' => 'بالمشروع',
            'piece' => 'بالقطعة',
            default => 'غير محدد'
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'active' => 'نشط',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            default => 'غير محدد'
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'active' => 'green',
            'completed' => 'blue',
            'cancelled' => 'red',
            default => 'gray'
        };
    }
}
