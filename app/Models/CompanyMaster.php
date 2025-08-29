<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompanyMaster extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'region', 'to_emails', 'cc_emails'];

    protected $casts = [
        'to_emails' => 'array',
        'cc_emails' => 'array',
    ];

    public function branches(): HasMany
    {
        return $this->hasMany(BranchMaster::class);
    }

    public function businessUnits(): HasMany
    {
        return $this->hasMany(BusinessUnitMaster::class, 'company_id');
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class, 'company_id');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'company_id');
    }
}
