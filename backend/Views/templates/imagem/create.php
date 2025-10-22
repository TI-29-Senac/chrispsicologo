<?php use App\Psico\Core\Flash; ?>

<div class="w3-container w3-white w3-text-grey w3-card-4" style="padding-bottom: 32px;">
    <h2 class="w3-text-grey w3-padding-16"><i class="fa fa-plus-circle fa-fw w3-margin-right w3-xxlarge" style="color: #A3B8A1;"></i>Adicionar Nova Imagem</h2>

    <?= Flash::getFlash() ?>

    <div class="w3-container">
        <form action="/backend/imagens/salvar" method="POST" enctype="multipart/form-data" id="form-add-imagem">

            <div class="w3-row-padding w3-section">
                <div class="w3-half">
                    <label for="id_pagina"><b><i class="fa fa-sitemap"></i> Página do Site</b></label>
                    <select class="w3-select w3-border" id="id_pagina" name="id_pagina" required>
                        <option value="" disabled selected>Selecione a página...</option>
                        <?php if (!empty($paginas)): ?>
                            <?php foreach ($paginas as $pagina): ?>
                                <option value="<?= htmlspecialchars($pagina->id_pagina) ?>">
                                    <?= htmlspecialchars($pagina->nome_pagina) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option disabled>Nenhuma página encontrada</option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="w3-half" id="container-secao">
                    <label for="id_secao"><b><i class="fa fa-folder-open"></i> Seção Específica</b></label>
                    <select class="w3-select w3-border" id="id_secao" name="id_secao" required disabled>
                        <option value="" disabled selected>Selecione a página primeiro...</option>
                    </select>
                     <small id="secao-feedback" style="color: #5D6D68;"></small>
                </div>
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
    const selectPagina = document.getElementById('id_pagina');
    const selectSecao = document.getElementById('id_secao');
    const secaoFeedback = document.getElementById('secao-feedback');

    selectPagina.addEventListener('change', function() {
        const idPaginaSelecionada = this.value;
        selectSecao.innerHTML = '<option value="" disabled selected>Carregando seções...</option>'; // Placeholder
        selectSecao.disabled = true;
        secaoFeedback.textContent = '';

        if (!idPaginaSelecionada) {
            selectSecao.innerHTML = '<option value="" disabled selected>Selecione a página primeiro...</option>';
            return;
        }

        // Fazer a chamada API para buscar as seções
        fetch(`/backend/api/secoes/por-pagina/${idPaginaSelecionada}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.secoes && data.secoes.length > 0) {
                    selectSecao.innerHTML = '<option value="" disabled selected>Selecione a seção...</option>'; // Reset com prompt
                    data.secoes.forEach(secao => {
                        const option = document.createElement('option');
                        option.value = secao.id_secao;
                        option.textContent = secao.nome_secao;
                        selectSecao.appendChild(option);
                    });
                    selectSecao.disabled = false; // Habilita o select
                } else {
                     selectSecao.innerHTML = '<option value="" disabled selected>Nenhuma seção encontrada</option>';
                     secaoFeedback.textContent = data.message || 'Nenhuma seção específica encontrada para esta página.';
                }
            })
            .catch(error => {
                console.error('Erro ao buscar seções:', error);
                selectSecao.innerHTML = '<option value="" disabled selected>Erro ao carregar</option>';
                secaoFeedback.textContent = 'Erro ao carregar seções.';
            });
    });

    // Validação no submit (opcional, mas recomendada)
     document.getElementById('form-add-imagem').addEventListener('submit', function(e) {
         if (!selectPagina.value) {
             alert('Por favor, selecione a Página do Site.');
             e.preventDefault();
             selectPagina.focus();
             return;
         }
         if (!selectSecao.value || selectSecao.disabled) {
              alert('Por favor, selecione a Seção Específica.');
              e.preventDefault();
              selectSecao.focus();
              return;
         }
     });
</script>