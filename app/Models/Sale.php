<?php
// Written & debugged by: Tech Ngoun Leang, Ratanakvesal Thong, Longwei Ngor, Samady Sok
// Tested by: Tech Ngoun Leang
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'register_id',
        'total_price',
        'tax_amount',
        'discount_amount',
        'payment_method',
        'sale_date'
    ];

    // Define relationship with User (cashier)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Define relationship with Register
    public function register()
    {
        return $this->belongsTo(Register::class);
    }

    // Define relationship with Order (if you have one)
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Scope for filtering by date range
    public function scopeDateBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('sale_date', [$startDate, $endDate]);
    }

    // Get sales by payment method
    public static function getSalesByPaymentMethod($startDate = null, $endDate = null)
    {
        $query = self::query();
        
        if ($startDate && $endDate) {
            $query->dateBetween($startDate, $endDate);
        }
        
        return $query->select('payment_method', \DB::raw('SUM(total_price) as total'))
                    ->groupBy('payment_method')
                    ->get();
    }

    // Get daily sales for a given period
    public static function getDailySales($startDate = null, $endDate = null)
    {
        $query = self::query();
        
        if (!$startDate) {
            $startDate = Carbon::now()->subDays(30);
        }
        
        if (!$endDate) {
            $endDate = Carbon::now();
        }
        
        return $query->dateBetween($startDate, $endDate)
                    ->select(\DB::raw('DATE(sale_date) as date'), \DB::raw('SUM(total_price) as total'))
                    ->groupBy(\DB::raw('DATE(sale_date)'))
                    ->orderBy('date')
                    ->get();
    }
}