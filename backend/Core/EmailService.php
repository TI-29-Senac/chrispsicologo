<?php
namespace App\Psico\Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Psico\Database\Config; // Para buscar as configurações

class EmailService {
    private $mailer;
    private $config;

    public function __construct() {
        $this->config = Config::get()['mailer']; // Pega as configs do mailer
        $this->mailer = new PHPMailer(true); // Habilita exceções

        try {
            // Configurações do Servidor
            // $this->mailer->SMTPDebug = 2; // Descomente para debug detalhado
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
                 $this->mailer->SMTPSecure = false; // Desabilita criptografia se não for tls/ssl
                 $this->mailer->SMTPAutoTLS = false; // Necessário se a porta não for 587/465
            }
            $this->mailer->Port       = $this->config['port'];
            $this->mailer->CharSet    = PHPMailer::CHARSET_UTF8; // Para acentos

            // Remetente
            $this->mailer->setFrom($this->config['from_address'], $this->config['from_name']);

        } catch (Exception $e) {
            // Logar o erro de configuração inicial pode ser útil
            error_log("Erro ao configurar PHPMailer: {$this->mailer->ErrorInfo}");
            // Você pode querer lançar uma exceção aqui ou lidar de outra forma
        }
    }

    /**
     * Envia um email.
     *
     * @param string $paraEmail Email do destinatário.
     * @param string $paraNome Nome do destinatário (opcional).
     * @param string $assunto Assunto do email.
     * @param string $corpoHtml Corpo do email em HTML.
     * @param string $corpoTexto Corpo alternativo em texto puro (opcional).
     * @return bool True se enviado com sucesso, false caso contrário.
     */
    public function enviarEmail(string $paraEmail, string $paraNome = '', string $assunto, string $corpoHtml, string $corpoTexto = ''): bool {
        try {
            // Destinatários
            $this->mailer->addAddress($paraEmail, $paraNome);

            // Conteúdo
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $assunto;
            $this->mailer->Body    = $corpoHtml;
            $this->mailer->AltBody = $corpoTexto ?: strip_tags($corpoHtml); // Usa texto puro ou remove tags HTML

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Erro ao enviar email para {$paraEmail}: {$this->mailer->ErrorInfo}");
            return false;
        } finally {
            // Limpa os destinatários para o próximo envio
            $this->mailer->clearAddresses();
            // $this->mailer->clearAttachments(); // Se usar anexos
        }
    }
}