<?php
// ti-29-senac/chrispsicologo/chrispsicologo-backend-correto2/backend/Views/templates/partials/header.php
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>Dashboard - Chris Psicologia</title>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Questrial">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        html, body, h1, h2, h3, h4, h5, h6 { font-family: "Questrial", sans-serif; }
        body { background-color: #faf6ee; }
        .w3-bar { background-color: #5D6D68; }
        .w3-sidebar { background-color: #fff; }
        .w3-button { color: #ffffffff; }
        .w3-button:hover { background-color: white !important; }
        .w3-card-4 { border-radius: 12px; }
        .header-logo { max-width: 250px; margin: 20px auto;}
        .w3-bar .w3-bar-item { color: white; }
    </style>
</head>
<body style="background-color: #faf6eed9;">

<div class="w3-bar w3-top w3-large" style="z-index:4; background-color: #5D6D68;">
  <button class="w3-bar-item w3-button w3-hide-large w3-hover-none w3-hover-text-light-grey" onclick="w3_open();"><i class="fa fa-bars"></i>  Menu</button>
  <span class="w3-bar-item w3-right">Chris Psicologia</span>
</div>

<nav class="w3-sidebar w3-collapse w3-animate-left" style="z-index:3;width:300px; background-color: #7C8F88;" id="mySidebar"><br>
  <div class="w3-container w3-row w3-center" style="background-color: #7C8F88; border-radius: 20px !important;">
      <img src="/img/logo/logochris.svg" alt="Logo Chris Psicologia" class="header-logo">
  </div>
  <hr>
  <div class="w3-container">
    <h5 style="color: white; font-size: 1.5rem">Dashboard</h5>
  </div>
  <div class="w3-bar-block">
    <a href="#" class="w3-bar-item w3-button w3-padding-16 w3-hide-large w3-dark-grey w3-hover-black" onclick="w3_close()" title="close menu"><i class="fa fa-remove fa-fw"></i>  Fechar Menu</a>
    <a href="/backend/usuario/listar" class="w3-bar-item w3-button w3-padding"><i class="fa fa-users fa-fw"></i>  Usuários</a>
    <a href="/backend/agendamentos/listar" class="w3-bar-item w3-button w3-padding"><i class="fa fa-calendar fa-fw"></i>  Agendamentos</a>
    <a href="/backend/avaliacoes/listar" class="w3-bar-item w3-button w3-padding"><i class="fa fa-star fa-fw"></i>  Avaliações</a>
    <a href="/backend/pagamentos/listar" class="w3-bar-item w3-button w3-padding"><i class="fa fa-credit-card fa-fw"></i>  Pagamentos</a>
    <a href="/backend/profissionais/listar" class="w3-bar-item w3-button w3-padding"><i class="fa fa-user-md fa-fw"></i>  Profissionais</a>
  </div>
</nav>

<div class="w3-overlay w3-hide-large w3-animate-opacity" onclick="w3_close()" style="cursor:pointer" title="close side menu" id="myOverlay"></div>

<div class="w3-main" style="margin-left:300px; margin-top:43px; padding: 0 24px 24px 24px;">

  <header class="w3-container" style="padding-top:22px">
    <h5><b><i class="fa fa-dashboard"></i> Meu Dashboard</b></h5>
  </header>

    <div class="w3-row-padding w3-margin-bottom">
    <?php 
      // Define um array de cores para variar os cards
      $colors = ['#5D6D68', '#7C8F88', '#A3B8A1', '#8F9E8B'];
      $colorIndex = 0;

      if (isset($stats) && is_array($stats)): 
        foreach ($stats as $stat): 
          $color = $colors[$colorIndex % count($colors)]; 
    ?>
    <div class="w3-quarter">
      <div class="w3-container w3-card-4 w3-padding-16" style="background-color: <?= $color; ?>; color: white; border-radius: 12px;">
        <div class="w3-left"><i class="fa <?= htmlspecialchars($stat['icon']) ?> w3-xxxlarge"></i></div>
        <div class="w3-right">
          <h3><?= htmlspecialchars($stat['value']) ?></h3>
        </div>
        <div class="w3-clear"></div>
        <h4><?= htmlspecialchars($stat['label']) ?></h4>
      </div>
    </div>
    <?php 
          $colorIndex++;
        endforeach; 
      endif; 
    ?>
  </div>