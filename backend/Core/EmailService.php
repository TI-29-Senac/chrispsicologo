<?php
namespace App\Psico\Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Psico\Database\Config; 

class EmailService {
    private $mailer;
    private $config;

    public function __construct() {
        $this->config = Config::get()['mailer']; 
        $this->mailer = new PHPMailer(true);

        try {
            $this->mailer->isSMTP();
            $this->mailer->Host       = $this->config['host'];
            $this->mailer->SMTPAuth   = true;
            $this->mailer->Username   = $this->config['username'];
            $this->mailer->Password   = $this->config['password'];
            if ($this->config['encryption'] === 'tls') {
                $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            } elseif ($this->config['encryption'] === 'ssl') {
                $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } else {
                 $this->mailer->SMTPSecure = false;
                 $this->mailer->SMTPAutoTLS = false; 
            }
            $this->mailer->Port       = $this->config['port'];
            $this->mailer->CharSet    = PHPMailer::CHARSET_UTF8;

            // Remetente
            $this->mailer->setFrom($this->config['from_address'], $this->config['from_name']);

        } catch (Exception $e) {
            error_log("Erro ao configurar PHPMailer: {$this->mailer->ErrorInfo}");
        }
    }

    public function enviarEmail(string $paraEmail, string $paraNome, string $assunto, string $corpoHtml, string $corpoTexto = ''): bool {
        try {
            $this->mailer->addAddress($paraEmail, $paraNome);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $assunto;
            $this->mailer->Body    = $corpoHtml;
            $this->mailer->AltBody = $corpoTexto ?: strip_tags($corpoHtml);

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Erro ao enviar email para {$paraEmail}: {$this->mailer->ErrorInfo}");
            return false;
        } finally {
            $this->mailer->clearAddresses();
        }
    }
}