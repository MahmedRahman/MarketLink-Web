<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'company',
        'address',
        'notes',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

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

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
