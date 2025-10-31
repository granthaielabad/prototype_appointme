<?php
namespace App\Models;

use App\Core\Model;

class Invoice extends Model
{
    protected string $table = 'tbl_invoices';
    protected string $primaryKey = 'invoice_id';
}
