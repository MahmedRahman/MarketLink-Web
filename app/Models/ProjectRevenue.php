<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

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
        'transfer_image',
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

    protected $appends = ['transfer_image_url'];

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
            'vodafone_cash' => 'فودافون كاش',
            'instapay' => 'انستاباي',
            'paypal' => 'باي بال',
            'western_union' => 'ويسترن يونيون',
            'other' => 'أخرى',
            default => 'غير محدد'
        };
    }

    /**
     * الحصول على رابط صورة التحويل
     */
    public function getTransferImageUrlAttribute(): ?string
    {
        if ($this->transfer_image) {
            // التحقق من وجود الملف أولاً
            if (Storage::disk('public')->exists($this->transfer_image)) {
                // استخدام asset() للحصول على المسار الصحيح بناءً على الطلب الحالي
                // إذا كان request متاحاً، استخدمه، وإلا استخدم config
                if (app()->runningInConsole() || !request()->hasHeader('Host')) {
                    return config('app.url') . '/storage/' . $this->transfer_image;
                }
                return request()->getSchemeAndHttpHost() . '/storage/' . $this->transfer_image;
            }
        }
        return null;
    }
}