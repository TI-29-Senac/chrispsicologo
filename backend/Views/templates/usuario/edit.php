<?php
use App\Psico\Core\Flash;
 
$usuario = $dados['usuario'];
?>
 
<div class="w3-container w3-white w3-text-grey w3-card-4" style="padding-bottom: 32px;">
    <h2 class="w3-text-grey w3-padding-16"><i class="fa fa-user fa-fw w3-margin-right w3-xxlarge" style="color: #A3B8A1;"></i>Editar Usuário</h2>
   
    <?= Flash::getFlash() ?>
 
    <div class="w3-container">
        <div class="w3-row">
            <form action="/backend/usuario/editar/<?= $usuario->id_usuario; ?>" method="post">
                <div class="w3-row-padding">
                    <div class="w3-half">
                        <label for="nome_usuario"><b>Nome</b></label>
                        <input class="w3-input w3-border" id="nome_usuario" name="nome_usuario" type="text" value="<?= htmlspecialchars($usuario->nome_usuario ?? '') ?>" required>
                    </div>
                    <div class="w3-half">
                        <label for="email_usuario"><b>Email</b></label>
                        <input class="w3-input w3-border" id="email_usuario" name="email_usuario" type="email" value="<?= htmlspecialchars($usuario->email_usuario ?? '') ?>" required>
                    </div>
                </div>
 
                <div class="w3-row-padding w3-section">
                    <div class="w3-half">
                        <label for="senha_usuario"><b>Senha (deixe em branco para não alterar)</b></label>
                        <input class="w3-input w3-border" id="senha_usuario" name="senha_usuario" type="password">
                    </div>
                    <div class="w3-half">
                         <label for="tipo_usuario"><b>Tipo de Usuário</b></label>
                        <select class="w3-select w3-border" name="tipo_usuario" id="tipo_usuario" required>
                            <option value="user" <?= ($usuario->tipo_usuario == 'user') ? 'selected' : ''; ?>>Usuário</option>
                            <option value="admin" <?= ($usuario->tipo_usuario == 'admin') ? 'selected' : ''; ?>>Administrador</option>
                        </select>
                    </div>
                </div>
                 <div class="w3-row-padding w3-section">
                    <div class="w3-half">
                        <label for="cpf"><b>CPF</b></label>
                        <input class="w3-input w3-border" id="cpf" name="cpf" type="text" value="<?= htmlspecialchars($usuario->cpf ?? '') ?>">
                    </div>
                </div>
 
                <button type="submit" class="w3-button w3-right w3-padding" style="background-color: #A3B8A1 !important;">Atualizar Usuário</button>
                <a href="/backend/usuario/listar" class="w3-button w3-right w3-padding w3-light-grey w3-margin-right">Cancelar</a>
            </form>
        </div>
    </div>
</div>