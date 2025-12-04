<?php
namespace App\Psico\Controllers;

use App\Psico\Core\EmailService;     // Para enviar o e-mail para a CLÍNICA
use App\Psico\Core\EmailNotificar;   // Para avisar o USUÁRIO (Novo)
use App\Psico\Models\Contato;        // Para salvar no BANCO (Novo)
use App\Psico\Database\Database;     // Conexão com o banco
use App\Psico\Database\Config;

class ContatoController {

    private $emailService;
    private $emailNotificar;
    private $contatoModel;
    private $emailDestino;

    public function __construct() {
        $db = Database::getInstance();
        $this->contatoModel = new Contato($db);
        
        $this->emailService = new EmailService();
        $this->emailNotificar = new EmailNotificar();
        
        $this->emailDestino = Config::get()['mailer']['from_address'] ?? 'email_padrao@exemplo.com';
    }

    public function processarFormulario() {
        header('Content-Type: application/json');

        // 1. Obter e validar dados
        $nome = trim($_POST['nome'] ?? '');
        $emailRemetente = trim($_POST['email'] ?? '');
        $mensagem = trim($_POST['mensagem'] ?? '');

        $erros = [];
        if (empty($nome)) $erros[] = 'Nome obrigatório.';
        if (empty($emailRemetente) || !filter_var($emailRemetente, FILTER_VALIDATE_EMAIL)) $erros[] = 'E-mail inválido.';
        if (empty($mensagem)) $erros[] = 'Mensagem obrigatória.';

        if (!empty($erros)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => implode("\n", $erros)]);
            return;
        }

        // 2. Salvar no Banco de Dados
        try {
            $salvo = $this->contatoModel->inserirContato($nome, $emailRemetente, $mensagem);
            if (!$salvo) {
                throw new \Exception("Erro ao salvar contato no banco.");
            }
        } catch (\Exception $e) {
            error_log("Erro DB Contato: " . $e->getMessage());
            // Opcional: Continuar mesmo se falhar o banco, ou parar. Aqui paramos.
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erro interno ao salvar sua mensagem.']);
            return;
        }

        // 3. Enviar notificação para o USUÁRIO (EmailNotificar)
        // Isso roda em segundo plano ou silenciamos erros para não travar a resposta principal
        $this->emailNotificar->notificarRecebimento($emailRemetente, $nome);

        // 4. Enviar e-mail para a CLÍNICA (Aviso de novo lead)
        $assuntoClinica = "Novo Contato pelo Site - " . $nome;
        $corpoClinica = "<h2>Novo contato:</h2><p><strong>Nome:</strong> $nome</p><p><strong>Email:</strong> $emailRemetente</p><p><strong>Msg:</strong> $mensagem</p>";
        
        $this->emailService->enviarEmail(
            $this->emailDestino, 
            'Admin Chris Psicologia', 
            $assuntoClinica, 
            $corpoClinica
        );

        // 5. Resposta Final
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Mensagem enviada e salva com sucesso! Verifique seu e-mail.']);
    }
}