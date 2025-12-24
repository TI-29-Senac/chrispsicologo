<?php
// A variável $profissional é injetada automaticamente pelo View.php
// A variável $flash (para mensagens) também deve estar disponível
?>

<div class="w3-main" style="margin-top:43px; padding: 0 24px 24px 24px;">

    <header class="w3-container" style="padding-top:22px">
        <h5><b><i class="fa fa-user-md fa-fw"></i> Meu Perfil Profissional</b></h5>
    </header>

    <div class="w3-container">

        <?php

        $flash = \App\Psico\Core\Flash::get();

if (isset($flash['type'], $flash['message'])):
    $type = $flash['type'] === 'success' ? 'green' : 'red';
    $icon = $flash['type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
?>
    <div class="w3-panel w3-card-2 w3-<?= $type; ?> w3-padding w3-display-container"
         style="border-radius: 8px; color: white !important;
                background-color: <?= $flash['type'] === 'success' ? '#5D6D68' : '#D32F2F'; ?> !important;">
        <span onclick="this.parentElement.style.display='none'"
              class="w3-button w3-display-topright w3-hover-none w3-hover-text-light-grey"
              style="padding: 8px 16px;">&times;</span>
        <p style="margin: 0;">
            <i class="fa <?= $icon; ?>"></i> <?= htmlspecialchars($flash['message']); ?>
        </p>
    </div>
<?php endif; ?>

        <div class="w3-card-4 w3-white" style="border-radius: 12px; overflow: hidden;">

            <header class="w3-container" style="background-color: #5D6D68; color: white; padding: 12px 16px;">
                <h5 style="margin: 0;">Atualizar Meus Dados</h5>
            </header>

            <div class="w3-container w3-padding">
                
                <?php if (isset($profissional) && $profissional) : ?>
                    <form action="/backend/profissional/atualizar-meu-perfil" method="POST" enctype="multipart/form-data" class="w3-row-padding">

                        <div class="w3-col m5 l4">
                            
                            <div class="w3-margin-bottom">
                                <label class="w3-text-grey">Foto de Perfil (Opcional)</label>
                                <input type="file" class="w3-input w3-border w3-white" id="img_profissional" name="img_profissional" accept="image/png, image/jpeg, image/webp" style="border-radius: 4px; padding: 8px;">
                                <small class="w3-text-grey">Envie uma nova imagem apenas se desejar alterar a atual.</small>
                                
                                <?php if (!empty($profissional->img_profissional)) : ?>
                                    <div class="w3-margin-top">
                                        <img src="/<?php echo htmlspecialchars($profissional->img_profissional); ?>" alt="Imagem Atual" style="max-width: 150px; height: auto; border-radius: 8px; border: 1px solid #ddd;">
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="w3-margin-bottom">
                                <label class="w3-text-grey">Valor da Consulta (R$)</label>
                                <input type="number" class="w3-input w3-border" id="valor_consulta" name="valor_consulta" 
                                       step="0.01" min="0" style="border-radius: 4px;"
                                       value="<?php echo htmlspecialchars($profissional->valor_consulta ?? 0); ?>" required>
                            </div>

                            <div class="w3-margin-bottom">
                                <label class="w3-text-grey">Valor do Sinal (R$)</label>
                                <input type="number" class="w3-input w3-border" id="sinal_consulta" name="sinal_consulta" 
                                       step="0.01" min="0" style="border-radius: 4px;"
                                       value="<?php echo htmlspecialchars($profissional->sinal_consulta ?? 0); ?>" required>
                            </div>

                        </div>

                        <div class="w3-col m7 l8">
                            
                            <div class="w3-margin-bottom">
                                <label class="w3-text-grey">Especialidade(s)</label>
                                <input type="text" class="w3-input w3-border" id="especialidade" name="especialidade" 
                                       value="<?php echo htmlspecialchars($profissional->especialidade ?? ''); ?>" 
                                       placeholder="Ex: Terapia Cognitivo-Comportamental, Psicologia Infantil" style="border-radius: 4px;">
                                <small class="w3-text-grey">Separe as especialidades por vírgula.</small>
                            </div>

                            <div class="w3-margin-bottom">
                                <label class="w3-text-grey">Sobre Mim</label>
                                <textarea class="w3-input w3-border" id="sobre" name="sobre" rows="12" 
                                          placeholder="Escreva uma breve biografia..." style="border-radius: 4px;"><?php echo htmlspecialchars($profissional->sobre ?? ''); ?></textarea>
                            </div>

                        </div>
                        
                        <div class="w3-col m12 w3-padding-16">
                            <button type="submit" class="w3-button w3-right w3-hover-white w3-hover-text-black" style="background-color: #5D6D68; color: white; border-radius: 4px;">
                                <i class="fa fa-save"></i> Salvar Alterações
                            </button>
                        </div>

                    </form>
                
                <?php else : ?>
                    <div class="w3-panel w3-pale-red w3-border w3-border-red w3-padding" style="border-radius: 8px;">
                        <p><b>Erro Crítico:</b> Não foi possível carregar os dados do perfil profissional.</p>
                        <p>A variável <code>$profissional</code> não foi recebida pela view. Verifique o Controller (`viewMeuPerfilProfissional`) e o método `extract()` no `Core/View.php`.</p>
                    </div>
                <?php endif; ?>

            </div>

        </div>
    </div>
</div>