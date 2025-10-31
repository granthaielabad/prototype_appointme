<?php
namespace App\Models;

use App\Core\Model;

class Role extends Model
{
    protected string $table = 'tbl_roles';
    protected string $primaryKey = 'role_id';
}
