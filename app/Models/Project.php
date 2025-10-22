<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    protected $fillable = [
        'client_id',
        'business_name',
        'business_description',
        'website_url',
        'facebook_url',
        'instagram_url',
        'twitter_url',
        'linkedin_url',
        'youtube_url',
        'tiktok_url',
        'whatsapp_number',
        'phone_number',
        'email',
        'address',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
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
}
