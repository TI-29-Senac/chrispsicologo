<?php foreach($profissionais as $profissional): ?>
    <p><?= $profissional['id_profissional'] ?></p>
    <p><?= $profissional['id_usuario'] ?></p>
    <p><?= $profissional['especialidade'] ?></p>
<?php endforeach; ?>