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
  <div class="w3-container" style="padding-left: 16px; color: white;">
    <?php if (isset($_SESSION['usuario_nome'])): ?>
        <h5 style="font-size: 1.2rem; margin-bottom: 2px; word-wrap: break-word;">Olá, <?= htmlspecialchars($_SESSION['usuario_nome']) ?></h5>
    <?php endif; ?>

    <?php if (isset($_SESSION['usuario_tipo'])): ?>
        <span style="font-size: 0.9rem; text-transform: capitalize; opacity: 0.8;">
            (<?= htmlspecialchars($_SESSION['usuario_tipo']) ?>)
        </span>
    <?php endif; ?>
  </div>
  <hr style="margin-top: 16px;">
  <div class="w3-container">
    <h5 style="color: white; font-size: 1.5rem">Menu</h5>
  </div>
  <div class="w3-bar-block">
    <a href="#" class="w3-bar-item w3-button w3-padding-16 w3-hide-large w3-dark-grey w3-hover-black" onclick="w3_close()" title="close menu"><i class="fa fa-remove fa-fw"></i> Fechar Menu</a>

    <?php
    // Pega o tipo de usuário da sessão
    $userType = $_SESSION['usuario_tipo'] ?? '';
    ?>

    <?php // Dashboard: Visível para admin, profissional e recepcionista ?>
    <?php if (in_array($userType, ['admin', 'profissional', 'recepcionista'])): ?>
        <a href="/backend/dashboard" class="w3-bar-item w3-button w3-padding <?= (strpos($_SERVER['REQUEST_URI'], '/backend/dashboard') !== false) ? 'w3-light-grey' : ''; ?>"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
    <?php endif; ?>

    <?php // --- LINK "MEU PERFIL" ADICIONADO --- ?>
    <?php // Visível para todos os tipos logados ?>
    <?php if (in_array($userType, ['admin', 'profissional', 'recepcionista'])): ?>
        <a href="/backend/meu-perfil" class="w3-bar-item w3-button w3-padding <?= (strpos($_SERVER['REQUEST_URI'], '/backend/meu-perfil') !== false || strpos($_SERVER['REQUEST_URI'], '/backend/usuario/editar/' . ($_SESSION['usuario_id'] ?? '')) !== false) ? 'w3-light-grey' : ''; ?>"><i class="fa fa-user fa-fw"></i> Meu Perfil</a>
    <?php endif; ?>

    <?php // Usuários: Visível apenas para admin ?>
    <?php if ($userType === 'admin'): ?>
        <a href="/backend/usuario/listar" class="w3-bar-item w3-button w3-padding <?= (strpos($_SERVER['REQUEST_URI'], '/backend/usuario/listar') !== false) ? 'w3-light-grey' : ''; ?>"><i class="fa fa-users fa-fw"></i> Usuários</a>
    <?php endif; ?>

    <?php // Agendamentos: Visível para admin, profissional e recepcionista ?>
    <?php if (in_array($userType, ['admin', 'profissional', 'recepcionista'])): ?>
        <a href="/backend/agendamentos/listar" class="w3-bar-item w3-button w3-padding <?= (strpos($_SERVER['REQUEST_URI'], '/backend/agendamentos') !== false) ? 'w3-light-grey' : ''; ?>"><i class="fa fa-calendar fa-fw"></i> Agendamentos</a>
    <?php endif; ?>

    <?php // --- LINK "MEU PERFIL PROFISSIONAL" (NOVO) --- ?>
    <?php // Visível apenas para Profissional ?>
    <?php if ($userType === 'profissional'): ?>
        <a href="/backend/profissional/meu-perfil" class="w3-bar-item w3-button w3-padding <?= (strpos($_SERVER['REQUEST_URI'], '/backend/profissional/meu-perfil') !== false) ? 'w3-light-grey' : ''; ?>"><i class="fa fa-user-md fa-fw"></i> Meu Perfil Profissional</a>
    <?php endif; ?>
    
    <?php // Avaliações: Visível apenas para admin ?>
    <?php if ($userType === 'admin'): ?>
        <a href="/backend/avaliacoes/listar" class="w3-bar-item w3-button w3-padding <?= (strpos($_SERVER['REQUEST_URI'], '/backend/avaliacoes') !== false) ? 'w3-light-grey' : ''; ?>"><i class="fa fa-star fa-fw"></i> Avaliações</a>
    <?php endif; ?>

    <?php // Pagamentos: Visível para admin e recepcionista ?>
    <?php if (in_array($userType, ['admin', 'recepcionista'])): ?>
        <a href="/backend/pagamentos/listar" class="w3-bar-item w3-button w3-padding <?= (strpos($_SERVER['REQUEST_URI'], '/backend/pagamentos') !== false) ? 'w3-light-grey' : ''; ?>"><i class="fa fa-credit-card fa-fw"></i> Pagamentos</a>
    <?php endif; ?>

    <?php // Profissionais: Visível para admin e profissional ?>
    <?php if (in_array($userType, ['admin'])): ?>
        <a href="/backend/profissionais/listar" class="w3-bar-item w3-button w3-padding <?= (strpos($_SERVER['REQUEST_URI'], '/backend/profissionais') !== false) ? 'w3-light-grey' : ''; ?>"><i class="fa fa-user-md fa-fw"></i> Profissionais</a>
    <?php endif; ?>

    <?php // Imagens do Site: Visível apenas para admin ?>
    <?php if ($userType === 'admin'): ?>
        <a href="/backend/imagens/listar" class="w3-bar-item w3-button w3-padding <?= (strpos($_SERVER['REQUEST_URI'], '/backend/imagens') !== false) ? 'w3-light-grey' : ''; ?>"><i class="fa fa-picture-o fa-fw"></i> Imagens do Site</a>
    <?php endif; ?>

    <?php // Sair: Visível para todos os tipos logados no backend ?>
    <?php if (in_array($userType, ['admin', 'profissional', 'recepcionista', 'cliente'])): ?>
        <a href="/backend/logout" class="w3-bar-item w3-button w3-padding"><i class="fa fa-sign-out fa-fw"></i> Sair</a>
    <?php endif; ?>

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