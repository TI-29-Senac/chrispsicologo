<?php
// ti-29-senac/chrispsicologo/chrispsicologo-backend-correto2/backend/Views/templates/servico/edit.php
use App\Psico\Core\Flash;

$servico = $dados['servico']; // Variável $servico vinda do Controller
?>

<div class="w3-container w3-white w3-text-grey w3-card-4" style="padding-bottom: 32px;">
    <h2 class="w3-text-grey w3-padding-16"><i class="fa fa-briefcase fa-fw w3-margin-right w3-xxlarge" style="color: #A3B8A1;"></i>Editar Serviço</h2>

    <?= Flash::getFlash() ?>

    <div class="w3-container">
        <div class="w3-row">
            <form action="/backend/servicos/atualizar/<?= htmlspecialchars($servico->id) ?>" method="POST" enctype="multipart/form-data">
                
                <div class="w3-row-padding">
                    <div class="w3-full">
                        <label for="titulo"><b>Título do Serviço</b></label>
                        <input class="w3-input w3-border" id="titulo" name="titulo" type="text" value="<?= htmlspecialchars($servico->titulo ?? '') ?>" required>
                    </div>
                </div>

                <div class="w3-row-padding w3-section">
                    <div class="w3-full">
                        <label for="descricao"><b>Descrição do Serviço</b></label>
                        <textarea class="w3-input w3-border" id="descricao" name="descricao" rows="4" placeholder="Descreva o serviço..."><?= htmlspecialchars($servico->descricao ?? '') ?></textarea>
                    </div>
                </div>

                 <div class="w3-row-padding w3-section">
                     <div class="w3-half">
                        <label for="icone_path"><b>Alterar Ícone (Opcional)</b></label>
                        <input class="w3-input w3-border" type="file" id="icone_path" name="icone_path" accept="image/svg+xml, image/png, image/jpeg, image/webp">
                        <small>Deixe em branco para manter o atual. Máx 1MB.</small>
                        <input type="hidden" name="icone_atual" value="<?= htmlspecialchars($servico->icone_path ?? '') ?>">
                        
                        <?php if (!empty($servico->icone_path)): ?>
                            <div style="margin-top: 10px;">
                                <label>Ícone Atual:</label><br>
                                <img src="/<?= htmlspecialchars($servico->icone_path) ?>" alt="Ícone Atual" style="max-width: 50px; height: auto; border: 1px solid #ccc; background-color: #f4f4f4;">
                            </div>
                        <?php endif; ?>
                    </div>
                     <div class="w3-half" style="padding-top: 24px;">
                        <input class="w3-check" type="checkbox" id="ativo" name="ativo" value="1" <?= (isset($servico->ativo) && $servico->ativo == 1) ? 'checked' : '' ?>>
                        <label for="ativo"><b>Mostrar no site público?</b></label>
                    </div>
                 </div>

                <button type="submit" class="w3-button w3-right w3-padding" style="background-color: #A3B8A1 !important;">Atualizar Serviço</button>
                <a href="/backend/servicos/listar" class="w3-button w3-right w3-padding w3-light-grey w3-margin-right">Cancelar</a>
            </form>
        </div>
    </div>
</div>