<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class OTP extends Model
{
    protected string $table = 'tbl_otps';
    protected string $primaryKey = 'otp_id';

    public function createOtp(?int $userId, string $code, int $expirySeconds = 600, ?string $email = null): int
    {
        $expiry = date('Y-m-d H:i:s', time() + $expirySeconds);
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (user_id, code, expiry, is_used, email, created_at)
            VALUES (:uid, :code, :expiry, 0, :email, NOW())
        ");
        $stmt->execute([
            'uid'   => $userId,
            'code'  => $code,
            'expiry'=> $expiry,
            'email' => $email
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function getValidOtp(string $code, ?int $userId = null, ?string $email = null): ?array
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE code = :code AND is_used = 0 AND expiry >= NOW()";
        $params = ['code' => $code];

        if ($userId !== null) {
            $sql .= " AND user_id = :uid";
            $params['uid'] = $userId;
        }

        if ($email !== null) {
            $sql .= " AND email = :email";
            $params['email'] = $email;
        }

        $sql .= " ORDER BY created_at DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function markUsed(int $otpId): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET is_used = 1 WHERE otp_id = :id");
        return $stmt->execute(['id' => $otpId]);
    }

    public function countRecentSendsByEmail(string $email, int $secondsWindow = 3600): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM {$this->table} 
            WHERE email = :email 
              AND created_at >= (NOW() - INTERVAL :seconds SECOND)
        ");
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':seconds', $secondsWindow, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function lastOtpByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE email = :email 
            ORDER BY created_at DESC 
            LIMIT 1
        ");
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}
