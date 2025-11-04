<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'client_id',
        'organization_id',
        'business_name',
        'business_description',
        'website_url',
        'facebook_url',
        'instagram_url',
        'twitter_url',
        'linkedin_url',
        'youtube_url',
        'tiktok_url',
        'status',
        'authorized_persons',
        'project_accounts'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'authorized_persons' => 'array',
        'project_accounts' => 'array',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function revenues(): HasMany
    {
        return $this->hasMany(ProjectRevenue::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(ProjectExpense::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(ProjectFile::class);
    }

    public function monthlyPlans(): HasMany
    {
        return $this->hasMany(MonthlyPlan::class);
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'project_employees')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * الحصول على الموظفين المديرين للمشروع
     */
    public function managers(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'project_employees')
            ->wherePivot('role', 'manager')
            ->withTimestamps();
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

    /**
     * إضافة شخص موثق للمشروع
     */
    public function addAuthorizedPerson($name, $phone)
    {
        $persons = $this->authorized_persons ?? [];
        $persons[] = [
            'name' => $name,
            'phone' => $phone,
            'added_at' => now()->toISOString()
        ];
        $this->authorized_persons = $persons;
        $this->save();
    }

    /**
     * إضافة حساب للمشروع
     */
    public function addProjectAccount($username, $password, $url)
    {
        $accounts = $this->project_accounts ?? [];
        $accounts[] = [
            'username' => $username,
            'password' => $password,
            'url' => $url,
            'added_at' => now()->toISOString()
        ];
        $this->project_accounts = $accounts;
        $this->save();
    }

    /**
     * الحصول على عدد الأشخاص الموثقين
     */
    public function getAuthorizedPersonsCountAttribute()
    {
        return count($this->authorized_persons ?? []);
    }

    /**
     * الحصول على عدد الحسابات
     */
    public function getProjectAccountsCountAttribute()
    {
        return count($this->project_accounts ?? []);
    }
}
