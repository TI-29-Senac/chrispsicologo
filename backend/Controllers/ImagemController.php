<?php
namespace App\Psico\Controllers;

// ... (usos existentes) ...
use App\Psico\Models\ImagemSite;
use App\Psico\Database\Database;
use App\Psico\Core\View;
use App\Psico\Core\Redirect;
use App\Psico\Core\FileManager;
use App\Psico\Controllers\Admin\AuthenticatedController;


class ImagemController extends AuthenticatedController {

    // ... (propriedades e construtor existentes) ...
    private ImagemSite $imagemModel;
    private FileManager $fileManager;
    private string $diretorioUpload = 'img/site';

    public function __construct() {
        // parent::__construct();
        $db = Database::getInstance();
        $this->imagemModel = new ImagemSite($db);
        $this->fileManager = new FileManager(__DIR__ . '/../../');
    }

    // ... (viewListarImagens existente) ...
    public function viewListarImagens() {
        $imagensAgrupadas = $this->imagemModel->buscarTodasAgrupadasPorSecao();
        View::render('imagem/index', ['imagensAgrupadas' => $imagensAgrupadas]);
    }


    /**
     * Exibe o formulário para adicionar uma nova imagem, com lógica para dropdowns dependentes.
     */
    public function viewCriarImagem() {
        $todasSecoes = $this->imagemModel->buscarSecoesDisponiveis();

        $paginasPrincipais = [];
        $secoesFilhasPorPagina = []; // Mapeamento para o JavaScript

        // Define o nome da página pai que terá subseções
        $paginaPaiNome = 'Home'; // <<< AJUSTE SE O NOME FOR DIFERENTE NO SEU BANCO

        foreach ($todasSecoes as $secao) {
            // Ajuste 'nome_pagina' e 'id_pagina' se os nomes das colunas forem diferentes
            $nomeSecao = $secao->nome_pagina;
            $idSecao = $secao->id_pagina;

            // Verifica se é uma subseção da página principal (ex: começa com "Home - ")
            if (strpos($nomeSecao, $paginaPaiNome . ' - ') === 0) {
                // Remove o prefixo para exibir no dropdown filho (ex: "Quem Somos Carrossel")
                $nomeFilha = str_replace($paginaPaiNome . ' - ', '', $nomeSecao);
                if (!isset($secoesFilhasPorPagina[$paginaPaiNome])) {
                    $secoesFilhasPorPagina[$paginaPaiNome] = [];
                }
                $secoesFilhasPorPagina[$paginaPaiNome][] = ['id' => $idSecao, 'nome' => $nomeFilha];
            } else {
                // É uma página principal ou uma seção que não depende de outra
                $paginasPrincipais[] = ['id' => $idSecao, 'nome' => $nomeSecao];
            }
        }

        // Garante que a página pai principal (ex: "Home") esteja na lista, caso exista como entrada separada
        $paginaPaiEncontrada = false;
        foreach($paginasPrincipais as $p) {
            if ($p['nome'] === $paginaPaiNome) {
                $paginaPaiEncontrada = true;
                break;
            }
        }
        // Se a página "Home" não foi adicionada (porque só existem seções filhas dela),
        // busca o ID dela especificamente para adicionar ao dropdown principal.
        // Isso assume que existe uma entrada "Home" na tabela pagina_site.
        if (!$paginaPaiEncontrada && !empty($secoesFilhasPorPagina[$paginaPaiNome])) {
             // Você precisaria de um método no Model ou fazer a query aqui para buscar o ID da "Home"
             // Exemplo simplificado (idealmente seria $this->imagemModel->buscarSecaoPorNome($paginaPaiNome)):
             foreach ($todasSecoes as $secao) {
                 if ($secao->nome_pagina === $paginaPaiNome) {
                     array_unshift($paginasPrincipais, ['id' => $secao->id_pagina, 'nome' => $secao->nome_pagina]); // Adiciona no início
                     break;
                 }
             }
        }


        View::render('imagem/create', [
            'paginasPrincipais' => $paginasPrincipais,
            // Passa o mapeamento como JSON para o JavaScript usar
            'secoesFilhasJson' => json_encode($secoesFilhasPorPagina)
        ]);
    }

    // ... (salvarImagem, viewEditarImagem, atualizarImagem, viewExcluirImagem, deletarImagem, listarQuemSomos existentes) ...
     // Salvar imagem não precisa mudar muito, pois o <select name="id_secao"> final terá o ID correto.
    public function salvarImagem() {
        // Validação básica
         // A validação agora deve checar 'id_secao' que virá do dropdown final (ou do único, se não for Home)
        if (empty($_POST['id_secao']) || !isset($_FILES['arquivo_imagem']) || $_FILES['arquivo_imagem']['error'] != UPLOAD_ERR_OK) {
             Redirect::redirecionarComMensagem("imagens/criar", "error", "Selecione a página/seção final e envie um arquivo de imagem válido.");
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

            // Usa o id_secao que veio do formulário (que será o ID da seção filha ou da página principal)
            $id_secao = (int)$_POST['id_secao'];
            $ordem = isset($_POST['ordem']) ? (int)$_POST['ordem'] : 99;

            $id_inserido = $this->imagemModel->inserirImagem($id_secao, $caminhoImagemSalva, $ordem);

            if ($id_inserido) {
                Redirect::redirecionarComMensagem("imagens/listar", "success", "Imagem adicionada com sucesso!");
            } else {
                $this->fileManager->delete($caminhoImagemSalva);
                Redirect::redirecionarComMensagem("imagens/criar", "error", "Erro ao salvar informações da imagem no banco de dados.");
            }

        } catch (\Exception $e) {
             if ($caminhoImagemSalva) {
                 $this->fileManager->delete($caminhoImagemSalva);
             }
            Redirect::redirecionarComMensagem("imagens/criar", "error", "Erro: " . $e->getMessage());
        }
    }

    public function viewEditarImagem(int $id) {
        $imagem = $this->imagemModel->buscarImagemPorId($id);
        if (!$imagem) {
            Redirect::redirecionarComMensagem("imagens/listar", "error", "Imagem não encontrada.");
            return;
        }

        // Lógica similar à viewCriarImagem para preparar os dropdowns
        $todasSecoes = $this->imagemModel->buscarSecoesDisponiveis();
        $paginasPrincipais = [];
        $secoesFilhasPorPagina = [];
        $paginaPaiNome = 'Home'; // <<< AJUSTE SE NECESSÁRIO
        $idPaginaPaiSelecionada = null;
        $idSecaoFilhaSelecionada = null;
        $paginaPaiEncontrada = false; // <<< INICIALIZAÇÃO ADICIONADA AQUI

        // Determina se a imagem atual pertence a uma página pai ou a uma subseção
        $nomeSecaoAtual = $imagem->nome_secao ?? '';
        $idSecaoAtual = $imagem->id_secao;

        $eSubsecao = strpos($nomeSecaoAtual, $paginaPaiNome . ' - ') === 0;

        foreach ($todasSecoes as $secao) {
            $nomeSecao = $secao->nome_pagina;
            $idSecao = $secao->id_pagina;

            if (strpos($nomeSecao, $paginaPaiNome . ' - ') === 0) {
                $nomeFilha = str_replace($paginaPaiNome . ' - ', '', $nomeSecao);
                if (!isset($secoesFilhasPorPagina[$paginaPaiNome])) {
                    $secoesFilhasPorPagina[$paginaPaiNome] = [];
                }
                $secoesFilhasPorPagina[$paginaPaiNome][] = ['id' => $idSecao, 'nome' => $nomeFilha];

                // Se a imagem atual é desta subseção, marca o ID
                if ($eSubsecao && $idSecao == $idSecaoAtual) {
                    $idSecaoFilhaSelecionada = $idSecao;
                }
            } else {
                $paginasPrincipais[] = ['id' => $idSecao, 'nome' => $nomeSecao];
                // Se a imagem atual pertence a esta página principal, marca o ID
                if (!$eSubsecao && $idSecao == $idSecaoAtual) {
                     $idPaginaPaiSelecionada = $idSecao;
                }
                 // Se a imagem atual é subseção, precisamos encontrar o ID da página pai "Home" para pré-selecionar
                 if ($eSubsecao && $nomeSecao === $paginaPaiNome) {
                      $idPaginaPaiSelecionada = $idSecao;
                 }
                 // Marca que a página pai foi encontrada na lista principal
                 if ($nomeSecao === $paginaPaiNome) {
                    $paginaPaiEncontrada = true; // <<< Marca como encontrada
                 }
            }
        }
         // Garante que "Home" esteja na lista principal se tiver filhas E não tiver sido adicionada antes
         if (!$paginaPaiEncontrada && !empty($secoesFilhasPorPagina[$paginaPaiNome])) {
              foreach ($todasSecoes as $secao) {
                  if ($secao->nome_pagina === $paginaPaiNome) {
                      array_unshift($paginasPrincipais, ['id' => $secao->id_pagina, 'nome' => $secao->nome_pagina]);
                      // Se a imagem for subseção, marca o ID pai aqui se não foi marcado antes
                      if ($eSubsecao && !$idPaginaPaiSelecionada) {
                           $idPaginaPaiSelecionada = $secao->id_pagina;
                      }
                      break;
                  }
              }
         }


        View::render('imagem/edit', [
            'imagem' => $imagem,
            'paginasPrincipais' => $paginasPrincipais,
            'secoesFilhasJson' => json_encode($secoesFilhasPorPagina),
            'idPaginaPaiSelecionada' => $idPaginaPaiSelecionada,
            'idSecaoFilhaSelecionada' => $idSecaoFilhaSelecionada,
            'paginaPaiNome' => $paginaPaiNome
        ]);
    }

    // Atualizar não precisa mudar muito, pois o name="id_secao" virá preenchido corretamente pelo JS
     public function atualizarImagem(int $id) {
        $imagemAtual = $this->imagemModel->buscarImagemPorId($id);
        if (!$imagemAtual) {
            Redirect::redirecionarComMensagem("imagens/listar", "error", "Imagem não encontrada para atualizar.");
            return;
        }

        // A seção não pode ser alterada na edição, apenas ordem e arquivo
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

        $urlParaSalvar = $caminhoNovaImagem; // null se não houver nova imagem

        try {
            // A atualização no Model só precisa da URL nova (ou null) e da ordem
            $sucesso = $this->imagemModel->atualizarImagem($id, $urlParaSalvar, $ordem);

            if ($sucesso) {
                if ($caminhoNovaImagem && !empty($caminhoImagemAntiga) && $caminhoImagemAntiga !== $caminhoNovaImagem) {
                    $this->fileManager->delete($caminhoImagemAntiga);
                }
                Redirect::redirecionarComMensagem("imagens/listar", "success", "Imagem atualizada com sucesso!");
            } else {
                 if ($caminhoNovaImagem) {
                     $this->fileManager->delete($caminhoNovaImagem);
                 }
                Redirect::redirecionarComMensagem("imagens/editar/{$id}", "error", "Erro ao atualizar informações da imagem no banco de dados.");
            }
        } catch (\Exception $e) {
              if ($caminhoNovaImagem) {
                  $this->fileManager->delete($caminhoNovaImagem);
              }
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

     public function listarQuemSomos() {
         header('Content-Type: application/json');
         try {
             $idSecaoQuemSomos = 4; // <<< AJUSTE ID CONFORME SEU BANCO
             $imagensObjs = $this->imagemModel->buscarImagensPorSecao($idSecaoQuemSomos);
             $urls = array_map(fn($img) => $img->url_imagem, $imagensObjs);

             if (empty($urls)) {
                  http_response_code(404);
                  echo json_encode(['error' => 'Nenhuma imagem encontrada para esta seção.']);
                  return;
             }
             http_response_code(200);
             echo json_encode($urls);

         } catch (\Exception $e) {
             http_response_code(500);
             error_log("Erro API listarQuemSomos: " . $e->getMessage());
             echo json_encode(['error' => 'Erro interno ao buscar imagens.']);
         }
     }
}