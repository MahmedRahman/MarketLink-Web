<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Organization extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($organization) {
            if (empty($organization->slug)) {
                $organization->slug = Str::slug($organization->name);
            }
        });
    }

    /**
     * Get the users that belong to this organization.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the active subscription for this organization.
     */
    public function subscription(): HasMany
    {
        return $this->hasMany(Subscription::class)->latest();
    }

    /**
     * Get the active subscription for this organization.
     */
    public function activeSubscription()
    {
        return $this->subscription()
            ->whereIn('status', ['trial', 'active'])
            ->latest()
            ->first();
    }

    /**
     * Check if organization is on trial.
     */
    public function isOnTrial(): bool
    {
        $subscription = $this->activeSubscription();
        return $subscription && $subscription->status === 'trial' && $subscription->trial_ends_at && $subscription->trial_ends_at->isFuture();
    }

    /**
     * Check if organization trial has expired.
     */
    public function trialExpired(): bool
    {
        $subscription = $this->activeSubscription();
        return $subscription && $subscription->status === 'trial' && $subscription->trial_ends_at && $subscription->trial_ends_at->isPast();
    }

    /**
     * Check if organization has active subscription.
     */
    public function hasActiveSubscription(): bool
    {
        $subscription = $this->activeSubscription();
        return $subscription && $subscription->status === 'active' && $subscription->ends_at && $subscription->ends_at->isFuture();
    }

    /**
     * Get subscription requests for this organization.
     */
    public function subscriptionRequests(): HasMany
    {
        return $this->hasMany(SubscriptionRequest::class);
    }

    /**
     * Get the clients that belong to this organization.
     */
    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    /**
     * Get the employees that belong to this organization.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Get the projects that belong to this organization.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
