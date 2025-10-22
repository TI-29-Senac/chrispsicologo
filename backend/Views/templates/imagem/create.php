<?php use App\Psico\Core\Flash; ?>

<div class="w3-container w3-white w3-text-grey w3-card-4" style="padding-bottom: 32px;">
    <h2 class="w3-text-grey w3-padding-16"><i class="fa fa-plus-circle fa-fw w3-margin-right w3-xxlarge" style="color: #A3B8A1;"></i>Adicionar Nova Imagem</h2>

    <?= Flash::getFlash() ?>

    <div class="w3-container">
        <form action="/backend/imagens/salvar" method="POST" enctype="multipart/form-data" id="form-add-imagem">

            <div class="w3-row-padding w3-section">
                <div class="w3-half">
                    <label for="id_pagina_select"><b><i class="fa fa-sitemap"></i> Página Principal</b></label>
                    <select class="w3-select w3-border" id="id_pagina_select" name="id_pagina_select" required>
                        <option value="" disabled selected>Selecione a página...</option>
                        <?php if (!empty($paginasPrincipais)): ?>
                            <?php foreach ($paginasPrincipais as $pagina): ?>
                                <option value="<?= htmlspecialchars($pagina['id']) ?>" data-nome-pagina="<?= htmlspecialchars($pagina['nome']) ?>">
                                    <?= htmlspecialchars($pagina['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option disabled>Nenhuma página encontrada</option>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="w3-half" id="container-secao-filha" style="display: none;">
                    <label for="id_secao"><b><i class="fa fa-folder-open"></i> Seção Específica</b></label>
                    <select class="w3-select w3-border" id="id_secao" name="id_secao">
                    </select>
                    <small>Selecione a subseção onde a imagem aparecerá.</small>
                </div>
                 <input type="hidden" id="id_secao_hidden" name="id_secao">

            </div>

             <div class="w3-row-padding w3-section">
                <div class="w3-half">
                    <label for="ordem"><b><i class="fa fa-sort-numeric-asc"></i> Ordem de Exibição</b></label>
                    <input class="w3-input w3-border" type="number" id="ordem" name="ordem" value="99" min="1">
                    <small>Menor número aparece primeiro (1, 2, 3...).</small>
                </div>
                 <div class="w3-half">
                    <label for="arquivo_imagem"><b><i class="fa fa-upload"></i> Arquivo da Imagem</b></label>
                    <input class="w3-input w3-border" type="file" id="arquivo_imagem" name="arquivo_imagem" accept="image/jpeg, image/png, image/webp, image/gif" required>
                    <small>Formatos permitidos: JPG, PNG, WEBP, GIF (Máx 2MB).</small>
                 </div>
            </div>

            <button type="submit" class="w3-button w3-right w3-padding" style="background-color: #A3B8A1 !important;">Salvar Imagem</button>
            <a href="/backend/imagens/listar" class="w3-button w3-right w3-padding w3-light-grey w3-margin-right">Cancelar</a>
        </form>
    </div>
</div>

<script>
    // Mapeamento de seções filhas passado pelo PHP Controller
    const secoesFilhasPorPagina = <?= $secoesFilhasJson ?? '{}' ?>;
    // <<< IMPORTANTE: Verifique se este nome corresponde EXATAMENTE ao nome no BD >>>
    const paginaPaiComFilhas = 'Home';

    const selectPagina = document.getElementById('id_pagina_select');
    const containerSecaoFilha = document.getElementById('container-secao-filha');
    const selectSecaoFilha = document.getElementById('id_secao');
    const hiddenSecaoInput = document.getElementById('id_secao_hidden'); // Input oculto

    // Função para atualizar o dropdown filho
    function atualizarDropdownFilho() {
        const selectedOption = selectPagina.options[selectPagina.selectedIndex];
        // Verifica se a opção selecionada existe antes de pegar o atributo
        const nomePaginaSelecionada = selectedOption ? selectedOption.getAttribute('data-nome-pagina') : null;
        const idPaginaSelecionada = selectPagina.value;

        // Logs para depuração (verifique no console F12)
        console.log("Página selecionada:", nomePaginaSelecionada, "(ID:", idPaginaSelecionada, ")");
        console.log("Mapeamento filhas:", secoesFilhasPorPagina);
        console.log("Página pai definida:", paginaPaiComFilhas);

        // Remove o name do select filho inicialmente
        selectSecaoFilha.removeAttribute('name');
        // Define o valor do input hidden com o ID da página principal por padrão
        hiddenSecaoInput.value = idPaginaSelecionada;
         // Garante que o input hidden tenha o name por padrão
         hiddenSecaoInput.setAttribute('name', 'id_secao');


        // Verifica se a página selecionada é a que tem seções filhas E se há filhas mapeadas
        if (nomePaginaSelecionada === paginaPaiComFilhas && secoesFilhasPorPagina[paginaPaiComFilhas] && secoesFilhasPorPagina[paginaPaiComFilhas].length > 0) {
            console.log("Mostrando dropdown filho para:", nomePaginaSelecionada);
            // Mostra o container do dropdown filho
            containerSecaoFilha.style.display = 'block';
            selectSecaoFilha.setAttribute('name', 'id_secao'); // Adiciona o name de volta ao select filho
            hiddenSecaoInput.removeAttribute('name'); // Remove o name do input hidden
            selectSecaoFilha.required = true;

            // Limpa opções antigas
            selectSecaoFilha.innerHTML = '<option value="" disabled selected>Selecione a seção...</option>';

            // Popula com as seções filhas correspondentes
            secoesFilhasPorPagina[paginaPaiComFilhas].forEach(secao => {
                const option = document.createElement('option');
                option.value = secao.id;
                option.textContent = secao.nome;
                selectSecaoFilha.appendChild(option);
            });
            // Limpa o valor do hidden input, pois o valor virá do select filho
            // hiddenSecaoInput.value = ''; // Não é necessário limpar, apenas remover o name

        } else {
            console.log("Escondendo dropdown filho.");
            // Esconde o container do dropdown filho
            containerSecaoFilha.style.display = 'none';
            selectSecaoFilha.required = false;
            selectSecaoFilha.innerHTML = ''; // Limpa opções
            // Garante que o input hidden tenha o name correto e o valor da página principal
            selectSecaoFilha.removeAttribute('name'); // Garante que o select filho não tem name
            hiddenSecaoInput.setAttribute('name', 'id_secao'); // Garante que o hidden tem name
            hiddenSecaoInput.value = idPaginaSelecionada; // Confirma o valor
        }
    }

    // Adiciona o listener
    selectPagina.addEventListener('change', atualizarDropdownFilho);

    // Chama a função uma vez na carga da página para ajustar o estado inicial
    // (caso o navegador lembre a seleção anterior ou haja um valor padrão)
    atualizarDropdownFilho();

     // Validação extra no submit (mantida)
     document.getElementById('form-add-imagem').addEventListener('submit', function(e) {
         const secaoFinalInput = document.querySelector('[name="id_secao"]');
         if (!secaoFinalInput || !secaoFinalInput.value) {
             if (containerSecaoFilha.style.display === 'block' && !selectSecaoFilha.value) {
                 alert('Por favor, selecione a Seção Específica.');
                 e.preventDefault();
                 selectSecaoFilha.focus();
             } else if (containerSecaoFilha.style.display !== 'block' && (!hiddenSecaoInput || !hiddenSecaoInput.value)) {
                  alert('Erro interno: ID da seção não definido.');
                  e.preventDefault();
             }
         }
     });

</script>