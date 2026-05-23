<?php 
$timeout = 28800;//28.800 segundos = 480 minutos = 8 horas
ini_set( "session.gc_maxlifetime", $timeout );// Defina o máximo de tempo da sessão
ini_set( "session.cookie_lifetime", $timeout );// Defina a vida útil do cookie da sessão
session_start();

ini_set("display_errors", 1);
ini_set('error_reporting', E_ALL ^ (E_WARNING|E_NOTICE|E_DEPRECATED));
error_reporting(E_ALL & ~(E_WARNING|E_NOTICE|E_DEPRECATED));
date_default_timezone_set('America/Manaus');
clearstatcache();
require __DIR__."/pages/conf/define.php";
require __DIR__."/pages/conf/functions.php";
require __DIR__."/pages/conf/conectaOracle.php";
require __DIR__."/plugins/PHPMailer/function.php";
?>
<!doctype html>
<html lang="pt-br" class="h-100">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.88.1">
    <title>FICHA CADASTRO DE CLIENTE</title>
    <link rel="icon" href="favicon.ico" type="image/png">
    <meta name="theme-color" content="#7952b3">
    <link href="dist/css/bootstrap.css" rel="stylesheet">
    <link href="dist/css/bd-example-row.css" rel="stylesheet">
    <link href="dist/css/sticky-footer-navbar.css" rel="stylesheet">
    <link href="plugins/fontawesome-free/css/all.css" rel="stylesheet">
    <link href="plugins/DataTables/css/dataTables.bootstrap5.css" rel="stylesheet">
    <link href="plugins/datepicker/css/bootstrap-datepicker.css" rel="stylesheet" />
    <link href="plugins/datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" />
    <link href="plugins/select2/dist/css/select2.css" rel="stylesheet" />
    <link href="plugins/select2/dist/css/select2-bootstrap-5-theme.css" rel="stylesheet" />
    <link href="plugins/bootstrap-image-checkbox/dist/css/bootstrap-image-checkbox.css" rel="stylesheet" />

  </head>
  <body class="text-sm">

    <div class="container-lg">
      <div class="row mt-4">
        <main class="col-lg-12 m-4">

          <?php 
            require_once("pages/conf/header_ficha.php"); 
            require_once("pages/financeiro/function.php"); 
            if(isset($_POST) && !empty($_POST)){
               $dados = $_POST;
            } else {
              if(isset($_GET) && !empty($_GET)){
                 $dados = $_GET;
              } else {
                $dados = false;
              }
            }
            
            if (!isset($dados['TIPOFJ'])) {
              $dados['TIPOFJ'] = 'inicio';
            }
            
            switch ($dados['TIPOFJ']) {
              case 'F':
                require_once("pages/financeiro/fichaPF.php"); 
                break;

              case 'J':
                require_once("pages/financeiro/fichaPJ.php"); 
                break;
              
              default:
                require_once("pages/financeiro/inicio.php"); 
                break;
            }
            exibeModal();
            ?>
        </main>
      </div>
    </div>

    <script type="text/javascript" src="plugins/jquery/dist/jquery-3.5.1.js"></script>
    <script type="text/javascript" src="plugins/jquery.mask/dist/jquery.mask.js"></script>
    <script type="text/javascript" src="dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="dist/js/inputMask.js"></script>
    <script type="text/javascript" src="plugins/DataTables/js/jquery.dataTables.js"></script>
    <script type="text/javascript" src="plugins/DataTables/js/dataTables.bootstrap5.js"></script>
    <script type="text/javascript" src="plugins/select2/dist/js/select2.full.js"></script>
    <script type="text/javascript">
    $(document).ready(function () {
      $('#myModal0').modal('show');
      $('#myModal1').modal('show');
      $('#myModal2').modal('show');
      $('#myModal3').modal('show');
      $('#myModal4').modal('show');
      $('#myModal5').modal('show');
    })
    </script>
  </body>
</html>
