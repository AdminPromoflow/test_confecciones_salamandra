<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Clase Pulse Session
 */
class PulseSessions
{
    public function setUserData($name, $data) {
        $_SESSION[$name] = $data;
    }

    public function getUserData($name, $data = null) {
        if (isset($_SESSION[$name])) {
            if (isset($data)) {
              return $_SESSION[$name][$data];
            } else {
                return $_SESSION[$name];
            }
        } else {
            return [];
        }
    }

    public function unsetUserData($name)
    {
      unset($_SESSION[$name]);
    }

    public function sessionDestroy()
    {
        session_destroy();
        setcookie(session_name(), '', time() - 3600, '/');
    }

    public function setFlashMessage($type, $message)
    {
        $_SESSION['flash_type'] = $type;
        $_SESSION['flash_message'] = $message;
    }

    public function getFlashType($type)
    {
        if (isset($_SESSION['flash_type']) && $_SESSION['flash_type'] == $type) {
            $flash = $_SESSION['flash_type'];
            unset($_SESSION['flash_type']);
        } else {
            return false;
        }

        return $flash;
    }

    public function getFlashContent()
    {
        if (isset($_SESSION['flash_message'])) {
            $flash_message = $_SESSION['flash_message'];
            unset($_SESSION['flash_message']);
        } else {
            return false;
        }

        return $flash_message;
    }
}
