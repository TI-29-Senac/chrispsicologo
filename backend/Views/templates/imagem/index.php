<?php use App\Psico\Core\Flash; ?>

<div class="w3-container">
    <div class="w3-row w3-margin-bottom">
        <div class="w3-col m8 l9">
            <h2 style="color: #5D6D68;"><i class="fa fa-picture-o fa-fw"></i> Gerenciar Imagens do Site</h2>
        </div>
        <div class="w3-col m4 l3 w3-right-align">
            <a href="/backend/imagens/criar" class="w3-button" style="background-color: #5D6D68 !important; color: white; border-radius: 8px;">+ Adicionar Imagem</a>
        </div>
    </div>

    <?= Flash::getFlash() ?>

    <?php if (!empty($imagensAgrupadas)): ?>
        <?php foreach ($imagensAgrupadas as $nomeSecao => $imagens): ?>
            <h3 style="color: #5D6D68; border-bottom: 2px solid #A3B8A1; padding-bottom: 5px; margin-top: 30px;"><?= htmlspecialchars($nomeSecao) ?></h3>
            <div class="w3-row-padding">
                <?php if (!empty($imagens)): ?>
                    <?php foreach ($imagens as $img): ?>
                        <div class="w3-quarter w3-margin-bottom">
                            <div class="w3-card">
                                <img src="/<?= htmlspecialchars($img->url_imagem) ?>" alt="Imagem <?= $img->id_imagem ?>" style="width:100%; height: 150px; object-fit: cover;">
                                <div class="w3-container w3-center w3-padding">
                                    <p>Ordem: <?= htmlspecialchars($img->ordem) ?></p>
                                    <a href="/backend/imagens/editar/<?= $img->id_imagem ?>" class="w3-button w3-tiny w3-round" style="background-color: #5D6D68; color: white;" title="Editar"><i class="fa fa-pencil"></i></a>
                                    <a href="/backend/imagens/excluir/<?= $img->id_imagem ?>" class="w3-button w3-tiny w3-red w3-round" title="Excluir"><i class="fa fa-trash"></i></a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Nenhuma imagem encontrada para esta seção.</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="w3-center">Nenhuma imagem cadastrada no site ainda.</p>
    <?php endif; ?>
</div>