<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class OTP extends Model
{
    protected string $table = 'tbl_otps';
    protected string $primaryKey = 'otp_id';

    /**
     * Create a new OTP entry.
     * Uses Asia/Manila timezone consistently for created_at and expiry.
     */
    public function createOtp(?int $userId, string $code, int $expirySeconds = 600, ?string $email = null): int
    {
        // Ensure consistent timezone
        date_default_timezone_set('Asia/Manila');

        // Current and expiry times in Manila timezone
        $now = new \DateTime('now', new \DateTimeZone('Asia/Manila'));
        $expiry = clone $now;
        $expiry->modify("+{$expirySeconds} seconds");

        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (user_id, email, code, expiry, is_used, created_at)
            VALUES (:uid, LOWER(TRIM(:email)), :code, :expiry, 0, :created)
        ");

        $stmt->execute([
            'uid'     => $userId,
            'email'   => $email,
            'code'    => str_pad($code, 6, '0', STR_PAD_LEFT),
            'expiry'  => $expiry->format('Y-m-d H:i:s'),
            'created' => $now->format('Y-m-d H:i:s'),
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Retrieve a valid, unexpired OTP by code (and optionally user or email).
     */
    public function getValidOtp(string $code, ?int $userId = null, ?string $email = null): ?array
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE code = :code
                AND is_used = 0
                AND expiry >= NOW()";

        $params = ['code' => str_pad($code, 6, '0', STR_PAD_LEFT)];

        if ($userId !== null) {
            $sql .= " AND user_id = :uid";
            $params['uid'] = $userId;
        }

        if ($email !== null) {
            $sql .= " AND LOWER(TRIM(email)) = LOWER(TRIM(:email))";
            $params['email'] = $email;
        }

        $sql .= " ORDER BY created_at DESC LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /**
     * Mark an OTP as used.
     */
    public function markUsed(int $otpId): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET is_used = 1 WHERE otp_id = :id");
        return $stmt->execute(['id' => $otpId]);
    }

    /**
     * Count OTPs created within a certain time window (e.g., past hour).
     */
    public function countRecentSendsByEmail(string $email, int $secondsWindow = 3600): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM {$this->table} 
            WHERE LOWER(TRIM(email)) = LOWER(TRIM(:email))
              AND created_at >= (NOW() - INTERVAL :seconds SECOND)
        ");
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':seconds', $secondsWindow, PDO::PARAM_INT);
        $stmt->execute();

        return (int)$stmt->fetchColumn();
    }

    /**
     * Fetch the most recent OTP entry for a given email.
     */
    public function lastOtpByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE LOWER(TRIM(email)) = LOWER(TRIM(:email))
            ORDER BY created_at DESC 
            LIMIT 1
        ");
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }
}
