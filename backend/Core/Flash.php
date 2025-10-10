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
    public static function getFlash()
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
       
        $tipo = $flash['tipo'] ?? 'info';
        $mensagem = $flash['mensagem'] ?? '';
       
        $alertClass = '';
        switch ($tipo) {
            case 'success':
                $alertClass = 'w3-panel w3-green w3-display-container';
                break;
            case 'error':
                $alertClass = 'w3-panel w3-red w3-display-container';
                break;
            default:
                $alertClass = 'w3-panel w3-blue w3-display-container';
                break;
        }
 
        return "<div class=\"{$alertClass}\">
                  <span onclick=\"this.parentElement.style.display='none'\"
                  class=\"w3-button w3-large w3-display-topright\">&times;</span>
                  <p>{$mensagem}</p>
                </div>";
    }
    return '';
}
}