<?php
namespace App\Psico\Core;

use App\Psico\Core\EmailService;

class EmailNotificar {
    private $emailService;

    public function __construct() {
        // Reutiliza o serviço de e-mail já configurado no projeto
        $this->emailService = new EmailService();
    }

    public function notificarRecebimento(string $emailUsuario, string $nomeUsuario): bool {
        $assunto = "Recebemos seu contato - Chris Psicologia";
        
        $corpoHtml = "
            <div style='font-family: Arial, sans-serif; color: #5D6D68;'>
                <h2>Olá, " . htmlspecialchars($nomeUsuario) . "!</h2>
                <p>Obrigado por entrar em contato conosco.</p>
                <p>Recebemos sua mensagem e nossa equipe irá analisá-la em breve.</p>
                <p>Por favor, aguarde, entraremos em contato o mais rápido possível.</p>
                <hr>
                <p><em>Atenciosamente,<br>Equipe Chris Psicologia</em></p>
            </div>
        ";

        $corpoTexto = "Olá, " . $nomeUsuario . "!\n\nObrigado por entrar em contato. Recebemos sua mensagem e retornaremos em breve.\n\nAtenciosamente,\nEquipe Chris Psicologia";

        // Envia o e-mail para o USUÁRIO (remetente do formulário)
        return $this->emailService->enviarEmail(
            $emailUsuario, 
            $nomeUsuario, 
            $assunto, 
            $corpoHtml, 
            $corpoTexto
        );
    }
}