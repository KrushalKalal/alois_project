<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessUnitMaster extends Model
{
    use HasFactory;

    protected $fillable = ['unit', 'company_id', 'unit_status'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(CompanyMaster::class);
    }

    public function scopeTemporary($query)
    {
        return $query->where('unit_status', 0);
    }

    public function scopePermanent($query)
    {
        return $query->where('unit_status', 1);
    }
}
