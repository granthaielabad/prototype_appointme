<?php
namespace App\Models;

use App\Core\Model;

class Payment extends Model
{
    protected string $table = 'tbl_payments';
    protected string $primaryKey = 'payment_id';
}
