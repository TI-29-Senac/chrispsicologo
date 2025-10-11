<?php
use App\Psico\Core\Flash;

$profissional = $dados['usuario'];
?>

<div class="w3-container w3-white w3-text-grey w3-card-4" style="padding-bottom: 32px;">
    <h2 class="w3-text-grey w3-padding-16"><i class="fa fa-user-md fa-fw w3-margin-right w3-xxlarge" style="color: #A3B8A1;"></i>Editar Profissional</h2>
    
    <?= Flash::getFlash() ?>

    <div class="w3-container">
        <div class="w3-row">
            <form action="/backend/profissionais/atualizar/<?= htmlspecialchars($profissional->id_profissional) ?>" method="POST">
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
                        <label for="especialidade"><b>Especialidade</b></label>
                        <input class="w3-input w3-border" id="especialidade" name="especialidade" type="text" value="<?= htmlspecialchars($profissional->especialidade ?? '') ?>" required>
                    </div>
                    <div class="w3-half">
                        <label for="senha_usuario"><b>Senha (deixe em branco para não alterar)</b></label>
                        <input class="w3-input w3-border" id="senha_usuario" name="senha_usuario" type="password">
                    </div>
                </div>

                <div class="w3-row-padding w3-section">
                    <div class="w3-half">
                        <label for="valor_consulta"><b>Valor da Consulta (R$)</b></label>
                        <input class="w3-input w3-border" type="number" step="0.01" id="valor_consulta" name="valor_consulta" value="<?= htmlspecialchars($profissional->valor_consulta ?? '0.00') ?>" required>
                    </div>
                    <div class="w3-half">
                        <label for="sinal_consulta"><b>Valor do Sinal (R$)</b></label>
                        <input class="w3-input w3-border" type="number" step="0.01" id="sinal_consulta" name="sinal_consulta" value="<?= htmlspecialchars($profissional->sinal_consulta ?? '0.00') ?>" required>
                    </div>
                </div>
                
                <div class="w3-row-padding w3-section">
                    <div class="w3-half">
                         <label for="tipo_usuario"><b>Tipo de Usuário</b></label>
                        <select class="w3-select w3-border" name="tipo_usuario" id="tipo_usuario" required>
                            <option value="profissional" selected>Profissional</option>
                        </select>
                    </div>
                    <div class="w3-half">
                        <label for="status_usuario"><b>Status</b></label>
                        <select class="w3-select w3-border" name="status_usuario" id="status_usuario" required>
                            <option value="ativo" <?= ($profissional->status_usuario == 'ativo') ? 'selected' : '' ?>>Ativo</option>
                            <option value="inativo" <?= ($profissional->status_usuario == 'inativo') ? 'selected' : '' ?>>Inativo</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="w3-button w3-right w3-padding" style="background-color: #A3B8A1 !important;">Atualizar Profissional</button>
                <a href="/backend/profissionais/listar" class="w3-button w3-right w3-padding w3-light-grey w3-margin-right">Cancelar</a>
            </form>
        </div>
    </div>
</div>