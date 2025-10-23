<?php
use App\Psico\Core\Flash;
$imagem = $dados['imagem']; // Objeto imagem com url_imagem, ordem, nome_pagina, nome_secao, id_secao
// Assumindo que você buscou o conteúdo associado no Controller e passou como $dados['conteudo']
$conteudo = $dados['conteudo'] ?? null; // Objeto ou array com titulo_secao, subtitulo, texto, id_conteudo
?>

<div class="w3-container w3-white w3-text-grey w3-card-4" style="padding-bottom: 32px;">
    <h2 class="w3-text-grey w3-padding-16"><i class="fa fa-pencil fa-fw w3-margin-right w3-xxlarge" style="color: #A3B8A1;"></i>Editar Imagem e Conteúdo (ID Imagem: <?= htmlspecialchars($imagem->id_imagem) ?>)</h2>

    <?= Flash::getFlash() ?>

    <div class="w3-container">
        <form action="/backend/imagens/atualizar/<?= htmlspecialchars($imagem->id_imagem) ?>" method="POST" enctype="multipart/form-data">
            
            <?php if ($conteudo && isset($conteudo->id_conteudo)): ?>
                <input type="hidden" name="id_conteudo" value="<?= htmlspecialchars($conteudo->id_conteudo) ?>">
            <?php endif; ?>
             <input type="hidden" name="id_secao" value="<?= htmlspecialchars($imagem->id_secao) ?>">


            <h4 style="color: #5D6D68;"><i class="fa fa-picture-o"></i> Detalhes da Imagem</h4>

            <div class="w3-row-padding w3-section">
                <div class="w3-half">
                    <label><b><i class="fa fa-sitemap"></i> Página Atual</b></label>
                    <input class="w3-input w3-border w3-light-grey" type="text" value="<?= htmlspecialchars($imagem->nome_pagina ?? 'N/A') ?>" readonly disabled>
                </div>
                <div class="w3-half">
                    <label><b><i class="fa fa-folder-open"></i> Seção Atual</b></label>
                    <input class="w3-input w3-border w3-light-grey" type="text" value="<?= htmlspecialchars($imagem->nome_secao ?? 'N/A') ?>" readonly disabled>
                </div>
            </div>

            <div class="w3-row-padding w3-section">
                <div class="w3-half">
                    <label for="ordem"><b><i class="fa fa-sort-numeric-asc"></i> Ordem de Exibição (Imagem/Conteúdo)</b></label>
                    <input class="w3-input w3-border" type="number" id="ordem" name="ordem" value="<?= htmlspecialchars($imagem->ordem ?? 99) ?>" min="1">
                    <small>Menor número aparece primeiro.</small>
                </div>
                <div class="w3-half">
                     <label for="arquivo_imagem"><b><i class="fa fa-upload"></i> Alterar Arquivo (Opcional)</b></label>
                     <input class="w3-input w3-border" type="file" id="arquivo_imagem" name="arquivo_imagem" accept="image/jpeg, image/png, image/webp, image/gif">
                     <small>Deixe em branco para manter a imagem atual. (Máx 2MB).</small>
                </div>
            </div>

            <div class="w3-row-padding w3-section">
                <div class="w3-full">
                     <?php if (!empty($imagem->url_imagem)): ?>
                        <div style="margin-top: 15px;">
                            <label><b>Imagem Atual:</b></label><br>
                            <img src="/<?= htmlspecialchars($imagem->url_imagem) ?>" alt="Imagem Atual" style="max-width: 200px; height: auto; border: 1px solid #ccc; margin-top: 5px;">
                            <input type="hidden" name="imagem_atual_url" value="<?= htmlspecialchars($imagem->url_imagem) ?>">
                        </div>
                     <?php endif; ?>
                </div>
             </div>

            <hr style="border-top: 1px solid #ccc; margin: 30px 0;">

             <h4 style="color: #5D6D68;"><i class="fa fa-file-text-o"></i> Conteúdo Associado (tabela conteudo_site)</h4>

             <div class="w3-row-padding w3-section">
                 <div class="w3-half">
                     <label for="titulo_secao"><b><i class="fa fa-header"></i> Título / Nome do Serviço</b></label>
                     <input class="w3-input w3-border" type="text" id="titulo_secao" name="titulo_secao" placeholder="Ex: Psicoterapia Individual" value="<?= htmlspecialchars($conteudo->titulo_secao ?? '') ?>" required>
                 </div>
                  <div class="w3-half">
                     <label for="subtitulo"><b><i class="fa fa-tag"></i> Subtítulo (Opcional)</b></label>
                      <input class="w3-input w3-border" type="text" id="subtitulo" name="subtitulo" placeholder="Ex: Para Adultos" value="<?= htmlspecialchars($conteudo->subtitulo ?? '') ?>">
                 </div>
             </div>

             <div class="w3-row-padding w3-section">
                 <div class="w3-full">
                     <label for="texto"><b><i class="fa fa-align-left"></i> Texto / Descrição</b></label>
                      <textarea class="w3-input w3-border" id="texto" name="texto" rows="4" placeholder="Descreva o serviço ou conteúdo aqui..." required><?= htmlspecialchars($conteudo->texto ?? '') ?></textarea>
                 </div>
             </div>

            <button type="submit" class="w3-button w3-right w3-padding" style="background-color: #A3B8A1 !important;">Atualizar Imagem e Conteúdo</button>
            <a href="/backend/imagens/listar" class="w3-button w3-right w3-padding w3-light-grey w3-margin-right">Cancelar</a>
        </form>
    </div>
</div>