<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectRevenue extends Model
{
    protected $fillable = [
        'project_id',
        'title',
        'description',
        'amount',
        'currency',
        'revenue_date',
        'payment_method',
        'payment_reference',
        'status',
        'invoice_number',
        'invoice_date',
        'notes'
    ];

    protected $attributes = [
        'currency' => 'EGP',
    ];

    protected $casts = [
        'revenue_date' => 'date',
        'invoice_date' => 'date',
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2) . ' جنيه';
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => 'في الانتظار',
            'received' => 'تم الاستلام',
            'cancelled' => 'ملغي',
            default => 'غير محدد'
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'received' => 'green',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    public function getPaymentMethodBadgeAttribute()
    {
        return match($this->payment_method) {
            'cash' => 'نقدي',
            'bank_transfer' => 'تحويل بنكي',
            'credit_card' => 'بطاقة ائتمان',
            'check' => 'شيك',
            'other' => 'أخرى',
            default => 'غير محدد'
        };
    }
}