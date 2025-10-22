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
use App\Psico\Core\Flash;
 
class UsuarioController extends AdminController {

    public $usuario;
    public $db;
    public $agendamento;
    public $profissional;

    public function __construct(){
        $this->db = Database::getInstance();
        $this->usuario = new Usuario($this->db);
        $this->agendamento = new Agendamento($this->db);
        $this->profissional = new Profissional($this->db);
    }

    
     public function index(){
        $resultado = $this->usuario->buscarUsuarios();
        var_dump($resultado);
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

    public function dashboard() {
        
        $this->verificarAcesso(['admin', 'recepcionista', 'profissional']);

        
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

        
        View::render("dashboard/index", ["stats" => $stats]);
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
}