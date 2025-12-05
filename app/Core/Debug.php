<?php
namespace App\Core;

class Debug
{
    private static string $logFile = '';

    public static function init(string $logPath = ''): void
    {
        if (empty($logPath)) {
            $logPath = __DIR__ . '/../../logs';
        }
        
        if (!is_dir($logPath)) {
            mkdir($logPath, 0755, true);
        }
        
        self::$logFile = $logPath . '/debug_' . date('Y-m-d') . '.log';
    }

    public static function log(string $message, string $level = 'INFO'): void
    {
        if (empty(self::$logFile)) {
            self::init();
        }

        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;
        
        error_log($logMessage, 3, self::$logFile);
        
        // Also log to PHP error log
        if (in_array($level, ['ERROR', 'WARNING'])) {
            error_log($logMessage);
        }
    }

    public static function logRequest(): void
    {
        $message = sprintf(
            "REQUEST: %s %s | IP: %s | Headers: %s",
            $_SERVER['REQUEST_METHOD'] ?? 'N/A',
            $_SERVER['REQUEST_URI'] ?? 'N/A',
            $_SERVER['REMOTE_ADDR'] ?? 'N/A',
            json_encode(getallheaders() ?? [])
        );
        self::log($message, 'DEBUG');
    }

    public static function logException(\Throwable $e): void
    {
        $message = sprintf(
            "EXCEPTION: %s | File: %s:%d | Message: %s",
            get_class($e),
            $e->getFile(),
            $e->getLine(),
            $e->getMessage()
        );
        self::log($message, 'ERROR');
    }

    public static function logApiResponse(string $endpoint, array $response, int $statusCode = 200): void
    {
        $message = sprintf(
            "API_RESPONSE: %s | Status: %d | Response: %s",
            $endpoint,
            $statusCode,
            json_encode($response)
        );
        self::log($message, 'DEBUG');
    }
}
