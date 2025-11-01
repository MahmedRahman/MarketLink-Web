<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectExpense extends Model
{
    protected $fillable = [
        'project_id',
        'employee_id',
        'title',
        'description',
        'amount',
        'currency',
        'expense_date',
        'category',
        'payment_method',
        'payment_reference',
        'status',
        'notes'
    ];

    protected $attributes = [
        'currency' => 'EGP',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2) . ' جنيه';
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => 'في الانتظار',
            'paid' => 'تم الدفع',
            'cancelled' => 'ملغي',
            default => 'غير محدد'
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'paid' => 'green',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    public function getCategoryBadgeAttribute()
    {
        return match($this->category) {
            'marketing' => 'تسويق',
            'advertising' => 'إعلانات',
            'design' => 'تصميم',
            'development' => 'تطوير',
            'content' => 'محتوى',
            'tools' => 'أدوات',
            'subscriptions' => 'اشتراكات',
            'other' => 'أخرى',
            default => 'غير محدد'
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
            'other' => 'أخرى',
            default => 'غير محدد'
        };
    }
}