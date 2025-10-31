<?php
namespace App\Models;
use App\Core\Model;
use PDO;

class PasswordReset extends Model {
    protected string $table = 'tbl_password_resets';
    protected string $primaryKey = 'id';
    protected array $fillable = ['email','token','created_at'];

    public function findByToken(string $token): ?array {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE token = :token LIMIT 1");
        $stmt->execute(['token'=>$token]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return $r ?: null;
    }

    public function deleteByEmail(string $email): bool {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE email = :email");
        return $stmt->execute(['email'=>$email]);
    }
}
