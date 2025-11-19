<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class User extends Model
{
    protected string $table = 'tbl_users';
    protected string $primaryKey = 'user_id';

    /*
        Find user by email.
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $r = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $r ?: null;
    }

    /*
        Save a reset token and expiry for password reset flows
     */
    public function saveResetToken(int $userId, string $token, int $expirySeconds = 3600): bool
    {
        $expiry = date('Y-m-d H:i:s', time() + $expirySeconds);
        $stmt = $this->db->prepare("UPDATE {$this->table} SET reset_token = :token, token_expiry = :expiry WHERE user_id = :uid");
        return (bool)$stmt->execute(['token' => $token, 'expiry' => $expiry, 'uid' => $userId]);
    }

    /*
        Find user by valid (non-expired) reset token.
     */ 
    public function findByResetToken(string $token): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE reset_token = :token LIMIT 1");
        $stmt->execute(['token' => $token]);
        $r = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$r) return null;
        if (!empty($r['token_expiry']) && strtotime($r['token_expiry']) < time()) {
            return null; // expired
        }
        return $r;
    }

    /*
        Retrieve all users (optional, for admin list).
     */
    public function all(): array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
