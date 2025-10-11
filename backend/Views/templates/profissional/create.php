<?php
use App\Psico\Core\Flash;
?>

<div class="w3-container w3-white w3-text-grey w3-card-4" style="padding-bottom: 32px;">
    <h2 class="w3-text-grey w3-padding-16"><i class="fa fa-user-md fa-fw w3-margin-right w3-xxlarge" style="color: #A3B8A1;"></i>Adicionar Novo Profissional</h2>
    
    <?= Flash::getFlash() ?>

    <div class="w3-container">
        <div class="w3-row">
            <form action="/backend/profissionais/salvar" method="POST">
                <div class="w3-row-padding">
                    <div class="w3-half">
                        <label for="id_usuario"><b>ID do Usuário a ser Profissional</b></label>
                        <input class="w3-input w3-border" id="id_usuario" name="id_usuario" type="text" placeholder="Insira o ID de um usuário existente" required>
                    </div>
                    <div class="w3-half">
                        <label for="especialidade"><b>Especialidade</b></label>
                        <input class="w3-input w3-border" id="especialidade" name="especialidade" type="text" placeholder="Ex: Terapia Cognitivo-Comportamental" required>
                    </div>
                </div>

                <button type="submit" class="w3-button w3-right w3-padding w3-section" style="background-color: #A3B8A1 !important;">Salvar Profissional</button>
                <a href="/backend/profissionais/listar" class="w3-button w3-right w3-padding w3-light-grey w3-margin-right w3-section">Cancelar</a>
            </form>
        </div>
    </div>
</div>