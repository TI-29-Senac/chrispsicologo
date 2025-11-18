<?php
// ti-29-senac/chrispsicologo/chrispsicologo-backend-correto2/backend/Views/templates/servico/index.php
use App\Psico\Core\Flash;
?>

<div class="w3-container">
    <div class="w3-row w3-margin-bottom">
        <div class="w3-col m8 l9">
            <h2 style="color: #5D6D68;"><i class="fa fa-briefcase fa-fw"></i> Lista de Serviços</h2>
        </div>
        <div class="w3-col m4 l3 w3-right-align">
            <a href="/backend/servicos/criar" class="w3-button" style="background-color: #5D6D68 !important; color: white; border-radius: 8px;">+ Adicionar Serviço</a>
        </div>
    </div>
    
    <?= Flash::getFlash() ?>

    <div class="w3-responsive">
        <table class="w3-table-all w3-card-4 w3-hoverable w3-white" style="border-radius: 8px; overflow: hidden;">
          <thead style="background-color: #5D6D68; color: white;">
                <tr class="w3-light-grey">
                <th>ID</th>
                <th>Ícone</th>
                <th>Título</th>
                <th>Descrição</th>
                <th>Status</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($servicos)): // Variável $servicos vinda do Controller ?>
                  <?php foreach($servicos as $servico): ?>
                  <tr>
                    <td><?= htmlspecialchars($servico->id) ?></td>
                    <td>
                        <?php if(!empty($servico->icone_path)): ?>
                            <img src="/<?= htmlspecialchars($servico->icone_path) ?>" alt="ícone" style="width: 30px; height: 30px;">
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($servico->titulo) ?></td>
                    <td><?= htmlspecialchars(substr($servico->descricao, 0, 80)) . '...' // Mostra apenas parte da descrição ?></td>
                    <td>
                      <span class="w3-tag <?= $servico->ativo == 1 ? 'w3-green' : 'w3-red' // Baseado na coluna 'ativo' ?> w3-round">
                        <?= $servico->ativo == 1 ? 'Ativo' : 'Inativo' ?>
                      </span>
                    </td>
                    <td>
                      <a href="/backend/servicos/editar/<?= $servico->id ?>" class="w3-button w3-tiny w3-round" style="background-color: #5D6D68; color: white;"><i class="fa fa-pencil"></i> Editar</a>
                      <a href="/backend/servicos/excluir/<?= $servico->id ?>" class="w3-button w3-tiny w3-red w3-round"><i class="fa fa-trash"></i> Excluir</a>
                    </td>
                  </tr>
                  <?php endforeach; ?>
              <?php else: ?>
                  <tr><td colspan="6" class="w3-center">Nenhum serviço encontrado.</td></tr>
              <?php endif; ?>
            </tbody>
        </table>
    </div>
    <br>
    
    <?php if (isset($paginacao) && $paginacao['ultima_pagina'] > 1): ?>
    <div class="w3-center">
        <div class="w3-bar">
            <?php if ($paginacao['pagina_atual'] > 1): ?>
                <a href="?pagina=<?= $paginacao['pagina_atual'] - 1 ?>" class="w3-button">&laquo;</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $paginacao['ultima_pagina']; $i++): ?>
                <a href="?pagina=<?= $i ?>" class="w3-button <?= ($i == $paginacao['pagina_atual']) ? 'w3-green' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($paginacao['pagina_atual'] < $paginacao['ultima_pagina']): ?>
                <a href="?pagina=<?= $paginacao['pagina_atual'] + 1 ?>" class="w3-button">&raquo;</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>