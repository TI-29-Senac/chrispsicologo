<?php
namespace App\Psico\Controllers;

use App\Psico\Controllers\Admin\AdminController;

use App\Psico\Core\View;
use App\Psico\Models\Usuario;
use App\Psico\Models\Agendamento;
use App\Psico\Models\Profissional;
use App\Psico\Database\Database;
use App\Psico\Core\Redirect;
use App\Psico\Validadores\UsuarioValidador;
use App\Psico\Core\FileManager;
use PDOException; 
use PDO;
use App\Psico\Core\Flash;
use App\Psico\Core\EmailService; 
use DateTime; 
use DateInterval;
 
class UsuarioController extends AdminController {

    public $usuario;
    public $db;
    public $agendamento;
    public $profissional;
    private $emailService;

    public function __construct(){
        $this->db = Database::getInstance();
        $this->usuario = new Usuario($this->db);
        $this->agendamento = new Agendamento($this->db);
        $this->profissional = new Profissional($this->db);
        $this->emailService = new EmailService();
    }

    
     public function index(){
    $resultado = $this->usuario->buscarUsuarios();
    }
    public function viewListarUsuarios() {
        
        $this->verificarAcesso(['admin']); 

        $pagina = $_GET['pagina'] ?? 1;
        $coluna = $_GET['coluna'] ?? null;
        $busca = $_GET['busca'] ?? null;

        if ($coluna && $busca !== null) {
            $dadosPaginados = $this->usuario->buscarComFiltro($coluna, $busca, (int)$pagina, 10);
        } else {
             $dadosPaginados = $this->usuario->paginacao((int)$pagina, 10);
        }


        
        $todosUsuarios = $this->usuario->buscarTodosUsuarios();

        $totalUsuarios = count($todosUsuarios);
        $usuariosAtivos = 0;
        $totalProfissionais = 0;

        foreach ($todosUsuarios as $usuario) {
            if ($usuario->status_usuario === 'ativo') {
                $usuariosAtivos++;
            }
            if ($usuario->tipo_usuario === 'profissional') {
                $totalProfissionais++;
            }
        }
        $usuariosInativos = $totalUsuarios - $usuariosAtivos;

        
        $stats = [
            [
                'label' => 'Total de Usuários',
                'value' => $totalUsuarios,
                'icon' => 'fa-users'
            ],
            [
                'label' => 'Usuários Ativos',
                'value' => $usuariosAtivos,
                'icon' => 'fa-check-circle'
            ],
            [
                'label' => 'Usuários Inativos',
                'value' => $usuariosInativos,
                'icon' => 'fa-times-circle'
            ],
            [
                'label' => 'Profissionais',
                'value' => $totalProfissionais,
                'icon' => 'fa-user-md' 
            ]
        ];

        View::render("usuario/index", [
            "usuarios" => $dadosPaginados['data'],
            "paginacao" => $dadosPaginados,
            "stats" => $stats
        ]);
    }

    
    public function salvarUsuarios() {
        

        $erros = UsuarioValidador::ValidarEntradas($_POST);
        if(!empty($erros)){
            $mensagemErro = implode("<br>", array_values($erros));
            
            
            
            
             if (isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin') {
                 Redirect::redirecionarComMensagem("usuario/criar", "error", $mensagemErro);
             } else {
                 
                 
                 
                 Flash::set("error", $mensagemErro);
                 header('Location: /registro.html'); 
                 exit();
                 
             }
            return;
        }

        try {
            
             $tipoUsuario = $_POST['tipo_usuario'] ?? 'cliente';
             
             if ($tipoUsuario === 'user') {
                 $tipoUsuario = 'cliente';
             }

            $resultado = $this->usuario->inserirUsuario(
                $_POST['nome_usuario'],
                $_POST['email_usuario'],
                $_POST['senha_usuario'],
                 $tipoUsuario, 
                $_POST['cpf'] ?? '' 
            );

            if($resultado){
                
                 if (isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin') {
                     Redirect::redirecionarComMensagem("usuario/listar", "success", "Usuário criado com sucesso!");
                 } else {
                     Flash::set("success", "Registo realizado com sucesso! Pode fazer login.");
                     header('Location: /index.html'); 
                     exit();
                 }
            } else {
                 
                 $mensagemErro = "Ocorreu um erro ao registar o utilizador.";
                  if (isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin') {
                      Redirect::redirecionarComMensagem("usuario/criar", "error", $mensagemErro);
                  } else {
                      Flash::set("error", $mensagemErro);
                      header('Location: /registro.html');
                      exit();
                  }
            }
        } catch (PDOException $e) {
            
            if ($e->getCode() == 23000 || $e->getCode() == 1062) {
                $mensagemErro = "Erro ao registar: O email fornecido já está em uso.";
            } else {
                
                 error_log("Erro de PDO ao inserir usuário: " . $e->getMessage()); 
                $mensagemErro = "Ocorreu um erro inesperado no servidor. Tente novamente mais tarde.";
            }

            
            if (isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin') {
                Redirect::redirecionarComMensagem("usuario/criar", "error", $mensagemErro);
            } else {
                Flash::set("error", $mensagemErro);
                header('Location: /registro.html');
                exit();
            }
        }
    }


    public function relatorioUsuarios($id, $data1, $data2) {
         $this->verificarAcesso(['admin', 'recepcionista']); 
        View::render("usuario/relatorio", ["id" => $id, "data1" => $data1, "data2" => $data2]);
        }


    public function viewEditarUsuarios($id) {
         $this->verificarAcesso(['admin']); 
        $usuario = $this->usuario->buscarUsuarioPorId((int)$id);
        if ($usuario) {
            View::render("usuario/edit", ["usuario" => $usuario]);
        } else {
            Redirect::redirecionarComMensagem("usuario/listar", "error", "Usuário não encontrado.");
        }
    }

    public function atualizarUsuarios($id) {
         $this->verificarAcesso(['admin']); 
         
         $erros = UsuarioValidador::ValidarEntradas($_POST, true); 
         if (!empty($erros)) {
              $mensagemErro = implode("<br>", array_values($erros));
              Redirect::redirecionarComMensagem("usuario/editar/{$id}", "error", $mensagemErro);
              return;
         }

        try {
            $status = $_POST['status_usuario'] ?? 'ativo';
            $sucesso = $this->usuario->atualizarUsuario(
                (int)$id,
                $_POST['nome_usuario'],
                $_POST['email_usuario'],
                $_POST['senha_usuario'] ?? null, 
                $_POST['tipo_usuario'],
                $_POST['cpf'] ?? '', 
                $status
            );

            if ($sucesso) {
                Redirect::redirecionarComMensagem("usuario/listar", "success", "Usuário atualizado com sucesso!");
            } else {
                Redirect::redirecionarComMensagem("usuario/editar/{$id}", "error", "Erro ao atualizar usuário.");
            }
         } catch (PDOException $e) {
             
             if ($e->getCode() == 23000 || $e->getCode() == 1062) {
                 $mensagemErro = "Erro ao atualizar: O email fornecido já está em uso por outro utilizador.";
             } else {
                 error_log("Erro de PDO ao atualizar usuário: " . $e->getMessage()); 
                 $mensagemErro = "Ocorreu um erro inesperado no servidor ao atualizar. Tente novamente.";
             }
             Redirect::redirecionarComMensagem("usuario/editar/{$id}", "error", $mensagemErro);
         }
    }

    public function viewCriarUsuarios(){
        $this->verificarAcesso(['admin']); 
        View::render("usuario/create");
    }

    public function viewExcluirUsuarios($id) {
        $this->verificarAcesso(['admin']); 
        $usuario = $this->usuario->buscarUsuarioPorId((int)$id);
        if ($usuario) {
            
             if ($usuario->id_usuario == ($_SESSION['usuario_id'] ?? null)) {
                 Redirect::redirecionarComMensagem("usuario/listar", "error", "Você não pode excluir a si mesmo.");
                 return;
             }
            

            View::render("usuario/delete", ["usuario" => $usuario]);
        } else {
            Redirect::redirecionarComMensagem("usuario/listar", "error", "Usuário não encontrado.");
        }
    }
        
    public function deletarUsuarios($id) {
         $this->verificarAcesso(['admin']); 

         $usuario = $this->usuario->buscarUsuarioPorId((int)$id);
         if (!$usuario) {
              Redirect::redirecionarComMensagem("usuario/listar", "error", "Usuário não encontrado para exclusão.");
              return;
         }
         
         if ($usuario->id_usuario == ($_SESSION['usuario_id'] ?? null)) {
             Redirect::redirecionarComMensagem("usuario/listar", "error", "Você não pode excluir a si mesmo.");
             return;
         }

        
        $sucesso = $this->usuario->excluirUsuario((int)$id);
        if ($sucesso) {
            
             if ((int)$id === ($_SESSION['usuario_id'] ?? null)) {
                 $this->logout();
             }
            Redirect::redirecionarComMensagem("usuario/listar", "success", "Usuário inativado com sucesso!");
        } else {
            Redirect::redirecionarComMensagem("usuario/listar", "error", "Erro ao inativar usuário.");
        }
    }

    
    public function login() {
        header('Content-Type: application/json'); 

        if (empty($_POST['email']) || empty($_POST['senha'])) {
            http_response_code(400); 
            echo json_encode(['success' => false, 'message' => 'Email e senha são obrigatórios.']);
            return;
        }

        
        $usuarioAutenticado = $this->usuario->autenticarUsuario($_POST['email'], $_POST['senha']);

        
        if ($usuarioAutenticado) {
            
            if ($usuarioAutenticado->status_usuario === 'ativo') {
                
                session_regenerate_id(true);

                
                $_SESSION['usuario_id'] = $usuarioAutenticado->id_usuario;
                $_SESSION['usuario_nome'] = $usuarioAutenticado->nome_usuario;
                $_SESSION['usuario_tipo'] = $usuarioAutenticado->tipo_usuario;
                $_SESSION['logged_in'] = true;

                
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => 'Login bem-sucedido!',
                    'userType' => $usuarioAutenticado->tipo_usuario, 
                    'userName' => $usuarioAutenticado->nome_usuario   
                    
                ]);
            } else {
                
                http_response_code(401); 
                echo json_encode(['success' => false, 'message' => 'Este utilizador está inativo. Contacte o administrador.']);
            }
        } else {
            
            http_response_code(401); 
            echo json_encode(['success' => false, 'message' => 'Email ou senha incorretos.']);
        }
    }

    
    public function logout() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        header('Location: /index.html'); 
        exit();
    }

     
     private function verificarAcesso(array $tiposPermitidos) {
          if (session_status() == PHP_SESSION_NONE) {
              session_start();
          }
          
          if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !in_array($_SESSION['usuario_tipo'] ?? '', $tiposPermitidos)) {
              Flash::set('error', 'Acesso não autorizado ou sessão expirada.');

              
              if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
                   
                   session_unset(); 
                   session_destroy();
                   header('Location: /index.html'); 
              } else {
                   
                   session_unset();
                   session_destroy();
                   header('Location: /index.html');
              }
              exit();
      }
}

    // Dentro da classe UsuarioController
    public function meuPerfilApi() {
        error_reporting(E_ALL); 
        ini_set('display_errors', 1); 
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        ob_start();
        header('Content-Type: application/json'); 
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            http_response_code(401);
            ob_end_clean(); 
            echo json_encode(['success' => false, 'message' => 'Acesso não autorizado.']);
            exit; 
        }
        $id_usuario = $_SESSION['usuario_id'];
        $usuario = null; 
        $errorMessage = null; 
        try {
            $usuario = $this->usuario->buscarUsuarioPorId((int)$id_usuario);
        } catch (\PDOException $e) {
            error_log("PDO Error in meuPerfilApi: " . $e->getMessage()); // Loga o erro PDO
            $errorMessage = "Erro interno ao buscar dados do perfil [DB].";
        } catch (\Exception $e) {
            error_log("General Error in meuPerfilApi: " . $e->getMessage()); // Loga outros erros
            $errorMessage = "Erro interno ao buscar dados do perfil.";
        }

        // Limpa o buffer ANTES de verificar o resultado e enviar o JSON final
        $potentialErrorsOutput = ob_get_clean();
        if (!empty($potentialErrorsOutput)) {
             // Se algo foi impresso (um erro PHP, por exemplo), loga e retorna erro JSON
             error_log("Output inesperado capturado em meuPerfilApi: " . $potentialErrorsOutput);
             http_response_code(500);
             echo json_encode(['success' => false, 'message' => 'Erro interno do servidor [Output].', 'details' => $potentialErrorsOutput]);
             exit;
        }

        // Continua com a lógica original se não houve output inesperado
        if ($errorMessage) {
             http_response_code(500);
             echo json_encode(['success' => false, 'message' => $errorMessage]);
             exit;
        }

        if ($usuario) {
            unset($usuario->senha_usuario);
            http_response_code(200);
            echo json_encode(['success' => true, 'data' => $usuario]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Utilizador não encontrado.']);
        }
        exit; // Garante que nada mais é executado após o JSON
    }

    public function atualizarMeuPerfil() {
         header('Content-Type: application/json'); 

         if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
             http_response_code(401);
             echo json_encode(['success' => false, 'message' => 'Acesso não autorizado.']);
             return;
         }
         $id = $_SESSION['usuario_id'];
         
         // Usa o validador para atualização
         $erros = \App\Psico\Validadores\UsuarioValidador::ValidarEntradas($_POST, true); 
         if (!empty($erros)) {
             http_response_code(400);
             echo json_encode(['success' => false, 'message' => implode("<br>", array_values($erros))]);
             return;
         }
         
         try {
             $usuarioAtual = $this->usuario->buscarUsuarioPorId((int)$id);
             if (!$usuarioAtual) {
                 http_response_code(404);
                 echo json_encode(['success' => false, 'message' => 'Usuário não encontrado.']);
                 return;
             }

             $sucesso = $this->usuario->atualizarUsuario(
                 (int)$id,
                 $_POST['nome_usuario'],
                 $_POST['email_usuario'],
                 $_POST['senha_usuario'] ?? null, 
                 $usuarioAtual->tipo_usuario,
                 $_POST['cpf'] ?? '', 
                 $usuarioAtual->status_usuario
             );

             if ($sucesso) {
                 if (isset($_POST['nome_usuario'])) {
                      $_SESSION['usuario_nome'] = $_POST['nome_usuario'];
                 }

                 http_response_code(200);
                 echo json_encode(['success' => true, 'message' => 'Perfil atualizado com sucesso!', 'userName' => $_SESSION['usuario_nome']]);
             } else {
                 http_response_code(500);
                 echo json_encode(['success' => false, 'message' => 'Erro ao atualizar perfil.']);
             }
         } catch (\PDOException $e) {
              if ($e->getCode() == 23000 || $e->getCode() == 1062) {
                 http_response_code(409);
                 echo json_encode(['success' => false, 'message' => "Erro ao atualizar: O email fornecido já está em uso."]);
             } else {
                 error_log("Erro de PDO ao atualizar usuário: " . $e->getMessage()); 
                 http_response_code(500);
                 echo json_encode(['success' => false, 'message' => 'Ocorreu um erro inesperado no servidor ao atualizar.']);
             }
         }
     }

     public function solicitarRecuperacaoSenha() {
        header('Content-Type: application/json');

        if (empty($_POST['email'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'O endereço de e-mail é obrigatório.']);
            return;
        }

        $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
        if (!$email) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Formato de e-mail inválido.']);
            return;
        }

        $usuario = $this->usuario->buscarUsuarioPorEmail($email);

        if ($usuario) {
            // 1. Gerar Token Seguro
            $token = bin2hex(random_bytes(32));

            // 2. Definir Expiração (ex: 1 hora a partir de agora)
            $agora = new DateTime();
            $expiraEm = (clone $agora)->add(new DateInterval('PT1H')); // PT1H = Período de Tempo de 1 Hora

            // 3. Salvar no Banco (ou atualizar se já existir pedido para o email)
            try {
                $sql = "INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expira)
                        ON DUPLICATE KEY UPDATE token = :token, created_at = NOW(), expires_at = :expira";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':token', $token);
                $stmt->bindValue(':expira', $expiraEm->format('Y-m-d H:i:s'));
                $stmt->execute();
                if (!$stmt->execute()) { // Verifica se execute falhou
                    throw new \RuntimeException("Erro ao salvar token de reset no banco.");
                }

            } catch (\PDOException $e) { // Captura especificamente PDOException
                error_log("Erro de PDO ao salvar token de reset: " . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Erro interno [DB] ao processar a solicitação. Tente novamente mais tarde.']);
                return;
             } catch (\Exception $e) { // Captura outras exceções (como RuntimeException)
                 error_log("Erro geral ao salvar token de reset: " . $e->getMessage());
                 http_response_code(500);
                 echo json_encode(['success' => false, 'message' => $e->getMessage()]); // Retorna a mensagem da exceção
                 return;
             }

            $urlBaseSite = ($_SERVER['HTTPS'] ?? 'http') . '://' . $_SERVER['HTTP_HOST'];
            $linkReset = $urlBaseSite . '/redefinir-senha.html?token=' . urlencode($token);

            $assunto = "Redefinição de Senha - Chris Psicologia";
            $corpoHtml = "
                <p>Olá " . htmlspecialchars($usuario->nome_usuario) . ",</p>
                <p>Recebemos uma solicitação para redefinir a senha da sua conta em Chris Psicologia.</p>
                <p>Se você não fez esta solicitação, pode ignorar este email.</p>
                <p>Para redefinir sua senha, clique no link abaixo:</p>
                <p><a href='" . $linkReset . "'>" . $linkReset . "</a></p>
                <p>Este link expirará em 1 hora.</p>
                <p>Atenciosamente,<br>Equipe Chris Psicologia</p>
            ";
             $corpoTexto = "Olá " . $usuario->nome_usuario . ",\n\nRecebemos uma solicitação para redefinir a senha...\n\nLink: " . $linkReset . "\n\nExpira em 1 hora.\n\nAtenciosamente,\nEquipe Chris Psicologia";

            $enviado = $this->emailService->enviarEmail($email, $usuario->nome_usuario, $assunto, $corpoHtml, $corpoTexto);

            if (!$enviado) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Erro ao tentar enviar o email de recuperação. Tente novamente mais tarde.']);
                return;
            }

        }
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Se o e-mail estiver cadastrado em nosso sistema, um link de recuperação foi enviado.']);
        return;
    }

    public function validarTokenReset(string $token) {
        header('Content-Type: application/json');

        if (empty($token)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Token não fornecido.']);
            return;
        }

        try {
            $sql = "SELECT email, expires_at FROM password_resets WHERE token = :token LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':token', $token);
            $stmt->execute();
            $resetRequest = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$resetRequest) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Token inválido. Solicite a recuperação novamente.']);
                return;
            }

            // Verificar expiração
            $agora = new DateTime();
            $expiraEm = new DateTime($resetRequest['expires_at']);

            if ($agora > $expiraEm) {
                http_response_code(410); // Gone
                echo json_encode(['success' => false, 'message' => 'Token expirado. Solicite a recuperação novamente.']);
                // Opcional: Remover token expirado do banco aqui
                // $this->db->prepare("DELETE FROM password_resets WHERE token = :token")->execute([':token' => $token]);
                return;
            }

            // Token válido e não expirado
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'Token válido.']);

        } catch (\Exception $e) { // Captura PDOException e DateTime Exception
            error_log("Erro ao validar token de reset: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erro interno ao validar o token.']);
        }
    }

    // --- NOVO MÉTODO: Processar a Redefinição ---
    public function processarRedefinicaoSenha() {
        header('Content-Type: application/json');

        $token = $_POST['token'] ?? null;
        $novaSenha = $_POST['nova_senha'] ?? null;

        // Validação básica
        if (empty($token) || empty($novaSenha)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Token e nova senha são obrigatórios.']);
            return;
        }
        if (strlen($novaSenha) < 6) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'A nova senha deve ter pelo menos 6 caracteres.']);
            return;
        }

        try {
            // 1. Validar o Token Novamente (busca e expiração)
            $sqlSelect = "SELECT email, expires_at FROM password_resets WHERE token = :token LIMIT 1";
            $stmtSelect = $this->db->prepare($sqlSelect);
            $stmtSelect->bindParam(':token', $token);
            $stmtSelect->execute();
            $resetRequest = $stmtSelect->fetch(PDO::FETCH_ASSOC);

            if (!$resetRequest) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Token inválido ou já utilizado. Solicite novamente.']);
                return;
            }

            $agora = new DateTime();
            $expiraEm = new DateTime($resetRequest['expires_at']);
            if ($agora > $expiraEm) {
                http_response_code(410);
                echo json_encode(['success' => false, 'message' => 'Token expirado. Solicite novamente.']);
                // Opcional: Remover token expirado
                // $this->db->prepare("DELETE FROM password_resets WHERE token = :token")->execute([':token' => $token]);
                return;
            }

            // 2. Buscar o usuário pelo email associado ao token
            $emailUsuario = $resetRequest['email'];
            $usuario = $this->usuario->buscarUsuarioPorEmail($emailUsuario);
            if (!$usuario) {
                // Isso não deveria acontecer se o token foi gerado corretamente, mas é uma segurança extra
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Utilizador associado ao token não encontrado.']);
                // Considerar remover o token inválido aqui também
                return;
            }

            // 3. Atualizar a senha do usuário
            // Usaremos o método atualizarUsuario, mas precisamos dos outros dados.
            // Poderíamos criar um método específico no Model `Usuario.php` só para atualizar a senha.
            // Vamos usar o `atualizarUsuario` por enquanto:
            $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
            $sqlSenha = "UPDATE usuario SET senha_usuario = :senhaHash, atualizado_em = NOW() WHERE id_usuario = :id";
            $stmtSenha = $this->db->prepare($sqlSenha);
            $stmtSenha->bindParam(':senhaHash', $senhaHash);
            $stmtSenha->bindParam(':id', $usuario->id_usuario, PDO::PARAM_INT);
            $sucessoUpdate = $stmtSenha->execute();

            if (!$sucessoUpdate) {
                 throw new \Exception('Falha ao atualizar a senha no banco de dados.');
            }

            // 4. Remover/Invalidar o Token da tabela password_resets
            $sqlDelete = "DELETE FROM password_resets WHERE email = :email";
            $stmtDelete = $this->db->prepare($sqlDelete);
            $stmtDelete->bindParam(':email', $emailUsuario);
            $stmtDelete->execute();

            // 5. Retornar Sucesso
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'Senha redefinida com sucesso!']);

        } catch (\Exception $e) {
            error_log("Erro ao processar redefinição de senha: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erro interno ao redefinir a senha. Tente novamente mais tarde.']);
        }
    }

    public function dashboard() {
        
        $this->verificarAcesso(['admin', 'recepcionista', 'profissional']);

        // --- DADOS DOS CARDS (JÁ EXISTENTES) ---
        $totalUsuarios = count($this->usuario->buscarTodosUsuarios());
        $totalAgendamentos = count($this->agendamento->buscarAgendamentos()); 
        $totalProfissionais = count($this->profissional->listarProfissionais()); 

        $stats = [
             [
                'label' => 'Total de Usuários',
                'value' => $totalUsuarios,
                'icon' => 'fa-users',
                'link' => '/backend/usuario/listar' 
            ],
             [
                'label' => 'Agendamentos',
                'value' => $totalAgendamentos,
                'icon' => 'fa-calendar',
                'link' => '/backend/agendamentos/listar'
            ],
             [
                'label' => 'Profissionais',
                'value' => $totalProfissionais,
                'icon' => 'fa-user-md',
                'link' => '/backend/profissionais/listar'
            ]
        ];

        // --- INÍCIO: NOVOS DADOS PARA GRÁFICOS ---
        
        // Carrega o model de Pagamento
        $pagamentoModel = new \App\Psico\Models\Pagamento($this->db);

        // 1. Gráfico de Agendamentos
        $agendamentosData = $this->agendamento->getAgendamentosPorMes();
        $chartAgendamentosLabels = [];
        $chartAgendamentosValores = [];
        foreach ($agendamentosData as $data) {
            // Formata '2023-10-01' para 'Out/2023' (Exemplo de formatação)
            $chartAgendamentosLabels[] = date('m/Y', strtotime($data->mes_ano)); 
            $chartAgendamentosValores[] = $data->total;
        }

        // 2. Gráfico de Novos Clientes
        $novosClientesData = $this->usuario->getNovosClientesPorMes();
        $chartNovosClientesLabels = [];
        $chartNovosClientesValores = [];
        foreach ($novosClientesData as $data) {
            $chartNovosClientesLabels[] = date('m/Y', strtotime($data->mes_ano));
            $chartNovosClientesValores[] = $data->total;
        }

        // 3. Gráfico de Faturamento
        $faturamentoData = $pagamentoModel->getFaturamentoPorMes();
        $chartFaturamentoLabels = [];
        $chartFaturamentoValores = [];
        foreach ($faturamentoData as $data) {
            $chartFaturamentoLabels[] = date('m/Y', strtotime($data->mes_ano));
            $chartFaturamentoValores[] = $data->total;
        }
        
        // --- FIM: NOVOS DADOS PARA GRÁFICOS ---


        // Adiciona os dados dos gráficos ao array enviado para a View
        View::render("dashboard/index", [
            "stats" => $stats,

            // Dados para os gráficos
            "chartAgendamentosLabels" => json_encode($chartAgendamentosLabels),
            "chartAgendamentosValores" => json_encode($chartAgendamentosValores),
            "chartNovosClientesLabels" => json_encode($chartNovosClientesLabels),
            "chartNovosClientesValores" => json_encode($chartNovosClientesValores),
            "chartFaturamentoLabels" => json_encode($chartFaturamentoLabels),
            "chartFaturamentoValores" => json_encode($chartFaturamentoValores)
        ]);
    }

}