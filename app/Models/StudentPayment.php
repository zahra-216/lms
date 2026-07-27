<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentPayment extends Model
{
    protected $fillable = ['student_id', 'type_of_payment', 'amount', 'date', 'invoice_no', 'remarks'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}