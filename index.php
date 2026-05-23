<?php 
// Configura o cookie para expirar ao fechar o navegador
session_set_cookie_params(0); 

session_start();
ini_set("display_errors", 1);
ini_set('error_reporting', E_ALL ^ (E_WARNING|E_NOTICE|E_DEPRECATED));
error_reporting(E_ALL & ~(E_WARNING|E_NOTICE|E_DEPRECATED));
date_default_timezone_set('America/Manaus');
clearstatcache();

require __DIR__."/pages/conf/define.php";
require __DIR__."/pages/conf/functions.php";
require __DIR__."/pages/conf/conectaOracle.php";
require __DIR__."/pages/chamados/function.php";
require __DIR__."/plugins/PHPMailer/function.php";
?>
<!doctype html>
<html lang="pt-br" class="h-100">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= @APP_APELIDO.' - '.@APP_NOME; ?></title>
    <link rel="icon" href="favicon.ico" type="image/png">
    <meta name="theme-color" content="#7952b3">
    <link href="dist/css/bootstrap.css" rel="stylesheet">
    <link href="dist/css/custom.css" rel="stylesheet">
    <link href="dist/css/sticky-footer-navbar.css" rel="stylesheet">
    <link href="dist/css/small-box.css" rel="stylesheet">
    <link href="dist/css/direct-chat.css" rel="stylesheet">
    <link href="dist/css/prompt.css" rel="stylesheet">
    <link href="dist/css/loader.css" rel="stylesheet">
    <link href="plugins/fontawesome-pro/css/all.css" rel="stylesheet">
    <link href="plugins/DataTables/css/dataTables.bootstrap5.css" rel="stylesheet">
    <link href="plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.css" rel="stylesheet" />
    <link href="plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css" rel="stylesheet" />
    <link href="plugins/select2/dist/css/select2.css" rel="stylesheet" />
    <link href="plugins/select2/dist/css/select2-bootstrap-5-theme.css" rel="stylesheet" />
    <link href="plugins/bootstrap-image-checkbox/dist/css/bootstrap-image-checkbox.css" rel="stylesheet" />
    <link href="plugins/ionicons/css/ionicons.css" rel="stylesheet" >
    <style>
    #myProgress {
      width: 100%;
      background-color: #ddd;
    }

    #myBar {
      width: 1%;
      height: 30px;
      background-color: #04AA6D;
    }
    </style>
  </head>
  <body class="d-flex flex-column h-100 <?= (($_SESSION['login']['IDUSUARIO']!=2)?'text-sm':'')?> ">
    <script type="text/javascript" src="plugins/jquery/dist/jquery-3.5.1.js"></script>
    <script type="text/javascript" src="plugins/select2/dist/js/select2.full.js"></script>

    <?php 
      if (!isset($_SESSION['login'])) {
        limpaSession();
        redireciona("index2.php");
      }
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

      ######################  M A N U T E N Ç Ã O  ######################
      $manutencao = true;
      $manutencao = false;
      if ($manutencao && $_SESSION['login']['IDUSUARIO'] <> "1") {
        $dados['op'] = 6;
      }

      switch ($dados['op']) {
          // Inicio
          case '0':       
            if($_SESSION['login']['PERFIL']=='CLIENTE')
            {   require_once("pages/cliente/home.php");   break; }
            else if($_SESSION['login']['IDUSUARIO'] == 1)
            {   require_once("pages/conf/home_rca.php");   break; }
            else
            {   require_once("pages/conf/home.php");   break; }

          case '1':       require_once("pages/conf/manutencao.php");         break;
          case '2':       require_once("pages/admin/admin-locker.php");         break;
          case '3':       require_once("pages/admin/admin-plsql.php");         break;
          case '4':       require_once("pages/admin/equipamento_pesquisa.php");         break;
          case '5':       require_once("pages/admin/uploads.php");         break;
          case '6':       require_once("pages/admin/downloads.php");         break;
          case '7':       require_once("pages/admin/etiquetas.php");         break;
          
          // Usuario
          case '10':      require_once("pages/admin/usuario_editar.php");   break;
          case '11':      require_once("pages/admin/usuario_listar.php");         break;
          case '12':      require_once("pages/admin/usuario_profile.php");         break;
                
          // MENSAGEIRO
          case '35':      require_once("pages/chamados/list.php");   break;
          case '36':      require_once("pages/chamados/chamado.php");   break;

          // ORCAMENTO ARQUIVO
          case '51':      require_once("pages/orcamentoArquivo/formEnvia.php");         break;
          case '52':      require_once("pages/orcamentoArquivo/formProcessamento.php");         break;
          case '53':      require_once("pages/orcamentoArquivo/formValidacao.php");         break;
          case '54':      require_once("pages/orcamentoArquivo/controller.php");         break;
          case '55':      require_once("pages/orcamentoArquivo/pesquisaItens.php");         break;


          // ORCAMENTO COLABORADOR
          case '60':      require_once("pages/vendas/controller.php");         break;
          case '61':      require_once("pages/vendas/pesquisaCliente.php");         break;
          case '62':      require_once("pages/vendas/orcamento.php");         break;
          case '66':      require_once("pages/vendas/faturar.php");         break;
          case '67':      require_once("pages/vendas/emitirPedidoVenda.php");         break;
          case '68':      require_once("pages/vendas/pesquisaOrcamento.php");         break;

          // ARQUIVOS RADAR
          case '70':      require_once("pages/radar/pesquisa.php");         break;

          // CONJUNTO
          case '81':      require_once("pages/conjunto/pesquisaConjunto.php");         break;
          case '82':      require_once("pages/conjunto/pesquisaConjuntoItem.php");         break;

          //RELATORIOS
          case '90':      require_once("pages/relatorios/listaRelatorios.php"); break;
          case '92':      require_once("pages/relatorios/formEmitir.php"); break;
          case '93':      require_once("pages/relatorios/formCadastro.php"); break; // Interface do formulário          

          //ORCAMENTO CLIENTE
          case '100':      require_once("pages/orcamentoCliente/controller.php"); break;
          case '101':      require_once("pages/orcamentoCliente/pesquisa.php"); break;
          case '102':      require_once("pages/orcamentoCliente/view.php"); break;
          case '103':      require_once("pages/orcamentoCliente/assumir.php"); break;

          //COMPRAS
          case '110':      require_once("pages/compras/produto-pesquisa.php"); break;
          case '111':      require_once("pages/compras/produto-preview.php"); break;
          case '112':      require_once("pages/compras/produto-resultado.php"); break;

          case '114':      require_once("pages/compras/precificacao-pesquisa.php"); break;
          case '115':      require_once("pages/compras/precificacao-preview.php"); break;
          case '116':      require_once("pages/compras/precificacao-resultado.php"); break;

          case '117':      require_once("pages/compras/pedcompra-pesquisa.php"); break;
          case '118':      require_once("pages/compras/pedcompra-envia.php"); break;
          case '119':      require_once("pages/compras/pedcompra-preview.php"); break;
          case '120':      require_once("pages/compras/pedcompra-resultado.php"); break;

          case '121':      require_once("pages/compras/vide-pesquisa.php"); break;
          case '122':      require_once("pages/compras/vide-envia.php"); break;
          case '123':      require_once("pages/compras/vide-preview.php"); break;

          case '124':      require_once("pages/compras/analise-envia.php"); break;
          case '125':      require_once("pages/compras/analise-preview.php"); break;
          
          case '126':      require_once("pages/compras/classes_pesquisa.php"); break;
          case '127':      require_once("pages/compras/classes_preview.php"); break;
          case '128':      require_once("pages/compras/classes_resultado.php"); break;


          //LOGISTICA
          case '130':      require_once("pages/logistica/cancelados-conferencia.php"); break;
          case '131':      require_once("pages/logistica/produto_pesquisa.php"); break;
          case '132':      require_once("pages/logistica/checkout-pesquisa.php"); break;
          case '133':      require_once("pages/logistica/checkin_pesquisa.php"); break;
          case '134':      require_once("pages/logistica/inventario-pesquisa.php"); break;
          case '135':      require_once("pages/logistica/checkin_separacao.php"); break;
          case '136':      require_once("pages/logistica/notaFiscal-espelho.php"); break;
          case '137':      require_once("pages/logistica/checkout-separacao.php"); break;
          case '138':      require_once("pages/logistica/locacao-pesquisa.php"); break;
          case '139':      require_once("pages/logistica/inventario-conferencia.php"); break;
          case '148':      require_once("pages/logistica/ficha_pesquisa.php"); break;
          case '149':      require_once("pages/logistica/equip_pesquisa.php"); break;
          case '40':       require_once("pages/logistica/vide-pesquisar.php");  break;
          case '41':       require_once("pages/logistica/vide-preview.php");   break;
          case '42':       require_once("pages/logistica/vide-resultado.php"); break;
          case '43':       require_once("pages/logistica/etiqueta_home.php"); break;

          //FINANCEIRO
          case '140':      require_once("pages/financeiro/formFichas.php"); break;
          case '141':      require_once("pages/financeiro/ficha_pj.php"); break;
          case '142':      require_once("pages/financeiro/ficha_pf.php"); break;
          case '143':      require_once("pages/financeiro/listaFichas.php"); break;
          case '144':      require_once("pages/financeiro/aliquotaRR.php"); break;

          //CLIENTE
          case '150':      require_once("pages/cliente/controller.php"); break;
          case '151':      require_once("pages/cliente/orcamento.php"); break;

          case '161':      require_once("pages/ecommerce/bagy_pesquisa.php"); break;
          case '162':      require_once("pages/ecommerce/bagy_produto_edit.php"); break;
          
          case '171':      require_once("pages/ecommerce/tray_produto_pesquisa.php"); break;
          case '172':      require_once("pages/ecommerce/tray_produto_edit.php"); break;
          
          default:       require_once("pages/conf/404.php");         break;
      
      } // switch
      exibeModal();
      exibeAlerta();
    ?>
    <br>
    
    <script type="text/javascript" src="dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="dist/js/p5.min.js"></script>
    <script type="text/javascript" src="plugins/DataTables/js/jquery.dataTables.js"></script>
    <script type="text/javascript" src="plugins/DataTables/js/dataTables.bootstrap5.js"></script>
    <script type="text/javascript" src="plugins/jquery.mask/dist/jquery.mask.js"></script>
    
    <script type="text/javascript" src="plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.js"></script>
    <script type="text/javascript" src="plugins/bootstrap-datepicker/dist/locales/bootstrap-datepicker.pt-BR.min.js"></script>
    <script type="text/javascript" src="dist/js/custom.js"></script>
    <?php if (isset($_SESSION['ORCAMENTO'])): ?>
      <script type="text/javascript" src="dist/js/ajaxDuplicarOrcamento.js"></script>
    <?php endif ?>

  </body>
</html>
