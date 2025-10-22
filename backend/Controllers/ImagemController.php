<?php
namespace App\Psico\Controllers;

use App\Psico\Models\ImagemSite;
use App\Psico\Database\Database;
use App\Psico\Core\View;
use App\Psico\Core\Redirect;
use App\Psico\Core\FileManager;
use App\Psico\Controllers\Admin\AuthenticatedController;
use PDO;

class ImagemController extends AuthenticatedController {

    private ImagemSite $imagemModel;
    private FileManager $fileManager;
    private string $diretorioUpload = 'img/site';
    private PDO $db;

    public function __construct() {
        // parent::__construct();
        $this->db = Database::getInstance();
        $this->imagemModel = new ImagemSite($this->db);
        $this->fileManager = new FileManager(__DIR__ . '/../../');
    }

    // ... (viewListarImagens, viewCriarImagem, buscarSecoesPorPaginaApi, salvarImagem, viewEditarImagem, atualizarImagem, viewExcluirImagem, deletarImagem - permanecem iguais à versão anterior) ...
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
        if (empty($_POST['id_pagina']) || empty($_POST['id_secao']) || !isset($_FILES['arquivo_imagem']) || $_FILES['arquivo_imagem']['error'] != UPLOAD_ERR_OK) {
             Redirect::redirecionarComMensagem("imagens/criar", "error", "Selecione a Página, a Seção e envie um arquivo de imagem válido.");
             return;
        }
        $caminhoImagemSalva = null;
        try {
            $caminhoImagemSalva = $this->fileManager->salvarArquivo(
                $_FILES['arquivo_imagem'],
                $this->diretorioUpload,
                ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
                2 * 1024 * 1024
            );
            $id_secao = (int)$_POST['id_secao'];
            $ordem = isset($_POST['ordem']) ? (int)$_POST['ordem'] : 99;
            $id_inserido = $this->imagemModel->inserirImagem($id_secao, $caminhoImagemSalva, $ordem);

            if ($id_inserido) {
                Redirect::redirecionarComMensagem("imagens/listar", "success", "Imagem adicionada com sucesso!");
            } else {
                if($caminhoImagemSalva) $this->fileManager->delete($caminhoImagemSalva);
                Redirect::redirecionarComMensagem("imagens/criar", "error", "Erro ao salvar informações da imagem no banco de dados.");
            }
        } catch (\Exception $e) {
             if ($caminhoImagemSalva) { $this->fileManager->delete($caminhoImagemSalva); }
            Redirect::redirecionarComMensagem("imagens/criar", "error", "Erro: " . $e->getMessage());
        }
    }

    public function viewEditarImagem(int $id) {
        $imagem = $this->imagemModel->buscarImagemPorId($id);
        if (!$imagem) {
            Redirect::redirecionarComMensagem("imagens/listar", "error", "Imagem não encontrada.");
            return;
        }
        View::render('imagem/edit', ['imagem' => $imagem]);
    }

    public function atualizarImagem(int $id) {
        $imagemAtual = $this->imagemModel->buscarImagemPorId($id);
        if (!$imagemAtual) {
            Redirect::redirecionarComMensagem("imagens/listar", "error", "Imagem não encontrada para atualizar.");
            return;
        }
        $ordem = isset($_POST['ordem']) ? (int)$_POST['ordem'] : $imagemAtual->ordem;
        $caminhoNovaImagem = null;
        $caminhoImagemAntiga = $imagemAtual->url_imagem;

        if (isset($_FILES['arquivo_imagem']) && $_FILES['arquivo_imagem']['error'] == UPLOAD_ERR_OK) {
            try {
                $caminhoNovaImagem = $this->fileManager->salvarArquivo(
                     $_FILES['arquivo_imagem'],
                     $this->diretorioUpload,
                     ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
                     2 * 1024 * 1024
                 );
            } catch (\Exception $e) {
                 Redirect::redirecionarComMensagem("imagens/editar/{$id}", "error", "Erro no upload da nova imagem: " . $e->getMessage());
                 return;
             }
        }
        $urlParaSalvar = $caminhoNovaImagem;
        try {
            $sucesso = $this->imagemModel->atualizarImagem($id, $urlParaSalvar, $ordem);
            if ($sucesso) {
                 if ($caminhoNovaImagem && !empty($caminhoImagemAntiga) && $caminhoImagemAntiga !== $caminhoNovaImagem) {
                    $this->fileManager->delete($caminhoImagemAntiga);
                }
                Redirect::redirecionarComMensagem("imagens/listar", "success", "Imagem atualizada com sucesso!");
            } else {
                 if ($caminhoNovaImagem) { $this->fileManager->delete($caminhoNovaImagem); }
                Redirect::redirecionarComMensagem("imagens/editar/{$id}", "error", "Erro ao atualizar informações da imagem.");
            }
        } catch (\Exception $e) {
            if ($caminhoNovaImagem) { $this->fileManager->delete($caminhoNovaImagem); }
             Redirect::redirecionarComMensagem("imagens/editar/{$id}", "error", "Erro durante a atualização: " . $e->getMessage());
        }
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
        $imagem = $this->imagemModel->buscarImagemPorId($id);
        if (!$imagem) {
            Redirect::redirecionarComMensagem("imagens/listar", "error", "Imagem não encontrada para excluir.");
            return;
        }
        $caminhoArquivo = $imagem->url_imagem;
        $sucessoDB = $this->imagemModel->deletarImagem($id);

        if ($sucessoDB) {
            $sucessoArquivo = $this->fileManager->delete($caminhoArquivo);
            if (!$sucessoArquivo) {
                 Redirect::redirecionarComMensagem("imagens/listar", "error", "Imagem removida do banco, mas falha ao excluir o arquivo do servidor.");
                 return;
            }
            Redirect::redirecionarComMensagem("imagens/listar", "success", "Imagem excluída com sucesso!");
        } else {
            Redirect::redirecionarComMensagem("imagens/listar", "error", "Erro ao marcar a imagem como excluída no banco de dados.");
        }
    }


     /**
      * API para listar imagens da seção "Quem Somos (Carrosel)" da página "Home".
      * Refatorado para usar a estrutura correta do DER.
      */
     public function listarQuemSomos() {
         header('Content-Type: application/json');
         try {
             // --- CORREÇÃO APLICADA AQUI ---
             // Acessa as propriedades paginaTable e secaoTable através do $this->imagemModel
             $paginaTable = $this->imagemModel->paginaTable; // Nome da tabela de páginas vindo do Model
             $secaoTable = $this->imagemModel->secaoTable;   // Nome da tabela de seções vindo do Model

             // 1. Encontrar o id_pagina da 'Home' (Ajuste 'Home' se o nome for diferente)
             $sqlPagina = "SELECT id_pagina FROM {$paginaTable} WHERE nome_pagina = 'Home' LIMIT 1";
             $stmtPagina = $this->db->query($sqlPagina);
             $pagina = $stmtPagina->fetch(PDO::FETCH_OBJ);

             if (!$pagina) { throw new \Exception("Página 'Home' não encontrada na tabela '{$paginaTable}'."); }
             $idPaginaHome = $pagina->id_pagina;

             // 2. Encontrar o id_secao de 'Quem Somos (Carrosel)' DENTRO da página 'Home' (Ajuste o nome se necessário)
             $sqlSecao = "SELECT id_secao FROM {$secaoTable} WHERE id_pagina = :id_pagina AND nome_secao = 'Quem Somos (Carrosel)' AND excluido_em IS NULL LIMIT 1";
             $stmtSecao = $this->db->prepare($sqlSecao);
             $stmtSecao->bindParam(':id_pagina', $idPaginaHome, PDO::PARAM_INT);
             $stmtSecao->execute();
             $secao = $stmtSecao->fetch(PDO::FETCH_OBJ);

             if (!$secao) { throw new \Exception("Seção 'Quem Somos (Carrosel)' não encontrada ou inativa na tabela '{$secaoTable}' para a página Home."); }
             $idSecaoQuemSomos = $secao->id_secao;
             // --- FIM DA CORREÇÃO ---

             // 3. Buscar imagens usando o id_secao encontrado
             $imagensObjs = $this->imagemModel->buscarImagensPorSecao($idSecaoQuemSomos);
             $urls = array_map(fn($img) => $img->url_imagem, $imagensObjs);

             http_response_code(200);
             echo json_encode($urls);

         } catch (\Exception $e) {
             http_response_code(500);
             error_log("Erro API listarQuemSomos: " . $e->getMessage());
             echo json_encode(['error' => 'Erro interno ao buscar imagens: ' . $e->getMessage()]);
         }
     }

} // Fim da classe ImagemController