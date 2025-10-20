<?php
namespace App\Psico\Controllers;

use App\Psico\Core\View;
use App\Psico\Models\Usuario;
use App\Psico\Models\Agendamento;
use App\Psico\Models\Profissional;
use App\Psico\Database\Database;
use App\Psico\Core\Redirect;
use App\Psico\Core\Flash;
use App\Psico\Validadores\UsuarioValidador;
use PDOException; // Adicionado para capturar exceções do PDO

class UsuarioController {
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

    // ... (outros métodos como salvarUsuarios, viewListarUsuarios, etc., permanecem iguais à versão anterior) ...
     public function index(){
        $resultado = $this->usuario->buscarUsuarios();
        var_dump($resultado);
    }
    public function viewListarUsuarios() {
        // Garante que o usuário está logado e é admin para acessar esta página
        $this->verificarAcesso(['admin']); // Mantido aqui para proteger a listagem

        $pagina = $_GET['pagina'] ?? 1;
        $coluna = $_GET['coluna'] ?? null;
        $busca = $_GET['busca'] ?? null;

        if ($coluna && $busca !== null) {
            $dadosPaginados = $this->usuario->buscarComFiltro($coluna, $busca, (int)$pagina, 10);
        } else {
             $dadosPaginados = $this->usuario->paginacao((int)$pagina, 10);
        }


        // --- LÓGICA PARA OS CARDS ---
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

        // --- ARRAY DE STATS ATUALIZADO COM 4 ITENS ---
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
                'icon' => 'fa-user-md' // Ícone para profissionais
            ]
        ];

        View::render("usuario/index", [
            "usuarios" => $dadosPaginados['data'],
            "paginacao" => $dadosPaginados,
            "stats" => $stats
        ]);
    }

    // Salvar usuário (POST) - REGISTO PÚBLICO
    public function salvarUsuarios() {
        // REMOVIDO: $this->verificarAcesso(['admin']); // Não é necessário para registo público

        $erros = UsuarioValidador::ValidarEntradas($_POST);
        if(!empty($erros)){
            $mensagemErro = implode("<br>", array_values($erros));
            // Decide para onde redirecionar baseado na origem (admin ou registo público)
            // Se veio do formulário de registo, redireciona para ele. Senão, para o criar do admin.
            // Poderíamos adicionar um campo hidden no formulário de registo para identificar a origem.
            // Por simplicidade agora, vamos assumir que se não for admin logado, veio do registo.
             if (isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin') {
                 Redirect::redirecionarComMensagem("usuario/criar", "error", $mensagemErro);
             } else {
                 // Para o formulário público, talvez seja melhor retornar um JSON com o erro?
                 // Ou usar Flash e redirecionar para registro.html?error=1 (JS leria o parâmetro)
                 // Por enquanto, vamos manter o redirecionamento com Flash, mas para a página de registo
                 Flash::set("error", $mensagemErro);
                 header('Location: /registro.html'); // Redirecionamento direto para a página HTML
                 exit();
                 // Redirect::voltarPaginaAnteriorComMensagem("error", $mensagemErro); // Pode causar loop ou ir para lugar errado
             }
            return;
        }

        try {
            // Define o tipo padrão como 'cliente' para o registo público
             $tipoUsuario = $_POST['tipo_usuario'] ?? 'cliente';
             // No formulário de registro.html, o tipo_usuario está como 'user', vamos padronizar para 'cliente'
             if ($tipoUsuario === 'user') {
                 $tipoUsuario = 'cliente';
             }

            $resultado = $this->usuario->inserirUsuario(
                $_POST['nome_usuario'],
                $_POST['email_usuario'],
                $_POST['senha_usuario'],
                 $tipoUsuario, // Usar a variável definida
                $_POST['cpf'] ?? '' // CPF opcional no registo
            );

            if($resultado){
                // Se veio do admin, redireciona para a lista. Se veio do registo, para o index/login.
                 if (isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin') {
                     Redirect::redirecionarComMensagem("usuario/listar", "success", "Usuário criado com sucesso!");
                 } else {
                     Flash::set("success", "Registo realizado com sucesso! Pode fazer login.");
                     header('Location: /index.html'); // Ou para uma página de sucesso/login
                     exit();
                 }
            } else {
                 // Erro genérico se a inserção falhar por motivo desconhecido
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
            // Verifica se o erro é de chave duplicada (código 23000 ou 1062 para MySQL)
            if ($e->getCode() == 23000 || $e->getCode() == 1062) {
                $mensagemErro = "Erro ao registar: O email fornecido já está em uso.";
            } else {
                // Outro erro de base de dados
                 error_log("Erro de PDO ao inserir usuário: " . $e->getMessage()); // Loga o erro real
                $mensagemErro = "Ocorreu um erro inesperado no servidor. Tente novamente mais tarde.";
            }

            // Redireciona com a mensagem de erro específica
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
         $this->verificarAcesso(['admin', 'recepcionista']); // Mantido
        View::render("usuario/relatorio", ["id" => $id, "data1" => $data1, "data2" => $data2]);
        }


    public function viewEditarUsuarios($id) {
         $this->verificarAcesso(['admin']); // Mantido
        $usuario = $this->usuario->buscarUsuarioPorId((int)$id);
        if ($usuario) {
            View::render("usuario/edit", ["usuario" => $usuario]);
        } else {
            Redirect::redirecionarComMensagem("usuario/listar", "error", "Usuário não encontrado.");
        }
    }

    public function atualizarUsuarios($id) {
         $this->verificarAcesso(['admin']); // Mantido
         // Adiciona validação também na atualização
         $erros = UsuarioValidador::ValidarEntradas($_POST, true); // true indica que é update
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
                $_POST['senha_usuario'] ?? null, // Senha opcional na atualização
                $_POST['tipo_usuario'],
                $_POST['cpf'] ?? '', // Permite CPF vazio
                $status
            );

            if ($sucesso) {
                Redirect::redirecionarComMensagem("usuario/listar", "success", "Usuário atualizado com sucesso!");
            } else {
                Redirect::redirecionarComMensagem("usuario/editar/{$id}", "error", "Erro ao atualizar usuário.");
            }
         } catch (PDOException $e) {
             // Verifica se o erro é de chave duplicada (email)
             if ($e->getCode() == 23000 || $e->getCode() == 1062) {
                 $mensagemErro = "Erro ao atualizar: O email fornecido já está em uso por outro utilizador.";
             } else {
                 error_log("Erro de PDO ao atualizar usuário: " . $e->getMessage()); // Loga o erro real
                 $mensagemErro = "Ocorreu um erro inesperado no servidor ao atualizar. Tente novamente.";
             }
             Redirect::redirecionarComMensagem("usuario/editar/{$id}", "error", $mensagemErro);
         }
    }

    public function viewCriarUsuarios(){
        $this->verificarAcesso(['admin']); // Mantido
        View::render("usuario/create");
    }

    public function viewExcluirUsuarios($id) {
        $this->verificarAcesso(['admin']); // Mantido
        $usuario = $this->usuario->buscarUsuarioPorId((int)$id);
        if ($usuario) {
            // Não permitir excluir a si mesmo
             if ($usuario->id_usuario == ($_SESSION['usuario_id'] ?? null)) {
                 Redirect::redirecionarComMensagem("usuario/listar", "error", "Você não pode excluir a si mesmo.");
                 return;
             }
            // Adicionar lógica para impedir exclusão do último admin, se necessário

            View::render("usuario/delete", ["usuario" => $usuario]);
        } else {
            Redirect::redirecionarComMensagem("usuario/listar", "error", "Usuário não encontrado.");
        }
    }
        // --- MÉTODO PARA DELETAR (Soft Delete) ---
    public function deletarUsuarios($id) {
         $this->verificarAcesso(['admin']); // Mantido

         $usuario = $this->usuario->buscarUsuarioPorId((int)$id);
         if (!$usuario) {
              Redirect::redirecionarComMensagem("usuario/listar", "error", "Usuário não encontrado para exclusão.");
              return;
         }
         // Prevenção extra
         if ($usuario->id_usuario == ($_SESSION['usuario_id'] ?? null)) {
             Redirect::redirecionarComMensagem("usuario/listar", "error", "Você não pode excluir a si mesmo.");
             return;
         }

        // Usa o método de soft delete agora
        $sucesso = $this->usuario->excluirUsuario((int)$id);
        if ($sucesso) {
            // Invalida a sessão se o usuário excluído for o logado (caso raro, mas possível)
             if ((int)$id === ($_SESSION['usuario_id'] ?? null)) {
                 $this->logout();
             }
            Redirect::redirecionarComMensagem("usuario/listar", "success", "Usuário inativado com sucesso!");
        } else {
            Redirect::redirecionarComMensagem("usuario/listar", "error", "Erro ao inativar usuário.");
        }
    }

    public function dashboard() {
        // Verificar acesso - permitir admin, recepcionista e profissional (ajuste conforme necessário)
        $this->verificarAcesso(['admin', 'recepcionista', 'profissional']);

        // Opcional: Buscar alguns dados para exibir no dashboard
        $totalUsuarios = count($this->usuario->buscarTodosUsuarios());
        $totalAgendamentos = count($this->agendamento->buscarAgendamentos()); // Exemplo
        $totalProfissionais = count($this->profissional->listarProfissionais()); // Exemplo

        // Montar array de stats (pode ser mais simples ou complexo)
        $stats = [
             [
                'label' => 'Total de Usuários',
                'value' => $totalUsuarios,
                'icon' => 'fa-users',
                'link' => '/backend/usuario/listar' // Link para a secção
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
            // Adicionar mais stats conforme necessário (pagamentos, avaliações, etc.)
        ];

        // Renderiza a view do dashboard, passando os stats
        View::render("dashboard/index", ["stats" => $stats]);
    }

    // --- MÉTODO LOGIN ---
    public function login() {
        header('Content-Type: application/json'); // Define o cabeçalho JSON no início

        if (empty($_POST['email']) || empty($_POST['senha'])) {
            http_response_code(400); // Bad Request
            echo json_encode(['success' => false, 'message' => 'Email e senha são obrigatórios.']);
            return;
        }

        // Tenta autenticar
        $usuarioAutenticado = $this->usuario->autenticarUsuario($_POST['email'], $_POST['senha']);

        // Verifica se a autenticação foi bem-sucedida (retornou dados do usuário)
        if ($usuarioAutenticado) {
            // Verifica se o usuário está ativo
            if ($usuarioAutenticado->status_usuario === 'ativo') {
                // Regenera o ID da sessão para prevenir fixação de sessão
                session_regenerate_id(true);

                // Armazena dados na sessão
                $_SESSION['usuario_id'] = $usuarioAutenticado->id_usuario;
                $_SESSION['usuario_nome'] = $usuarioAutenticado->nome_usuario;
                $_SESSION['usuario_tipo'] = $usuarioAutenticado->tipo_usuario;
                $_SESSION['logged_in'] = true;

                // Resposta de sucesso - ENVIA TIPO E NOME PARA O FRONTEND
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => 'Login bem-sucedido!',
                    'userType' => $usuarioAutenticado->tipo_usuario, // Adicionado
                    'userName' => $usuarioAutenticado->nome_usuario   // Adicionado
                    // 'redirect' => '/backend/usuario/listar' // Removido - Frontend decide
                ]);
            } else {
                // Usuário encontrado, mas está inativo
                http_response_code(401); // Unauthorized
                echo json_encode(['success' => false, 'message' => 'Este utilizador está inativo. Contacte o administrador.']);
            }
        } else {
            // Usuário não encontrado ou senha incorreta
            http_response_code(401); // Unauthorized
            echo json_encode(['success' => false, 'message' => 'Email ou senha incorretos.']);
        }
    }

    // --- MÉTODO LOGOUT ---
    public function logout() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        header('Location: /index.html'); // Redireciona para a página inicial pública
        exit();
    }

     // --- MÉTODO PRIVADO PARA VERIFICAR ACESSO ---
     private function verificarAcesso(array $tiposPermitidos) {
          if (session_status() == PHP_SESSION_NONE) {
              session_start();
          }
          // Verifica se não está logado OU se o tipo não está na lista permitida
          if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !in_array($_SESSION['usuario_tipo'] ?? '', $tiposPermitidos)) {
              Flash::set('error', 'Acesso não autorizado ou sessão expirada.');

              // Se JÁ ESTAVA logado mas tentou aceder a área não permitida
              if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
                   // ALTERADO: Redireciona para a página inicial PÚBLICA
                   session_unset(); // Limpa a sessão para evitar loops se index.html tentar logar
                   session_destroy();
                   header('Location: /index.html'); // Manda para a página inicial pública
              } else {
                   // Se NÃO ESTAVA logado, destrói qualquer sessão residual e manda para a página inicial
                   session_unset();
                   session_destroy();
                   header('Location: /index.html');
              }
              exit();
      }
}
}