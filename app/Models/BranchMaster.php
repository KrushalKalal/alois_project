<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchMaster extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'company_id', 'branch_status'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(CompanyMaster::class);
    }

    public function scopeTemporary($query)
    {
        return $query->where('branch_status', 0);
    }

    public function scopePermanent($query)
    {
        return $query->where('branch_status', 1);
    }
}