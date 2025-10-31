<?php
// Carrega o header
require_once __DIR__ . '/../partials/header.php';

// Pega os dados do profissional (passados pelo Controller)
$profissional = $data['profissional'] ?? null;
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">

            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Meu Perfil Profissional</h1>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Atualizar Meus Dados</h6>
                </div>
                <div class="card-body">
                    
                    <?php if ($profissional) : ?>
                        <form action="/backend/profissional/atualizar-meu-perfil" method="POST" enctype="multipart/form-data">

                            <div class="row">
                                <div class="col-md-4">
                                    
                                    <div class="form-group">
                                        <label for="img_profissional">Foto de Perfil (Opcional)</label>
                                        <input type="file" class="form-control-file" id="img_profissional" name="img_profissional" accept="image/png, image/jpeg, image/webp">
                                        <small class="form-text text-muted">Envie uma nova imagem apenas se desejar alterar a atual.</small>
                                        
                                        <?php if (!empty($profissional->img_profissional)) : ?>
                                            <div class="mt-2">
                                                <img src="/<?php echo htmlspecialchars($profissional->img_profissional); ?>" alt="Imagem Atual" style="max-width: 150px; height: auto; border-radius: 8px;">
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group">
                                        <label for="valor_consulta">Valor da Consulta (R$)</label>
                                        <input type="number" class="form-control" id="valor_consulta" name="valor_consulta" 
                                               step="0.01" min="0" 
                                               value="<?php echo htmlspecialchars($profissional->valor_consulta ?? 0); ?>" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="sinal_consulta">Valor do Sinal (R$)</label>
                                        <input type="number" class="form-control" id="sinal_consulta" name="sinal_consulta" 
                                               step="0.01" min="0" 
                                               value="<?php echo htmlspecialchars($profissional->sinal_consulta ?? 0); ?>" required>
                                    </div>

                                </div>

                                <div class="col-md-8">
                                    
                                    <div class="form-group">
                                        <label for="especialidade">Especialidade(s)</label>
                                        <input type="text" class="form-control" id="especialidade" name="especialidade" 
                                               value="<?php echo htmlspecialchars($profissional->especialidade ?? ''); ?>" 
                                               placeholder="Ex: Terapia Cognitivo-Comportamental, Psicologia Infantil">
                                        <small class="form-text text-muted">Separe as especialidades por vírgula.</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="sobre">Sobre Mim</label>
                                        <textarea class="form-control" id="sobre" name="sobre" rows="10" 
                                                  placeholder="Escreva uma breve biografia que aparecerá no site..."><?php echo htmlspecialchars($profissional->sobre ?? ''); ?></textarea>
                                    </div>

                                </div>
                            </div>
                            
                            <hr>

                            <div class="text-right">
                                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                            </div>

                        </form>
                    <?php else : ?>
                        <div class="alert alert-danger">
                            Não foi possível carregar os dados do perfil profissional.
                        </div>
                    <?php endif; ?>

                </div>
            </div>

        </div>
    </div>
</div>
<?php
// Carrega o footer
require_once __DIR__ . '/../partials/footer.php';
?>