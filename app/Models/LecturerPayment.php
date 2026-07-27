<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LecturerPayment extends Model
{
    protected $fillable = [
        'lecturer_id', 'type_of_lecture', 'date', 'total_hours',
        'payment_type', 'rate_amount', 'total_payment',
        'completed_payment', 'paid_date', 'invoice_no', 'remarks',
    ];

    public function lecturer()
    {
        return $this->belongsTo(Lecturer::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'lecturer_payment_course');
    }

    public function getPaymentDueAttribute()
    {
        return $this->total_payment - $this->completed_payment;
    }
}