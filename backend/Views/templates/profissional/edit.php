<?php
use App\Psico\Core\Flash;

$profissional = $dados['usuario']; 
?>

<div class="w3-container w3-white w3-text-grey w3-card-4" style="padding-bottom: 32px;">
    <h2 class="w3-text-grey w3-padding-16"><i class="fa fa-user-md fa-fw w3-margin-right w3-xxlarge" style="color: #A3B8A1;"></i>Editar Profissional</h2>

    <?= Flash::getFlash() ?>

    <div class="w3-container">
        <div class="w3-row">
            <form action="/backend/profissionais/atualizar/<?= htmlspecialchars($profissional->id_profissional) ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_usuario" value="<?= htmlspecialchars($profissional->id_usuario) ?>">

                <div class="w3-row-padding">
                    <div class="w3-half">
                        <label for="nome_usuario"><b>Nome</b></label>
                        <input class="w3-input w3-border" id="nome_usuario" name="nome_usuario" type="text" value="<?= htmlspecialchars($profissional->nome_usuario ?? '') ?>" required>
                    </div>
                    <div class="w3-half">
                        <label for="email_usuario"><b>Email</b></label>
                        <input class="w3-input w3-border" id="email_usuario" name="email_usuario" type="email" value="<?= htmlspecialchars($profissional->email_usuario ?? '') ?>" required>
                    </div>
                </div>

                 <div class="w3-row-padding w3-section">
                    <div class="w3-half">
                        <label for="especialidade"><b>Especialidades (separadas por vírgula)</b></label>
                        <textarea class="w3-input w3-border" id="especialidade" name="especialidade" rows="3" placeholder="Ex: Terapia de Casal, Psicanálise" required><?= htmlspecialchars($profissional->especialidade ?? '') ?></textarea>
                    </div>
                     <div class="w3-half">
                        <label for="sobre"><b>Sobre Mim (Biografia Breve)</b></label>
                        <textarea class="w3-input w3-border" id="sobre" name="sobre" rows="3" placeholder="Descreva o profissional..."><?= htmlspecialchars($profissional->sobre ?? '') ?></textarea>
                    </div>
                </div>

                <div class="w3-row-padding w3-section">
                    <div class="w3-third">
                        <label for="valor_consulta"><b>Valor da Consulta (R$)</b></label>
                        <input class="w3-input w3-border" type="number" step="0.01" id="valor_consulta" name="valor_consulta" value="<?= htmlspecialchars($profissional->valor_consulta ?? '0.00') ?>" required>
                    </div>
                    <div class="w3-third">
                        <label for="sinal_consulta"><b>Valor do Sinal (R$)</b></label>
                        <input class="w3-input w3-border" type="number" step="0.01" id="sinal_consulta" name="sinal_consulta" value="<?= htmlspecialchars($profissional->sinal_consulta ?? '0.00') ?>" required>
                    </div>
                    <div class="w3-third">
                        <label for="ordem_exibicao"><b>Ordem de Exibição</b></label>
                        <input class="w3-input w3-border" type="number" id="ordem_exibicao" name="ordem_exibicao" value="<?= htmlspecialchars($profissional->ordem_exibicao ?? 99) ?>">
                    </div>
                </div>


                 <div class="w3-row-padding w3-section">
                     <div class="w3-half">
                        <label for="img_profissional"><b>Alterar Foto (Opcional)</b></label>
                        <input class="w3-input w3-border" type="file" id="img_profissional" name="img_profissional" accept="image/png, image/jpeg, image/webp">
                        <small>Deixe em branco para manter a atual. Máx 2MB.</small>
                        <input type="hidden" name="imagem_atual" value="<?= htmlspecialchars($profissional->img_profissional ?? '') ?>">
                        <?php if (!empty($profissional->img_profissional)): ?>
                            <div style="margin-top: 10px;">
                                <label>Imagem Atual:</label><br>
                                <img src="/<?= htmlspecialchars($profissional->img_profissional) ?>" alt="Imagem Atual" style="max-width: 100px; height: auto; border: 1px solid #ccc;">
                            </div>
                        <?php endif; ?>
                    </div>
                     <div class="w3-half" style="padding-top: 24px;">
                        <input class="w3-check" type="checkbox" id="publico" name="publico" value="1" <?= (isset($profissional->publico) && $profissional->publico == 1) ? 'checked' : '' ?>>
                        <label for="publico"><b>Mostrar no site público?</b></label>
                    </div>
                 </div>

                <div class="w3-row-padding w3-section">
                    <div class="w3-third">
                        <label for="senha_usuario"><b>Senha (deixe em branco para não alterar)</b></label>
                        <input class="w3-input w3-border" id="senha_usuario" name="senha_usuario" type="password">
                    </div>
                     <div class="w3-third">
                         <label for="tipo_usuario"><b>Tipo de Usuário</b></label>

                        <input class="w3-input w3-border" type="text" value="Profissional" readonly>
                        <input type="hidden" name="tipo_usuario" value="profissional">
                    </div>
                    <div class="w3-third">
                        <label for="status_usuario"><b>Status</b></label>
                        <select class="w3-select w3-border" name="status_usuario" id="status_usuario" required>
                            <option value="ativo" <?= (isset($profissional->status_usuario) && $profissional->status_usuario == 'ativo') ? 'selected' : '' ?>>Ativo</option>
                            <option value="inativo" <?= (isset($profissional->status_usuario) && $profissional->status_usuario == 'inativo') ? 'selected' : '' ?>>Inativo</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="w3-button w3-right w3-padding" style="background-color: #A3B8A1 !important;">Atualizar Profissional</button>
                <a href="/backend/profissionais/listar" class="w3-button w3-right w3-padding w3-light-grey w3-margin-right">Cancelar</a>
            </form>
        </div>
    </div>
</div>