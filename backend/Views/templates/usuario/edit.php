<?php
use App\Psico\Core\Flash;

// A variável $usuario é passada pelo Controller
$usuario = $dados['usuario']; 
?>

<div class="w3-container w3-white w3-text-grey w3-card-4" style="padding-bottom: 32px;">
    <h2 class="w3-text-grey w3-padding-16"><i class="fa fa-user fa-fw w3-margin-right w3-xxlarge" style="color: #A3B8A1;"></i>Editar Usuário</h2>
    
    <?= Flash::getFlash() ?>

    <div class="w3-container">
        <div class="w3-row">
            <form action="/backend/usuario/atualizar/<?= htmlspecialchars($usuario->id_usuario) ?>" method="POST">
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
                        
                        <?php 
                        // Garante que a sessão foi iniciada
                        if (session_status() == PHP_SESSION_NONE) { session_start(); }
                        
                        // Verifica se o usuário logado é admin
                        if (isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin'): 
                        ?>
                            <select class="w3-select w3-border" name="tipo_usuario" id="tipo_usuario" required>
                                <option value="cliente" <?= ($usuario->tipo_usuario == 'cliente') ? 'selected' : ''; ?>>Cliente</option>
                                <option value="admin" <?= ($usuario->tipo_usuario == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                <option value="profissional" <?= ($usuario->tipo_usuario == 'profissional') ? 'selected' : ''; ?>>Profissional</option>
                                <option value="recepcionista" <?= ($usuario->tipo_usuario == 'recepcionista') ? 'selected' : ''; ?>>Recepcionista</option>
                            </select>
                        
                        <?php else: ?>
                            <input class="w3-input w3-border w3-light-grey" type="text" value="<?= htmlspecialchars(ucfirst($usuario->tipo_usuario)) ?>" readonly disabled>
                            <input type="hidden" name="tipo_usuario" value="<?= htmlspecialchars($usuario->tipo_usuario) ?>">
                        
                        <?php endif; ?>
                    </div>
                </div>
                <div class="w3-row-padding w3-section">
                    <div class="w3-half">
                        <label for="cpf"><b>CPF</b></label>
                        <input class="w3-input w3-border w3-light-grey" id="cpf" type="text" value="<?= htmlspecialchars($usuario->cpf ?? '') ?>" readonly disabled>
                        <input type="hidden" name="cpf" value="<?= htmlspecialchars($usuario->cpf ?? '') ?>">
                    </div>
                    <div class="w3-half">
                        <label for="status_usuario"><b>Status</b></label>
                        
                        <?php 
                        if (session_status() == PHP_SESSION_NONE) { session_start(); }
                        if (isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin'): 
                        ?>
                            <select class="w3-select w3-border" name="status_usuario" id="status_usuario" required>
                                <option value="ativo" <?= ($usuario->status_usuario == 'ativo') ? 'selected' : '' ?>>Ativo</option>
                                <option value="inativo" <?= ($usuario->status_usuario == 'inativo') ? 'selected' : '' ?>>Inativo</option>
                            </select>
                        
                        <?php else: ?>
                            <input class="w3-input w3-border w3-light-grey" type="text" value="<?= htmlspecialchars(ucfirst($usuario->status_usuario)) ?>" readonly disabled>
                            <input type="hidden" name="status_usuario" value="<?= htmlspecialchars($usuario->status_usuario) ?>">
                        
                        <?php endif; ?>
                    </div>
                </div>

                <button type="submit" class="w3-button w3-right w3-padding" style="background-color: #A3B8A1 !important;">Atualizar Usuário</button>
                <a href="/backend/usuario/listar" class="w3-button w3-right w3-padding w3-light-grey w3-margin-right">Cancelar</a>
            </form>
        </div>
    </div>
</div>