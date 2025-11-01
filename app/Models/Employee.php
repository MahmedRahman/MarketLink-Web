<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Employee extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
        'role',
        'status',
        'organization_id',
        'instapay_number',
        'vodafone_cash_number',
        'facebook_url',
        'linkedin_url',
        'portfolio_url',
        'notes',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getRoleBadgeAttribute()
    {
        return match($this->role) {
            'content_writer' => 'كاتب محتوى',
            'ad_manager' => 'إدارة إعلانات',
            'designer' => 'مصمم',
            'video_editor' => 'مصمم فيديوهات',
            'page_manager' => 'إدارة الصفحة',
            'account_manager' => 'أكونت منجر',
            'monitor' => 'مونتير',
            'media_buyer' => 'ميديا بايرز',
            default => 'غير محدد'
        };
    }

    public function getRoleColorAttribute()
    {
        return match($this->role) {
            'content_writer' => 'blue',
            'ad_manager' => 'green',
            'designer' => 'purple',
            'video_editor' => 'red',
            'page_manager' => 'yellow',
            'account_manager' => 'indigo',
            'monitor' => 'pink',
            'media_buyer' => 'teal',
            default => 'gray'
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'pending' => 'في الانتظار',
            default => 'غير محدد'
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'active' => 'green',
            'inactive' => 'red',
            'pending' => 'yellow',
            default => 'gray'
        };
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
