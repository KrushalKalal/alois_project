<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultant extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'address',
        'state',
        'city',
        'country',
        'phone1',
        'phone2',
        'email1',
        'email2',
        'aadhaar',
        'pan',
        'po_copy',
        'extra_doc',
        'status'
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}