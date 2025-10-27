<?php
use App\Psico\Core\Flash;
?>

<div class="w3-container w3-white w3-text-grey w3-card-4" style="padding-bottom: 32px;">
    <h2 class="w3-text-grey w3-padding-16"><i class="fa fa-user-plus fa-fw w3-margin-right w3-xxlarge" style="color: #A3B8A1;"></i>Adicionar Novo Usuário</h2>
    
    <?= Flash::getFlash() ?>

    <div class="w3-container">
        <div class="w3-row">
            <form action="/backend/usuario/salvar" method="POST">
                <div class="w3-row-padding">
                    <div class="w3-half">
                        <label for="nome_usuario"><b>Nome do Usuário</b></label>
                        <input class="w3-input w3-border" id="nome_usuario" name="nome_usuario" type="text" placeholder="Insira o nome completo" required>
                    </div>
                    <div class="w3-half">
                        <label for="email_usuario"><b>Email</b></label>
                        <input class="w3-input w3-border" id="email_usuario" name="email_usuario" type="email" placeholder="Ex: email@dominio.com" required>
                    </div>
                </div>

                <div class="w3-row-padding w3-section">
                    <div class="w3-half">
                        <label for="senha_usuario"><b>Senha</b></label>
                        <input class="w3-input w3-border" id="senha_usuario" name="senha_usuario" type="password" placeholder="Mínimo 6 caracteres" required>
                    </div>
                    <div class="w3-half">
                        <label for="tipo_usuario"><b>Tipo de Usuário</b></label>
                        <select class="w3-select w3-border" id="tipo_usuario" name="tipo_usuario" required>
                            <option value="cliente" selected>Cliente</option>
                            <option value="admin">Admin</option>
                            <option value="recepcionista">Recepcionista</option>
                            <option value="profissional">Profissional</option>
                        </select>
                    </div>
                </div>

                <div class="w3-row-padding w3-section">
                    <div class="w3-half">
                        <label for="cpf"><b>CPF</b></label>
                        <input class="w3-input w3-border" id="cpf" name="cpf" type="text" placeholder="Insira o CPF">
                    </div>
                </div>

                <button type="submit" class="w3-button w3-right w3-padding w3-section" style="background-color: #A3B8A1 !important;">Salvar Usuário</button>
                <a href="/backend/usuario/listar" class="w3-button w3-right w3-padding w3-light-grey w3-margin-right w3-section">Cancelar</a>
            </form>
        </div>
    </div>
</div>