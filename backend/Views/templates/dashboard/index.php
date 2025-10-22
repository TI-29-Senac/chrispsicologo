<?php



$nomeUsuario = $_SESSION['usuario_nome'] ?? 'Utilizador';
?>

<div class="w3-container">
    <h2 style="color: #5D6D68;">👋 Bem-vindo(a) ao Painel, <?= htmlspecialchars($nomeUsuario) ?>!</h2>
    <p>Utilize o menu lateral para navegar pelas secções administrativas.</p>
    <hr>

    <h3 style="color: #5D6D68;">Resumo Rápido</h3>

    <div class="w3-row-padding w3-margin-bottom">

        <?php
          
          $dashboard_colors = ['#5D6D68', '#7C8F88', '#A3B8A1', '#8F9E8B', '#B9C8B5'];
          $d_colorIndex = 0;

          
          if (isset($stats) && is_array($stats) && !empty($stats)):
            foreach ($stats as $stat):
              
              $d_color = $dashboard_colors[$d_colorIndex % count($dashboard_colors)];
              
              $link = $stat['link'] ?? '#'; 
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
              $d_colorIndex++; 
            endforeach;
          else:
            
            echo "<p>Nenhuma estatística rápida disponível no momento.</p>";
          endif;
        ?>
    </div> </div>