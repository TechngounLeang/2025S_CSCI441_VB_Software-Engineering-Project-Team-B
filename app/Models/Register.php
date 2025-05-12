<?php
// Written & debugged by: Tech Ngoun Leang & Ratanakvesal Thong
// Tested by: Tech Ngoun Leang
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Register extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'cash_balance',
        'counted_balance',
        'balance_difference',
        'opened_at',
        'opened_by',
        'closed_at',
        'closed_by',
        'transaction_count',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'cash_balance' => 'decimal:2',
        'counted_balance' => 'decimal:2',
        'balance_difference' => 'decimal:2',
    ];

    // Get all orders from this register
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Get user who opened the register
    public function openedBy()
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    // Get user who closed the register
    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }
}