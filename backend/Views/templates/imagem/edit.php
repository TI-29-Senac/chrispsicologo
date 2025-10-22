<?php
use App\Psico\Core\Flash;
$imagem = $dados['imagem'];
// Variáveis passadas pelo Controller para pré-seleção
$idPaginaPaiSelecionada = $dados['idPaginaPaiSelecionada'] ?? null;
$idSecaoFilhaSelecionada = $dados['idSecaoFilhaSelecionada'] ?? null;
$paginaPaiNome = $dados['paginaPaiNome'] ?? 'Home'; // Nome da página pai com filhos
?>

<div class="w3-container w3-white w3-text-grey w3-card-4" style="padding-bottom: 32px;">
    <h2 class="w3-text-grey w3-padding-16"><i class="fa fa-pencil fa-fw w3-margin-right w3-xxlarge" style="color: #A3B8A1;"></i>Editar Imagem (ID: <?= htmlspecialchars($imagem->id_imagem) ?>)</h2>

    <?= Flash::getFlash() ?>

    <div class="w3-container">
        <form action="/backend/imagens/atualizar/<?= htmlspecialchars($imagem->id_imagem) ?>" method="POST" enctype="multipart/form-data">

            <div class="w3-row-padding w3-section">
                <div class="w3-half">
                    <label for="id_pagina_select"><b><i class="fa fa-sitemap"></i> Página Principal</b></label>
                    <select class="w3-select w3-border w3-light-grey" id="id_pagina_select" name="id_pagina_select_disabled" disabled>
                        <option value="">...</option>
                        <?php if (!empty($paginasPrincipais)): ?>
                            <?php foreach ($paginasPrincipais as $pagina): ?>
                                <option value="<?= htmlspecialchars($pagina['id']) ?>"
                                        data-nome-pagina="<?= htmlspecialchars($pagina['nome']) ?>"
                                        <?= ($pagina['id'] == $idPaginaPaiSelecionada) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($pagina['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                     <small>A página/seção não pode ser alterada após a criação.</small>
                     <input type="hidden" name="id_secao_original" value="<?= htmlspecialchars($imagem->id_secao) ?>">
                </div>


                <div class="w3-half" id="container-secao-filha" style="display: none;">
                    <label for="id_secao_display"><b><i class="fa fa-folder-open"></i> Seção Específica</b></label>
                    <select class="w3-select w3-border w3-light-grey" id="id_secao_display" name="id_secao_display_disabled" disabled>
                    </select>
                </div>
            </div>

            <div class="w3-row-padding w3-section">
                <div class="w3-half">
                    <label for="ordem"><b><i class="fa fa-sort-numeric-asc"></i> Ordem de Exibição</b></label>
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
                        </div>
                     <?php endif; ?>
                </div>
             </div>

            <button type="submit" class="w3-button w3-right w3-padding" style="background-color: #A3B8A1 !important;">Atualizar Imagem</button>
            <a href="/backend/imagens/listar" class="w3-button w3-right w3-padding w3-light-grey w3-margin-right">Cancelar</a>
        </form>
    </div>
</div>

<script>
    // Mapeamento e IDs pré-selecionados passados pelo PHP
    const secoesFilhasPorPagina = <?= $secoesFilhasJson ?? '{}' ?>;
    const paginaPaiComFilhas = '<?= $paginaPaiNome ?>'; // Nome da página pai
    const idPaginaPaiSelecionada = <?= json_encode($idPaginaPaiSelecionada) ?>;
    const idSecaoFilhaSelecionada = <?= json_encode($idSecaoFilhaSelecionada) ?>;

    const selectPagina = document.getElementById('id_pagina_select'); // O select visível (agora desabilitado)
    const containerSecaoFilha = document.getElementById('container-secao-filha');
    const selectSecaoFilhaDisplay = document.getElementById('id_secao_display'); // O select filho visível (desabilitado)

    // Função para configurar o estado inicial (executada na carga)
    function configurarEstadoInicial() {
        const nomePaginaSelecionada = selectPagina.options[selectPagina.selectedIndex]?.getAttribute('data-nome-pagina');

        if (nomePaginaSelecionada === paginaPaiComFilhas && secoesFilhasPorPagina[paginaPaiComFilhas]) {
            containerSecaoFilha.style.display = 'block';
            selectSecaoFilhaDisplay.innerHTML = '<option value="" disabled>...</option>'; // Limpa e adiciona placeholder

            // Popula com as seções filhas
            secoesFilhasPorPagina[paginaPaiComFilhas].forEach(secao => {
                const option = document.createElement('option');
                option.value = secao.id;
                option.textContent = secao.nome;
                // Pré-seleciona a seção filha correta
                if (secao.id == idSecaoFilhaSelecionada) {
                    option.selected = true;
                }
                selectSecaoFilhaDisplay.appendChild(option);
            });
        } else {
            containerSecaoFilha.style.display = 'none';
             selectSecaoFilhaDisplay.innerHTML = ''; // Limpa se não for aplicável
        }
    }

    // Configura o estado inicial quando o DOM estiver pronto
    document.addEventListener('DOMContentLoaded', configurarEstadoInicial);

</script>