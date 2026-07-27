<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentPlan extends Model
{
    protected $fillable = ['student_id', 'total_installments', 'total_fee'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}