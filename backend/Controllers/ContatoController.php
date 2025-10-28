<?php
namespace App\Psico\Controllers;

use App\Psico\Core\EmailService;
use App\Psico\Database\Config; // Para pegar o email de destino

class ContatoController {

    private $emailService;
    private $emailDestino;

    public function __construct() {
        $this->emailService = new EmailService();
        // Pega o email da clínica configurado no .env (usado como 'from_address')
        $this->emailDestino = Config::get()['mailer']['from_address'] ?? 'email_padrao_da_clinica@exemplo.com';
    }

    public function processarFormulario() {
        header('Content-Type: application/json'); // Define que a resposta será JSON

        // 1. Obter e validar os dados do POST
        $nome = trim($_POST['nome'] ?? '');
        $emailRemetente = trim($_POST['email'] ?? '');
        $mensagem = trim($_POST['mensagem'] ?? '');

        $erros = [];
        if (empty($nome)) {
            $erros[] = 'O campo Nome é obrigatório.';
        }
        if (empty($emailRemetente)) {
            $erros[] = 'O campo E-mail é obrigatório.';
        } elseif (!filter_var($emailRemetente, FILTER_VALIDATE_EMAIL)) {
            $erros[] = 'O E-mail fornecido não é válido.';
        }
        if (empty($mensagem)) {
            $erros[] = 'O campo Mensagem é obrigatório.';
        }

        // Se houver erros, retorna JSON de erro
        if (!empty($erros)) {
            http_response_code(400); // Bad Request
            echo json_encode(['success' => false, 'message' => implode("\n", $erros)]);
            return; // Interrompe a execução
        }

        // 2. Preparar o e-mail
        $assunto = "Nova Mensagem do Formulário de Contato - " . $nome;

        // Monta o corpo do e-mail (pode ser mais elaborado com HTML)
        $corpoHtml = "
            <h2>Nova mensagem recebida pelo site:</h2>
            <p><strong>Nome:</strong> " . htmlspecialchars($nome) . "</p>
            <p><strong>E-mail:</strong> " . htmlspecialchars($emailRemetente) . "</p>
            <hr>
            <p><strong>Mensagem:</strong></p>
            <p style='white-space: pre-wrap;'>" . htmlspecialchars($mensagem) . "</p>
        ";
        // Corpo em texto puro (opcional, mas bom para compatibilidade)
        $corpoTexto = "Nova mensagem recebida pelo site:\n\n";
        $corpoTexto .= "Nome: " . $nome . "\n";
        $corpoTexto .= "E-mail: " . $emailRemetente . "\n";
        $corpoTexto .= "------------------------------------\n";
        $corpoTexto .= "Mensagem:\n" . $mensagem;

        // 3. Enviar o e-mail usando EmailService
        // O EmailService já está configurado para usar as credenciais do .env
        $enviado = $this->emailService->enviarEmail(
            $this->emailDestino,      // Para: Email da clínica
            'Contato Site Chris Psicologia', // Nome do Destinatário (interno)
            $assunto,                 // Assunto
            $corpoHtml,               // Corpo HTML
            $corpoTexto               // Corpo Texto Puro
            // Adicional: Configurar Reply-To para o email do remetente (opcional mas útil)
            // Para isso, precisaria modificar EmailService ou configurar o $mailer diretamente aqui
            // Ex: $this->emailService->getMailerInstance()->addReplyTo($emailRemetente, $nome);
        );

        // 4. Retornar resposta JSON para o frontend
        if ($enviado) {
            http_response_code(200); // OK
            echo json_encode(['success' => true, 'message' => 'Sua mensagem foi enviada com sucesso! Entraremos em contato em breve.']);
        } else {
            http_response_code(500); // Internal Server Error
            error_log("Falha ao enviar e-mail de contato de: " . $emailRemetente); // Log do erro
            echo json_encode(['success' => false, 'message' => 'Desculpe, ocorreu um erro ao enviar sua mensagem. Tente novamente mais tarde.']);
        }
    }
}