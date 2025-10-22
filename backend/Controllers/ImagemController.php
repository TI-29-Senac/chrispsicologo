<?php
namespace App\Psico\Controllers;

use App\Psico\Models\ImagemSite;
use App\Psico\Database\Database;
use App\Psico\Core\View;
use App\Psico\Core\Redirect;
use App\Psico\Core\FileManager;
use App\Psico\Controllers\Admin\AuthenticatedController;


class ImagemController extends AuthenticatedController {

    
    private ImagemSite $imagemModel;
    private FileManager $fileManager;
    private string $diretorioUpload = 'img/site';

    public function __construct() {
        
        $db = Database::getInstance();
        $this->imagemModel = new ImagemSite($db);
        $this->fileManager = new FileManager(__DIR__ . '/../../');
    }

    
    public function viewListarImagens() {
        $imagensAgrupadas = $this->imagemModel->buscarTodasAgrupadasPorSecao();
        View::render('imagem/index', ['imagensAgrupadas' => $imagensAgrupadas]);
    }



    public function viewCriarImagem() {
        $todasSecoes = $this->imagemModel->buscarSecoesDisponiveis();

        $paginasPrincipais = [];
        $secoesFilhasPorPagina = []; 

        
        $paginaPaiNome = 'Home'; 

        foreach ($todasSecoes as $secao) {
            
            $nomeSecao = $secao->nome_pagina;
            $idSecao = $secao->id_pagina;

            
            if (strpos($nomeSecao, $paginaPaiNome . ' - ') === 0) {
                
                $nomeFilha = str_replace($paginaPaiNome . ' - ', '', $nomeSecao);
                if (!isset($secoesFilhasPorPagina[$paginaPaiNome])) {
                    $secoesFilhasPorPagina[$paginaPaiNome] = [];
                }
                $secoesFilhasPorPagina[$paginaPaiNome][] = ['id' => $idSecao, 'nome' => $nomeFilha];
            } else {
                
                $paginasPrincipais[] = ['id' => $idSecao, 'nome' => $nomeSecao];
            }
        }

        
        $paginaPaiEncontrada = false;
        foreach($paginasPrincipais as $p) {
            if ($p['nome'] === $paginaPaiNome) {
                $paginaPaiEncontrada = true;
                break;
            }
        }
        
        
        
        if (!$paginaPaiEncontrada && !empty($secoesFilhasPorPagina[$paginaPaiNome])) {
             
             
             foreach ($todasSecoes as $secao) {
                 if ($secao->nome_pagina === $paginaPaiNome) {
                     array_unshift($paginasPrincipais, ['id' => $secao->id_pagina, 'nome' => $secao->nome_pagina]); 
                     break;
                 }
             }
        }


        View::render('imagem/create', [
            'paginasPrincipais' => $paginasPrincipais,
            
            'secoesFilhasJson' => json_encode($secoesFilhasPorPagina)
        ]);
    }

    
     
    public function salvarImagem() {
        
         
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

        
        $todasSecoes = $this->imagemModel->buscarSecoesDisponiveis();
        $paginasPrincipais = [];
        $secoesFilhasPorPagina = [];
        $paginaPaiNome = 'Home'; 
        $idPaginaPaiSelecionada = null;
        $idSecaoFilhaSelecionada = null;
        $paginaPaiEncontrada = false; 

        
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

                
                if ($eSubsecao && $idSecao == $idSecaoAtual) {
                    $idSecaoFilhaSelecionada = $idSecao;
                }
            } else {
                $paginasPrincipais[] = ['id' => $idSecao, 'nome' => $nomeSecao];
                
                if (!$eSubsecao && $idSecao == $idSecaoAtual) {
                     $idPaginaPaiSelecionada = $idSecao;
                }
                 
                 if ($eSubsecao && $nomeSecao === $paginaPaiNome) {
                      $idPaginaPaiSelecionada = $idSecao;
                 }
                 
                 if ($nomeSecao === $paginaPaiNome) {
                    $paginaPaiEncontrada = true; 
                 }
            }
        }
         
         if (!$paginaPaiEncontrada && !empty($secoesFilhasPorPagina[$paginaPaiNome])) {
              foreach ($todasSecoes as $secao) {
                  if ($secao->nome_pagina === $paginaPaiNome) {
                      array_unshift($paginasPrincipais, ['id' => $secao->id_pagina, 'nome' => $secao->nome_pagina]);
                      
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
             $idSecaoQuemSomos = 4; 
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