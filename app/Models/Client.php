<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_code',
        'client_name',
        'company_id',
        'client_status',
        'loaded_cost',
        'qualify_days',
        'phone',
        'email',
        'status',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(CompanyMaster::class, 'company_id');
    }

    public function scopeTemporary($query)
    {
        return $query->where('client_status', 0);
    }

    public function scopePermanent($query)
    {
        return $query->where('client_status', 1);
    }
}