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
        .w3-button:hover { background-color: #e0e0e0 !important; }
        .w3-card-4 { border-radius: 12px; }
        .header-logo { max-width: 250px; margin: 20px auto;}
        .w3-bar .w3-bar-item { color: white; }
    </style>
</head>
<body style="background-color: #faf6eed9;">>

<div class="w3-bar w3-top w3-large" style="z-index:4; background-color: #5D6D68;">
  <button class="w3-bar-item w3-button w3-hide-large w3-hover-none w3-hover-text-light-grey" onclick="w3_open();"><i class="fa fa-bars"></i>  Menu</button>
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
    <a href="/backend/usuario/listar" class="w3-bar-item w3-button w3-padding"><i class="fa fa-users fa-fw"></i>  Criar Usuário</a>
  </div>
</nav>

<div class="w3-overlay w3-hide-large w3-animate-opacity" onclick="w3_close()" style="cursor:pointer" title="close side menu" id="myOverlay"></div>

<div class="w3-main" style="margin-left:300px;margin-top:43px;">

  <header class="w3-container" style="padding-top:22px">
    <h5><b><i class="fa fa-dashboard"></i> Meu Dashboard</b></h5>
  </header>

  <div class="w3-row-padding w3-margin-bottom">
    <div class="w3-quarter">
      <div class="w3-container w3-card-4 w3-padding-16" style="background-color: #5D6D68; color: white;">
        <div class="w3-left"><i class="fa fa-star w3-xxxlarge"></i></div>
        <div class="w3-right">
          <h3><?= $stats['total_avaliacoes'] ?? '0' ?></h3>
        </div>
        <div class="w3-clear"></div>
        <h4>Avaliações</h4>
      </div>
    </div>
    <div class="w3-quarter">
      <div class="w3-container w3-card-4 w3-padding-16" style="background-color: #7C8F88; color: white;">
        <div class="w3-left"><i class="fa fa-check-circle w3-xxxlarge"></i></div>
        <div class="w3-right">
          <h3><?= $stats['usuarios_ativos'] ?? '0' ?></h3>
        </div>
        <div class="w3-clear"></div>
        <h4>Usuários Ativos</h4>
      </div>
    </div>
    <div class="w3-quarter">
      <div class="w3-container w3-card-4 w3-padding-16" style="background-color: #A3B8A1; color: white;">
        <div class="w3-left"><i class="fa fa-times-circle w3-xxxlarge"></i></div>
        <div class="w3-right">
          <h3><?= $stats['usuarios_inativos'] ?? '0' ?></h3>
        </div>
        <div class="w3-clear"></div>
        <h4>Usuários Inativos</h4>
      </div>
    </div>
    <div class="w3-quarter">
      <div class="w3-container w3-card-4 w3-padding-16" style="background-color: #E0E0E0; color: #5D6D68;">
        <div class="w3-left"><i class="fa fa-users w3-xxxlarge"></i></div>
        <div class="w3-right">
          <h3><?= $stats['total_usuarios'] ?? '0' ?></h3>
        </div>
        <div class="w3-clear"></div>
        <h4>Usuários</h4>
      </div>
    </div>
  </div>