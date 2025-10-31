<?php
namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected string $table = 'tbl_users';
    protected string $primaryKey = 'user_id';

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }
}
