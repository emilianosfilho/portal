<?php 
// Configura o cookie para expirar ao fechar o navegador
session_set_cookie_params(0); 

session_start();
ini_set("display_errors", 1);
ini_set('error_reporting', E_ALL ^ E_NOTICE);
error_reporting(E_ALL & ~(E_WARNING|E_NOTICE));
date_default_timezone_set('America/Manaus');
require __DIR__."/pages/conf/define.php";
require __DIR__."/pages/conf/functions.php";
require __DIR__."/pages/conf/conectaOracle.php";
?>
<!doctype html>
<html lang="pt-br" class="h-100">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.88.1">
    <title><?= @APP_APELIDO.' - '.@APP_NOME; ?></title>
    <link rel="icon" href="favicon.ico" type="image/png">
    <meta name="theme-color" content="#7952b3">
    <link href="dist/css/bootstrap.css" rel="stylesheet">
    <link href="dist/css/custom.css" rel="stylesheet">
    <link href="dist/css/dashboard.css" rel="stylesheet">
    <link href="dist/css/sticky-footer-navbar.css" rel="stylesheet">
    <link href="plugins/fontawesome-free/css/all.css" rel="stylesheet">
    <link href="plugins/DataTables/css/dataTables.bootstrap5.css" rel="stylesheet">
    <link href="plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.css" rel="stylesheet" />
    <link href="plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css" rel="stylesheet" />
    <link href="plugins/select2/dist/css/select2.css" rel="stylesheet" />
    <link href="plugins/select2/dist/css/select2-bootstrap-5-theme.css" rel="stylesheet" />
    <link href="plugins/bootstrap-image-checkbox/dist/css/bootstrap-image-checkbox.css" rel="stylesheet" />

  </head>
  <body class="d-flex flex-column h-100 text-sm">
    <?php 
      limpaSession();
      require_once("pages/conf/header.php"); 
      
      if(isset($_POST) && !empty($_POST)){
         $dados = $_POST;
      } else {
        if(isset($_GET) && !empty($_GET)){
           $dados = $_GET;
        } else {
          $dados = array();
          $dados['op'] = '0';
        }
      }
      // varDump2($dados);


      ######################  M A N U T E N Ç Ã O  ######################
      $manutencao = true;
      $manutencao = false;
      if ($manutencao && $_SESSION['login']['IDUSUARIO'] <> "1") {
        $dados['op'] = 6;
      }

      switch ($dados['op']) {
          // Inicio
          case '0':       require_once("pages/conf/home2.php");         break;

          case '10':      require_once("pages/cliente/home.php"); break;
          
          default:       require_once("pages/conf/404.php");         break;
      
      } // switch
      exibeModal();

    ?>
    <br>

    <script type="text/javascript" src="plugins/jquery/dist/jquery-3.5.1.js"></script>
    <script type="text/javascript" src="dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="plugins/DataTables/js/jquery.dataTables.js"></script>
    <script type="text/javascript" src="plugins/DataTables/js/dataTables.bootstrap5.js"></script>
    <script type="text/javascript" src="plugins/jquery.mask/dist/jquery.mask.js"></script>
    <script type="text/javascript" src="plugins/select2/dist/js/select2.full.js"></script>
    <script type="text/javascript" src="plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.js"></script>
    <script type="text/javascript" src="plugins/bootstrap-datepicker/dist/locales/bootstrap-datepicker.pt-BR.min.js"></script>
    <script type="text/javascript" src="dist/js/custom.js"></script>
    <script type="text/javascript" src="dist/js/feather.js"></script>
    <script type="text/javascript" src="dist/js/dashboard.js"></script>
    <?php if (isset($_SESSION['ORCAMENTO'])): ?>
      <script type="text/javascript" src="dist/js/ajaxDuplicarOrcamento.js"></script>
    <?php endif ?>

  </body>
</html>
