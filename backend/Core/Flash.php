<?php
namespace App\Psico\Core;

class Flash {

    private static $sessionKey = 'flash_message';

    /**
     * Define uma nova mensagem flash.
     */
    public static function set(string $type, string $message) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION[self::$sessionKey] = [
            'tipo' => $type,
            'mensagem' => $message
        ];
    }

    /**
     * Recupera a mensagem flash e a remove da sessão.
     * @return array|null
     */
    public static function get() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION[self::$sessionKey])) {
            $flash = $_SESSION[self::$sessionKey];
            unset($_SESSION[self::$sessionKey]);
            return $flash;
        }
        return null;
    }

    /**
     * Renderiza a mensagem flash no formato de painel do W3.CSS (se existir).
     * @return string
     */
    public static function getFlash()
    {
        $flash = self::get(); 

        if ($flash) {
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