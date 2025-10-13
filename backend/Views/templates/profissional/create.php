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
    <label for="id_usuario"><b>Selecione o Usuário</b></label>
    <select class="w3-select w3-border" id="id_usuario" name="id_usuario" required>
        <option value="" disabled selected>Escolha um usuário...</option>
        <?php if (!empty($usuariosDisponiveis)): ?>
            <?php foreach ($usuariosDisponiveis as $usuario): ?>
                <option value="<?= htmlspecialchars($usuario->id_usuario) ?>">
                    <?= htmlspecialchars($usuario->nome_usuario) ?>
                </option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
</div>