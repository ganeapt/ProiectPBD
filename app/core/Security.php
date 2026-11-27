<?php
declare(strict_types=1);

namespace App\Core;

class Security {
    public static function initSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            session_start();
        }
    }

    public static function generateCsrfToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function verifyCsrfToken(?string $token): bool {
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function csrfField(): string {
        $token = self::generateCsrfToken();
        return '<input type="hidden" name="csrf_token" value="' . self::e($token) . '">';
    }

    public static function e(?string $value): string {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public static function checkRateLimit(string $key = 'login_attempts', int $maxAttempts = 5, int $lockoutSeconds = 60): ?int {
        $data = $_SESSION[$key] ?? ['count' => 0, 'locked_until' => 0];
        if ($data['locked_until'] > time()) {
            return $data['locked_until'] - time();
        }
        if ($data['locked_until'] > 0 && $data['locked_until'] <= time()) {
            unset($_SESSION[$key]);
        }
        return null;
    }

    public static function recordFailedAttempt(string $key = 'login_attempts', int $maxAttempts = 5, int $lockoutSeconds = 60): void {
        $data = $_SESSION[$key] ?? ['count' => 0, 'locked_until' => 0];
        $data['count']++;
        if ($data['count'] >= $maxAttempts) {
            $data['locked_until'] = time() + $lockoutSeconds;
        }
        $_SESSION[$key] = $data;
    }

    public static function clearAttempts(string $key = 'login_attempts'): void {
        unset($_SESSION[$key]);
    }
}
