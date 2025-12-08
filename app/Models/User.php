<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class User extends Model
{
    protected string $table = 'tbl_users';
    protected string $primaryKey = 'user_id';

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return $r ?: null;
    }

    public function saveResetToken(int $userId, string $token, int $expirySeconds = 3600): bool
    {
        $expiry = date('Y-m-d H:i:s', time() + $expirySeconds);
        $stmt = $this->db->prepare("UPDATE {$this->table} SET reset_token = :token, token_expiry = :expiry WHERE user_id = :uid");
        return (bool)$stmt->execute(['token' => $token, 'expiry' => $expiry, 'uid' => $userId]);
    }

    public function findByResetToken(string $token): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE reset_token = :token LIMIT 1");
        $stmt->execute(['token' => $token]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$r) return null;
        if (!empty($r['token_expiry']) && strtotime($r['token_expiry']) < time()) {
            return null;
        }
        return $r;
    }

    public function all(): array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY date_created DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function activate(int $userId): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET is_active = 1 WHERE user_id = :uid");
        return (bool)$stmt->execute(['uid' => $userId]);
    }

    public function createWithRole(array $data, int $roleId = 2): int
    {
        $data['role_id'] = $roleId;
        $data['is_active'] = $data['is_active'] ?? 0;
        return $this->create($data);
    }

    public function findByInvitationToken(string $token): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE invitation_token = :token LIMIT 1");
        $stmt->execute(['token' => $token]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return $r ?: null;
    }

    public function completeEmployeeSetup(int $userId, string $password): bool
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE {$this->table} SET password = :password, is_active = 1, invitation_token = NULL, token_expiry = NULL WHERE user_id = :user_id");
        return (bool)$stmt->execute([
            'password' => $hashedPassword,
            'user_id' => $userId
        ]);
    }
}
