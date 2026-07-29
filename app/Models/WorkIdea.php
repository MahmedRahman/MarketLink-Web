<?php

namespace App\Models;

use App\Models\Employee;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkIdea extends Model
{
    protected $fillable = [
        'organization_id',
        'title',
        'description',
        'type',
        'status',
        'creator_type',
        'creator_id',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function getCreatorNameAttribute(): ?string
    {
        return match ($this->creator_type) {
            'employee' => Employee::find($this->creator_id)?->name,
            default => User::find($this->creator_id)?->name,
        };
    }
}

