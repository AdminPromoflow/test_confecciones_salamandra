<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Clase PulseCsrf
 */
class PulseCsrf
{
    private const TOKEN_LENGTH = 128;
    private const TOKEN_EXPIRATION_TIME = 900; // 15 minutos

    public static function generateToken()
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = self::_generateRandomToken();
            session_regenerate_id();
            $_SESSION['csrf_token_expiration'] = time() + self::TOKEN_EXPIRATION_TIME;
        }

        return $_SESSION['csrf_token'];
    }

    public static function verifyToken($requestData, $sessionData)
    {
        self::_ensureTokenExists($requestData, $sessionData);

        $tokenExpiration = $sessionData['csrf_token_expiration'];
        if (time() < $tokenExpiration) {
            return hash_equals($requestData['csrf_token'], $sessionData['csrf_token']);
        } else {
            self::generateToken();
            // throw new Exception("CSRF token expired");
        }
    }

    private static function _ensureTokenExists($requestData, $sessionData)
    {
        if (!isset($requestData['csrf_token'], $sessionData['csrf_token'])) {
            throw new PulseErrorHandler("CSRF token missing");
        }
    }

    private static function _generateRandomToken()
    {
        return bin2hex(random_bytes(self::TOKEN_LENGTH));
    }
}
