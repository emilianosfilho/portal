<?php 
session_start();
ini_set("display_errors", 1);
ini_set('error_reporting', E_ALL ^ E_NOTICE);
ini_set('memory_limit', '24G');
error_reporting(E_ALL & ~(E_WARNING|E_NOTICE|E_DEPRECATED));
date_default_timezone_set('America/Manaus');
require_once "pages/conf/define.php";
require_once "pages/conf/functions.php";
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <title><?= @APP_APELIDO.' - '.@APP_NOME; ?></title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <!-- Bootstrap core CSS -->
    <link href="dist/css/bootstrap.css" rel="stylesheet">
    <link href="dist/css/login.css" rel="stylesheet">
    <link href="plugins/fontawesome-free/css/all.css" rel="stylesheet">
  </head>
  <body class="text-center">
    <main class="form-signin">
      <?php  
      require_once "pages/conf/conectaOracle.php";
      require_once "pages/admin/function.php";
      require_once "pages/admin/controller.php";
      ?>
      <img class="mb-4 w-75 h-75" src="<?=@DIR_IMG.'logo_header.png'?>" >

      <form action="login.php" method="POST" autocomplete="off">
        <input type="text" style="display:none" readonly>
        <input type="email" style="display:none" readonly>
        <input type="password" style="display:none" readonly>

        <h1 class="h3 mb-3 fw-normal">Informe seus dados</h1>

        <div class="form-floating">
          <input name="login" type="text" class="form-control form-control-lg" id="login" required autocomplete="off">
          <label for="login">Usuário</label>
        </div>
        
        <div class="form-floating">
          <input name="email" type="email" class="form-control form-control-lg" id="email" required autocomplete="off">
          <label for="email">E-mail</label>
        </div>
        
        <button type="submit" name="acao" value="usuario_redefinirSenha" class="w-100 mt-2 btn btn-xl btn-primary">Redefinir minha senha</button>
        <a href="login.php" class="w-100 mt-2 btn btn-xl btn-secondary">Login</a>
      </form>


    </main>

    <?php exibeModal(); ?>
    <script type="text/javascript" src="plugins/jquery/dist/jquery-3.5.1.js"></script>
    <script type="text/javascript" src="dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript">
      $(document).ready(function () {
        document.getElementById("login").focus();
        $('#myModal0').modal('show');
        $('#myModal1').modal('show');
      });
    </script>
  </body>
</html>
