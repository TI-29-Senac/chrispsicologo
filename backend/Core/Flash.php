<?php

namespace App\Psico\Core;

class Flash {
    

    public static function set(string $key, $value) {
        if (!isset($_SESSION)) {
            session_start();
        }

        $_SESSION['_flash_data'][$key] = $value;
    }


    public static function get(string $key) {
        if (!isset($_SESSION)) {
             // session_start(); 
        }
        
        if (isset($_SESSION['_flash_data'][$key])) {
            $value = $_SESSION['_flash_data'][$key];
            unset($_SESSION['_flash_data'][$key]);
            return $value;
        }
        return null;
    }
}