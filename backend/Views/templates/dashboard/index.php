<?php
// backend/Views/templates/dashboard/index.php

// Mensagem de boas-vindas
$nomeUsuario = $_SESSION['usuario_nome'] ?? 'Utilizador';
?>

<div class="w3-container">
    <h2 style="color: #5D6D68;">👋 Bem-vindo(a) ao Painel, <?= htmlspecialchars($nomeUsuario) ?>!</h2>
    <p>Utilize o menu lateral para navegar pelas secções administrativas.</p>
    <hr>

    <h3 style="color: #5D6D68;">Resumo Rápido</h3>

    <div class="w3-row-padding w3-margin-bottom">

        <?php
          // Define um array de cores para variar os cards, se não vier do header
          $dashboard_colors = ['#5D6D68', '#7C8F88', '#A3B8A1', '#8F9E8B', '#B9C8B5'];
          $d_colorIndex = 0;

          // Verifica se a variável $stats foi passada pela view
          if (isset($stats) && is_array($stats) && !empty($stats)):
            foreach ($stats as $stat):
              // Define a cor para o card
              $d_color = $dashboard_colors[$d_colorIndex % count($dashboard_colors)];
              // Define o link (se existir)
              $link = $stat['link'] ?? '#'; // Link padrão '#' se não for definido
        ?>
        <div class="w3-quarter w3-margin-bottom">
          <a href="<?= htmlspecialchars($link) ?>" style="text-decoration: none;">
            <div class="w3-container w3-card-4 w3-padding-16 w3-hover-shadow" style="background-color: <?= $d_color; ?>; color: white; border-radius: 12px;">
              <div class="w3-left"><i class="fa <?= htmlspecialchars($stat['icon'] ?? 'fa-info-circle') ?> w3-xxxlarge"></i></div>
              <div class="w3-right">
                <h3><?= htmlspecialchars($stat['value'] ?? 'N/A') ?></h3>
              </div>
              <div class="w3-clear"></div>
              <h4><?= htmlspecialchars($stat['label'] ?? 'Informação') ?></h4>
            </div>
          </a>
        </div>
        <?php
              $d_colorIndex++; // Incrementa o índice da cor
            endforeach;
          else:
            // Mensagem se não houver stats para exibir
            echo "<p>Nenhuma estatística rápida disponível no momento.</p>";
          endif;
        ?>
    </div> </div>