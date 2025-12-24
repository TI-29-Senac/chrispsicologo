<?php
use App\Psico\Core\Flash;
?>

<div class="w3-container w3-white w3-text-grey w3-card-4" style="padding-bottom: 32px;">
    <h2 class="w3-text-grey w3-padding-16"><i class="fa fa-user-md fa-fw w3-margin-right w3-xxlarge" style="color: #A3B8A1;"></i>Adicionar Novo Profissional</h2>

    <?= Flash::getFlash() ?>

    <div class="w3-container">
        <div class="w3-row">
            <form action="/backend/profissionais/salvar" method="POST" enctype="multipart/form-data">

                <div class="w3-row-padding">
                    <div class="w3-half">
                        <label for="id_usuario"><b>Selecione o Usuário</b></label>
                        <select class="w3-select w3-border" id="id_usuario" name="id_usuario" required>
                            <option value="" disabled selected>Escolha um usuário...</option>
                            <?php if (!empty($usuariosDisponiveis)): ?>
                                <?php foreach ($usuariosDisponiveis as $usuario): ?>
                                    <option value="<?= htmlspecialchars($usuario->id_usuario) ?>">
                                        <?= htmlspecialchars($usuario->nome_usuario) ?> (ID: <?= htmlspecialchars($usuario->id_usuario) ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="w3-half">
                        <label for="especialidade"><b>Especialidades (separadas por vírgula)</b></label>
                        <input class="w3-input w3-border" type="text" id="especialidade" name="especialidade" placeholder="Ex: Terapia de Casal, Psicanálise" required>
                    </div>
                </div>

                <div class="w3-row-padding w3-section">
                    <div class="w3-full">
                        <label for="sobre"><b>Sobre Mim (Biografia Breve)</b></label>
                        <textarea class="w3-input w3-border" id="sobre" name="sobre" rows="4" placeholder="Escreva uma breve descrição sobre o profissional..."></textarea>
                    </div>
                </div>

                <div class="w3-row-padding w3-section">
                    <div class="w3-third">
                        <label for="valor_consulta"><b>Valor da Consulta (R$)</b></label>
                        <input class="w3-input w3-border" type="number" step="0.01" id="valor_consulta" name="valor_consulta" placeholder="Ex: 150.00" required>
                    </div>
                    <div class="w3-third">
                        <label for="sinal_consulta"><b>Valor do Sinal (R$)</b></label>
                        <input class="w3-input w3-border" type="number" step="0.01" id="sinal_consulta" name="sinal_consulta" placeholder="Ex: 50.00" required>
                    </div>
                     <div class="w3-third">
                         <label for="ordem_exibicao"><b>Ordem de Exibição</b></label>
                         <input class="w3-input w3-border" type="number" id="ordem_exibicao" name="ordem_exibicao" placeholder="1 (primeiro)..." value="99">
                    </div>
                </div>
                <div class="w3-row-padding w3-section">
                <div class="w3-full">
                    <label><b>Tipos de Atendimento (Público)</b></label>
                    <div class="w3-border w3-padding" style="border-radius: 8px; max-height: 150px; overflow-y: auto;">
                        <div class="w3-row-padding">
                            <?php if (!empty($tiposAtendimento)): ?>
                                <?php foreach ($tiposAtendimento as $tipo): ?>
                                    <div class="w3-col m4 l3">
                                        <input class="w3-check" type="checkbox" id="tipo_<?= htmlspecialchars($tipo->id_tipo) ?>" name="tipos_atendimento[]" value="<?= htmlspecialchars($tipo->id_tipo) ?>">
                                        <label for="tipo_<?= htmlspecialchars($tipo->id_tipo) ?>"><?= htmlspecialchars($tipo->nome) ?></label>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>Nenhum tipo de atendimento cadastrado no banco de dados.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
                <div class="w3-row-padding w3-section">
                     <div class="w3-half">
                        <label for="img_profissional"><b>Foto do Profissional</b></label>
                        <input class="w3-input w3-border" type="file" id="img_profissional" name="img_profissional" accept="image/png, image/jpeg, image/webp">
                        <small>Envie PNG, JPG ou WEBP (máx 2MB).</small>
                    </div>
                    <div class="w3-half" style="padding-top: 24px;">
                        <input class="w3-check" type="checkbox" id="publico" name="publico" value="1" checked>
                        <label for="publico"><b>Mostrar no site público?</b></label>
                    </div>
                </div>

                <button type="submit" class="w3-button w3-right w3-padding" style="background-color: #A3B8A1 !important;">Salvar Profissional</button>
                <a href="/backend/profissionais/listar" class="w3-button w3-right w3-padding w3-light-grey w3-margin-right">Cancelar</a>
            </form>
        </div>
    </div>
</div>