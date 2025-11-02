<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class OTP extends Model
{
    protected string $table = 'tbl_otps';
    protected string $primaryKey = 'otp_id';

    public function __construct()
    {
        parent::__construct();
    }

    public function createOtp(int $userId = null, string $code, int $expirySeconds = 300): int
    {
        $expiry = date('Y-m-d H:i:s', time() + $expirySeconds);
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (user_id, code, expiry, is_used, created_at) VALUES (:uid, :code, :expiry, 0, NOW())");
        $stmt->execute(['uid' => $userId, 'code' => $code, 'expiry' => $expiry]);
        return (int)$this->db->lastInsertId();
    }

    public function getValidOtp(string $code, int $userId = null): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE code = :code AND is_used = 0 AND expiry >= NOW()";
        $params = ['code' => $code];
        if ($userId !== null) {
            $sql .= " AND user_id = :uid";
            $params['uid'] = $userId;
        }
        $sql .= " LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return $r ?: null;
    }

    public function markUsed(int $otpId): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET is_used = 1 WHERE otp_id = :id");
        return (bool)$stmt->execute(['id' => $otpId]);
    }
}
