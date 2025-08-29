<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'emp_id',
        'name',
        'company_id',
        'email',
        'phone',
        'role',
        'checker_id',
        'is_self_checker',
        'designation',
        'status',
    ];

    protected $casts = [
        'is_self_checker' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(CompanyMaster::class, 'company_id');
    }


    public function checker(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'checker_id');
    }
}