<?php
namespace App\Psico\Controllers;

use App\Psico\Models\ImagemSite;
use App\Psico\Database\Database;
use App\Psico\Core\View;
use App\Psico\Core\Redirect;
use App\Psico\Core\FileManager;
use App\Psico\Controllers\Admin\AuthenticatedController; // Assume que está usando autenticação
use PDO;

class ImagemController extends AuthenticatedController { // Ajuste a classe base se necessário

    private ImagemSite $imagemModel;
    private FileManager $fileManager;
    private string $diretorioUpload = 'img/site'; // Diretório relativo à raiz do projeto
    private PDO $db;

    public function __construct() {
        // parent::__construct(); // Descomente se AuthenticatedController tiver lógica no construtor
        $this->db = Database::getInstance();
        $this->imagemModel = new ImagemSite($this->db);
        // O FileManager espera o caminho absoluto da pasta raiz do projeto
        $this->fileManager = new FileManager(dirname(__DIR__, 2)); // Sobe 2 níveis de /backend/Controllers
    }

    // --- Métodos do Painel Administrativo (CRUD de Imagens) ---

    public function viewListarImagens() {
        $imagensAgrupadas = $this->imagemModel->buscarTodasAgrupadasPorPagina();
        View::render('imagem/index', ['imagensAgrupadas' => $imagensAgrupadas]);
    }

    public function viewCriarImagem() {
        $paginas = $this->imagemModel->buscarPaginasDisponiveis();
        View::render('imagem/create', ['paginas' => $paginas]);
    }

    public function buscarSecoesPorPaginaApi(int $id_pagina) {
        header('Content-Type: application/json');
        try {
            $secoes = $this->imagemModel->buscarSecoesPorPagina($id_pagina);
            echo json_encode(['success' => true, 'secoes' => $secoes]);
        } catch (\Exception $e) {
            error_log("Erro API buscarSecoesPorPagina: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erro ao buscar seções.']);
        }
    }

    public function salvarImagem() {
        // Validações básicas (página, seção, arquivo, título, texto)
        if (empty($_POST['id_pagina']) || empty($_POST['id_secao']) || !isset($_FILES['arquivo_imagem']) || $_FILES['arquivo_imagem']['error'] != UPLOAD_ERR_OK || empty($_POST['titulo_secao']) || empty($_POST['texto'])) {
             Redirect::redirecionarComMensagem("imagens/criar", "error", "Preencha todos os campos obrigatórios: Página, Seção, Imagem, Título e Texto.");
             return;
        }

        $caminhoImagemSalva = null;
        $conteudoInseridoId = false;

        // Inicia transação
        $this->db->beginTransaction();

        try {
            // 1. Salva a imagem
            $caminhoImagemSalva = $this->fileManager->salvarArquivo(
                $_FILES['arquivo_imagem'], $this->diretorioUpload,
                ['image/jpeg', 'image/png', 'image/webp', 'image/gif'], 2 * 1024 * 1024
            );
            $urlParaSalvar = $caminhoImagemSalva;
            $id_secao = (int)$_POST['id_secao'];
            $ordem = isset($_POST['ordem']) ? (int)$_POST['ordem'] : 99;

            $id_imagem_inserida = $this->imagemModel->inserirImagem($id_secao, $urlParaSalvar, $ordem);
            if (!$id_imagem_inserida) {
                throw new \Exception("Erro ao salvar informações da imagem.");
            }

            // 2. Salva o conteúdo associado
            $titulo = $_POST['titulo_secao'];
            $subtitulo = !empty($_POST['subtitulo']) ? $_POST['subtitulo'] : null;
            $texto = $_POST['texto'];
            // Usa a mesma ordem para o conteúdo
            $conteudoInseridoId = $this->imagemModel->inserirConteudo($id_secao, $titulo, $subtitulo, $texto, $ordem);
             if (!$conteudoInseridoId) {
                throw new \Exception("Erro ao salvar o conteúdo associado.");
            }

            // Confirma transação
            $this->db->commit();
            Redirect::redirecionarComMensagem("imagens/listar", "success", "Imagem e conteúdo adicionados!");

        } catch (\Exception $e) {
            // Desfaz tudo
            $this->db->rollBack();
            if ($caminhoImagemSalva) { $this->fileManager->delete($caminhoImagemSalva); } // Deleta imagem física se salva
            error_log("Erro salvarImagem: " . $e->getMessage());
            Redirect::redirecionarComMensagem("imagens/criar", "error", "Erro: " . $e->getMessage());
        }
    }

    // --- Método atualizarImagem ATUALIZADO ---
    public function atualizarImagem(int $id) {
        $imagemAtual = $this->imagemModel->buscarImagemPorId($id);
        if (!$imagemAtual) {
            Redirect::redirecionarComMensagem("imagens/listar", "error", "Imagem não encontrada.");
            return;
        }
        // Validação básica do conteúdo
         if (empty($_POST['titulo_secao']) || empty($_POST['texto'])) {
             Redirect::redirecionarComMensagem("imagens/editar/{$id}", "error", "Título e Texto são obrigatórios.");
             return;
         }

        $ordem = isset($_POST['ordem']) ? (int)$_POST['ordem'] : $imagemAtual->ordem;
        $caminhoNovaImagem = null;
        // Usa a URL da imagem atual buscada do banco, NÃO do POST 'imagem_atual_url'
        $caminhoImagemAntiga = $imagemAtual->url_imagem;
        $urlParaSalvar = $caminhoImagemAntiga;

        // Inicia transação
        $this->db->beginTransaction();

        try {
            // 1. Processa upload de NOVA imagem (se houver)
            if (isset($_FILES['arquivo_imagem']) && $_FILES['arquivo_imagem']['error'] == UPLOAD_ERR_OK) {
                $caminhoNovaImagem = $this->fileManager->salvarArquivo(
                     $_FILES['arquivo_imagem'], $this->diretorioUpload,
                     ['image/jpeg', 'image/png', 'image/webp', 'image/gif'], 2 * 1024 * 1024
                 );
                 $urlParaSalvar = $caminhoNovaImagem; // Define a nova URL para salvar no DB
            }

            // 2. Atualiza a tabela imagem
            // Passa null como URL se não houver nova imagem para não sobrescrever com o valor antigo do hidden field
            $sucessoImagem = $this->imagemModel->atualizarImagem($id, ($caminhoNovaImagem ? $urlParaSalvar : null), $ordem);
            if (!$sucessoImagem) {
                throw new \Exception("Erro ao atualizar dados da imagem.");
            }

            // 3. Atualiza o conteúdo na tabela conteudo_site USANDO o ID específico
            $id_secao = (int)($_POST['id_secao'] ?? $imagemAtual->id_secao); // Pega a seção (para caso de inserção)
            $titulo = $_POST['titulo_secao'];
            $subtitulo = !empty($_POST['subtitulo']) ? $_POST['subtitulo'] : null;
            $texto = $_POST['texto'];
            // Garante que $id_conteudo seja inteiro ou null
            $id_conteudo = isset($_POST['id_conteudo']) && $_POST['id_conteudo'] !== '' ? (int)$_POST['id_conteudo'] : null;

            $sucessoConteudo = false;
            if ($id_conteudo) {
                // Prioriza atualizar pelo ID do conteúdo específico
                $sucessoConteudo = $this->imagemModel->atualizarConteudoPorId($id_conteudo, $titulo, $subtitulo, $texto, $ordem);
                if (!$sucessoConteudo) {
                    // Log: O ID foi passado, mas a atualização falhou (talvez ID inválido?)
                    error_log("Aviso: Falha ao atualizar conteudo_site com id_conteudo = {$id_conteudo}. Verifique se o ID existe.");
                    // Não vamos cair para atualizar pela seção aqui. Lançamos erro.
                    throw new \Exception("Erro ao atualizar o registro de conteúdo específico (ID: {$id_conteudo}).");
                }
            } else {
                // Se NÃO veio um id_conteudo (pode ser um item antigo ou erro no form)
                // Vamos tentar INSERIR um novo conteúdo para esta seção/ordem.
                // É importante que o formulário de edição SEMPRE envie o id_conteudo se ele existir.
                // Se não existe, significa que precisa ser criado.
                $id_novo_conteudo = $this->imagemModel->inserirConteudo($id_secao, $titulo, $subtitulo, $texto, $ordem);
                $sucessoConteudo = ($id_novo_conteudo !== false);
                if (!$sucessoConteudo) {
                    throw new \Exception("Erro ao tentar inserir novo conteúdo associado.");
                } else {
                    // Log opcional para saber que um novo conteúdo foi inserido durante uma edição
                     error_log("Info: Novo conteúdo inserido (ID: {$id_novo_conteudo}) para seção {$id_secao} durante a edição da imagem ID {$id}.");
                }
            }
            // A exceção já foi lançada acima se $sucessoConteudo for false

            

            // Confirma transação
            $this->db->commit();

            // Deleta a imagem física antiga SOMENTE SE uma nova foi enviada e salva com sucesso
            if ($caminhoNovaImagem && !empty($caminhoImagemAntiga) && $caminhoImagemAntiga !== $caminhoNovaImagem) {
               $this->fileManager->delete($caminhoImagemAntiga);
            }

            Redirect::redirecionarComMensagem("imagens/listar", "success", "Imagem e conteúdo atualizados!");

        } catch (\Exception $e) {
            // Desfaz tudo
            $this->db->rollBack();
            // Se uma nova imagem foi salva fisicamente, deleta ela
            if ($caminhoNovaImagem) { $this->fileManager->delete($caminhoNovaImagem); }
            error_log("Erro atualizarImagem: " . $e->getMessage());
            Redirect::redirecionarComMensagem("imagens/editar/{$id}", "error", "Erro: " . $e->getMessage());
        }
    }

     // --- Método viewEditarImagem MODIFICADO ---
     public function viewEditarImagem(int $id) {
        $imagem = $this->imagemModel->buscarImagemPorId($id);
        if (!$imagem) {
            Redirect::redirecionarComMensagem("imagens/listar", "error", "Imagem não encontrada.");
            return;
        }

        // Busca o conteúdo associado usando o id_secao da imagem
        $conteudo = null;
        if (isset($imagem->id_secao)) {
             // Usando buscarConteudoPorSecaoId que retorna o PRIMEIRO conteúdo da seção (ou false)
            $conteudo = $this->imagemModel->buscarConteudoPorSecaoId((int)$imagem->id_secao);
        }

        // Passa ambos para a view
        View::render('imagem/edit', ['imagem' => $imagem, 'conteudo' => $conteudo]);
    }

    public function viewExcluirImagem(int $id) {
        $imagem = $this->imagemModel->buscarImagemPorId($id);
        if (!$imagem) {
            Redirect::redirecionarComMensagem("imagens/listar", "error", "Imagem não encontrada.");
            return;
        }
        View::render('imagem/delete', ['imagem' => $imagem]);
    }

    public function deletarImagem(int $id) {
        // --- ADICIONAR LÓGICA PARA DELETAR CONTEÚDO ASSOCIADO ---
        $imagem = $this->imagemModel->buscarImagemPorId($id);
        if (!$imagem) {
            Redirect::redirecionarComMensagem("imagens/listar", "error", "Imagem não encontrada para excluir.");
            return;
        }

        $caminhoArquivo = $imagem->url_imagem;
        $id_secao = $imagem->id_secao; // Guarda o ID da seção

        $this->db->beginTransaction();
        try {
            // Tenta marcar imagem como excluída (soft delete)
            $sucessoDBImagem = $this->imagemModel->deletarImagem($id);
            if (!$sucessoDBImagem) {
                 throw new \Exception("Erro ao marcar a imagem como excluída no banco de dados.");
            }
            $this->db->commit();

            // Tenta deletar o arquivo físico do servidor
            $sucessoArquivo = $this->fileManager->delete($caminhoArquivo);
            if (!$sucessoArquivo && !empty($caminhoArquivo)) {
                 error_log("Aviso: Imagem ID {$id} excluída no DB, mas falha ao excluir arquivo físico: {$caminhoArquivo}");
                 Redirect::redirecionarComMensagem("imagens/listar", "error", "Imagem removida do banco, mas problema ao excluir arquivo. Ver logs.");
                 return;
            }
            Redirect::redirecionarComMensagem("imagens/listar", "success", "Imagem e conteúdo associado excluídos com sucesso!");

        } catch (\Exception $e) {
             $this->db->rollBack();
             error_log("Erro deletarImagem: " . $e->getMessage());
             Redirect::redirecionarComMensagem("imagens/listar", "error", "Erro ao excluir: " . $e->getMessage());
        }
    }


    // --- APIs Públicas ---

     /**
      * API para listar imagens da seção "Quem Somos (Carrosel)" da página "Home".
      * (Corrigido para usar a função de busca SIMPLES do Model)
      */
     public function listarQuemSomos() {
         header('Content-Type: application/json');
         try {
             $paginaTable = $this->imagemModel->paginaTable;
             $secaoTable = $this->imagemModel->secaoTable;

             // 1. Encontrar o id_pagina da 'Home' (id=1)
             $sqlPagina = "SELECT id_pagina FROM {$paginaTable} WHERE nome_pagina = 'Home' LIMIT 1";
             $stmtPagina = $this->db->query($sqlPagina);
             $pagina = $stmtPagina->fetch(PDO::FETCH_OBJ);
             if (!$pagina) { throw new \Exception("Página 'Home' não encontrada."); }
             $idPaginaHome = $pagina->id_pagina;

             // 2. Encontrar o id_secao de 'Quem Somos (Carrosel)' na página 'Home' (id_secao=4)
             $nomeSecaoAlvo = 'Quem Somos (Carrosel)';
             $sqlSecao = "SELECT id_secao FROM {$secaoTable} WHERE id_pagina = :id_pagina AND nome_secao = :nome_secao AND excluido_em IS NULL LIMIT 1";
             $stmtSecao = $this->db->prepare($sqlSecao);
             $stmtSecao->bindParam(':id_pagina', $idPaginaHome, PDO::PARAM_INT);
             $stmtSecao->bindParam(':nome_secao', $nomeSecaoAlvo);
             $stmtSecao->execute();
             $secao = $stmtSecao->fetch(PDO::FETCH_OBJ);
             if (!$secao) { throw new \Exception("Seção '{$nomeSecaoAlvo}' não encontrada ou inativa para a página Home."); }
             $idSecaoQuemSomos = $secao->id_secao;

             // 3. Buscar imagens usando a função SIMPLES do Model
             $imagensObjs = $this->imagemModel->buscarImagensPorSecao($idSecaoQuemSomos);

             $urls = array_map(fn($img) => '/' . ltrim($img->url_imagem ?? '', '/'), $imagensObjs);

             http_response_code(200);
             echo json_encode($urls);

         } catch (\Exception $e) {
             http_response_code(500);
             error_log("Erro API listarQuemSomos: " . $e->getMessage());
             echo json_encode(['error' => 'Erro interno ao buscar imagens Quem Somos: ' . $e->getMessage()]);
         }
     }

     /**
      * API para listar os serviços (imagem, título e texto) para a página inicial.
      */
     public function listarServicos() {
            header('Content-Type: application/json');
            try {
                $paginaTable = $this->imagemModel->paginaTable;
                $secaoTable = $this->imagemModel->secaoTable;

                // 1. Encontrar o id_pagina da página 'Home' (onde está a seção de serviços)
                $sqlPagina = "SELECT id_pagina FROM {$paginaTable} WHERE nome_pagina = 'Home' LIMIT 1";
                $stmtPagina = $this->db->query($sqlPagina);
                $pagina = $stmtPagina->fetch(PDO::FETCH_OBJ);
                if (!$pagina) { throw new \Exception("Página 'Home' não encontrada para buscar os serviços."); }
                $idPaginaHome = $pagina->id_pagina;

                // 2. Encontrar o id_secao de 'Serviços em Destaque' na página 'Home' (id_secao=5)
                $nomeSecaoAlvo = 'Serviços em Destaque'; // <<< NOME CORRIGIDO CONFORME DB
                $sqlSecao = "SELECT id_secao FROM {$this->imagemModel->secaoTable} WHERE id_pagina = :id_pagina AND nome_secao = :nome_secao AND excluido_em IS NULL LIMIT 1";
                $stmtSecao = $this->db->prepare($sqlSecao);
                $stmtSecao->bindParam(':id_pagina', $idPaginaHome, PDO::PARAM_INT);
                $stmtSecao->bindParam(':nome_secao', $nomeSecaoAlvo);
                $stmtSecao->execute();
                $secao = $stmtSecao->fetch(PDO::FETCH_OBJ);
                if (!$secao) { throw new \Exception("Seção '{$nomeSecaoAlvo}' não encontrada ou inativa para a página Home."); }
                $idSecaoServicos = $secao->id_secao;

                // 3. Buscar os dados completos usando a função com JOIN do Model
                $servicosObjs = $this->imagemModel->buscarDetalhesServicosPorSecao($idSecaoServicos);

                $dataParaJson = [];
                foreach ($servicosObjs as $servico) {
                    $caminhoImagemCorrigido = '/' . ltrim($servico->url_imagem ?? '', '/');
                    $dataParaJson[] = [
                        'caminho_imagem' => $caminhoImagemCorrigido,
                        'nome_servico' => $servico->nome_servico ?? 'Serviço Indisponível',
                        'descricao_servico' => $servico->descricao_servico ?? 'Descrição não disponível.'
                    ];
                }

                http_response_code(200);
                echo json_encode(['status' => 'success', 'data' => $dataParaJson]);

            } catch (\Exception $e) {
                http_response_code(500);
                error_log("Erro API listarServicos: " . $e->getMessage());
                echo json_encode(['status' => 'error', 'message' => 'Erro interno ao buscar serviços: ' . $e->getMessage()]);
            }
     }
} // Fim da classe ImagemController