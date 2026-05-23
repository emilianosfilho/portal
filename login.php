<?php 
// Configura o cookie para expirar ao fechar o navegador
session_set_cookie_params(0); 

session_start();
ini_set("display_errors", 1);
ini_set('error_reporting', E_ALL ^ E_NOTICE);
ini_set('memory_limit', '24G');
error_reporting(E_ALL & ~(E_WARNING|E_NOTICE|E_DEPRECATED));
date_default_timezone_set('America/Manaus');
require_once "pages/conf/define.php";
require_once "pages/conf/functions.php";

if ($_GET['acao'] == 'sair') {
  $_SESSION = array(); // Limpa as variáveis
  session_destroy(); // Destrói a sessão no servidor
}
?>
<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Elohim Soluções">
    <meta name="generator" content="Emiliano Filho">
    <title><?= @APP_APELIDO.' - '.@APP_NOME; ?></title>
    <!-- Bootstrap core CSS -->
    <link href="dist/css/bootstrap.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="dist/css/signin.css" rel="stylesheet">
    <link href="plugins/fontawesome-free/css/all.css" rel="stylesheet">
    <link rel="icon" href="favicon.ico">
  </head>
  <body class="text-center bg-portal">
    <?php  
    require_once "pages/conf/conectaOracle.php";
    require_once "pages/admin/function.php";
    require_once "pages/admin/controller.php";
    ?>
    
    <main class="form-signin bg-light text-dark shadow-lg rounded">

      <form action="login.php" method="POST" autocomplete="off">
        <input type="text" style="display:none" readonly>
        <input type="email" style="display:none" readonly>
        <input type="password" style="display:none" readonly>

        <img class="mb-4" src="<?= DIR_IMG."logo_header.png"?>" alt="" width="200">
        <h1 class="h3 mb-3 fw-normal">Faça o login</h1>

        <div class="form-floating">
          <input name="login" type="text" class="form-control form-control-lg" id="login" required autocomplete="off">
          <label for="login">Usuário</label>
        </div>
        
        <div class="form-floating">
          <input type="hidden" name="senha" id="senha_real">
          <input
            name="senha_exibida"
            type="text"
            class="form-control form-control-lg"
            id="password"
            required
            autocomplete="off"
          >
          <label for="password">Senha</label>
        </div>

        <button type="submit" name="acao" value="usuario_login" class="w-100 btn btn-lg btn-success my-3 shadow ">Login</button>
        <a href="esqueci.php" class="link-primary">Esqueci minha senha</a>
        <p class="mt-3 mb-3">&copy; 2015-<?=@date('Y')?></p>
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

    <script>
      const inputPass   = document.getElementById('password');
      const inputHidden = document.getElementById('senha_real');

      let realValue = '';

      inputPass.addEventListener('input', function (e) {
        const value = inputPass.value;

        // Se o usuário apagou tudo
        if (value.length === 0) {
          realValue = '';
          return;
        }

        // Detecta digitação ou colagem
        if (value.length > realValue.length) {
          const diff = value.length - realValue.length;

          // Captura os caracteres reais digitados
          const typed = value.slice(-diff);
          realValue += typed;
        }

        // Detecta backspace
        if (value.length < realValue.length) {
          realValue = realValue.slice(0, value.length);
        }

        // Aplica máscara visual
        inputPass.value = '•'.repeat(realValue.length);
      });

      inputPass.addEventListener('paste', function (e) {
        e.preventDefault();
        const paste = (e.clipboardData || window.clipboardData).getData('text');
        realValue += paste;
        inputPass.value = '•'.repeat(realValue.length);
      });

      document.querySelector('form').addEventListener('submit', function () {
        inputHidden.value = realValue;
      });
    </script>




  </body>
</html>
