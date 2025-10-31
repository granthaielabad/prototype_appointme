<?php
namespace App\Models;

use App\Core\Model;

class Inquiry extends Model
{
    protected string $table = 'tbl_inquiries';
    protected string $primaryKey = 'inquiry_id';
        
    protected array $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'message',
        'status',
        'created_at'
    ];
}
