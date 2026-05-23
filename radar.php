<?php 
session_start();
ini_set("display_errors", 1);
ini_set('error_reporting', E_ALL ^ (E_WARNING|E_NOTICE|E_DEPRECATED));
error_reporting(E_ALL & ~(E_WARNING|E_NOTICE|E_DEPRECATED));
date_default_timezone_set('America/Manaus');
clearstatcache();
require "pages/conf/define.php";
require "pages/conf/functions.php";
require "pages/conf/conectaOracle.php";
if (!isset($_SESSION['login'])) {
  limpaSession();
  redireciona("login.php");
}
?>
<!doctype html>
<html lang="pt-br" class="h-100">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Consulta de Peças VEMAP</title>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link href="dist/css/bootstrap.css" rel="stylesheet">
    <link href="dist/css/cover.css" rel="stylesheet">
    <link href="plugins/fontawesome-pro/css/all.css" rel="stylesheet">
    <link href="plugins/DataTables/css/dataTables.bootstrap5.css" rel="stylesheet">
    <link href="plugins/DataTables/css/fixedHeader.bootstrap5.min.css" rel="stylesheet">
    <style>
      /* Ajuste do Body para não colapsar o sticky */
      body {
        padding-top: 70px;
        overflow-x: hidden;
      }

      /* Container da tabela com scroll interno */
      .table-responsive-scroll {
        overflow-y: auto;
        height: calc(100vh - 160px); /* Ajusta conforme a soma das alturas do header+footer */
        margin-bottom: 50px;
      }

      /* O SEGREDO: Sticky no TH */
      #tb_radar thead th {
        position: -webkit-sticky; /* Suporte Safari */
        position: sticky;
        top: 0;
        z-index: 100;
        /*background-color: #212529 !important; /* Cor de fundo sólida para não vazar transparência */
        border-bottom: 2px solid #454d55;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
      }

      /* Garante que a div de info extra também fique fixa acima da tabela */
      #dados-complementares {
        position: sticky;
        top: 0;
        z-index: 1020;
        background-color: #212529;
        border-bottom: 1px solid #444;
      }
      /* Destaque para facilitar o uso do teclado com hover */
      #tb_radar tbody tr:hover {
          background-color: rgba(255, 193, 7, 0.1) !important; /* Um tom leve de warning/amarelo */
          cursor: pointer;
      }
      
      /* Garante que o ícone tenha transição suave */
      .row-check {
          transition: transform 0.1s ease-in-out;
      }
      
      .td-check:active .row-check {
          transform: scale(0.9);
      }
    </style>

    <script type="text/javascript" src="plugins/jquery/dist/jquery-3.5.1.js"></script>
    <script type="text/javascript" src="dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="plugins/DataTables/js/jquery.dataTables.js"></script>
    <script type="text/javascript" src="plugins/DataTables/js/dataTables.bootstrap5.js"></script>
    <script type="text/javascript" src="plugins/DataTables/js/dataTables.fixedHeader.min.js"></script>
    <script type="text/javascript" src="dist/js/radar.js"></script>    

  </head>
  <body class="d-flex flex-column text-white bg-dark">

    <?php 
    require "pages/vendas/function.php";
    require "pages/vendas/controller.php";
    require "pages/vendas/radar_modalVendas.php";
    require "pages/vendas/radar_modalCompras.php";
    require "pages/vendas/radar_modalOrcamentos.php";
    require "pages/vendas/radar_modalEnviarArquivo.php";
    

    if (!empty($_POST)) {
      $dados = $_POST;
    } else {
      $dados = $_GET;
    }

    if ($dados) {
      if (isset($dados['acao'])) {
        switch ($dados['acao']) {
          case 'clear':
            unset($_SESSION['RADAR']);
            redireciona("radar.php");
            break;
          case 'radar_consultaPeca':
            include 'pages/vendas/radar_consultaPeca.php';
            break;
        }
      }
    }
    ?>

  <header>
    <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark border-bottom border-secondary">
        <div class="container-fluid mx-3">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="dist/img/logo_header.png" height="35" class="me-2"> 
                <span class="d-none d-lg-inline">Consulta de Peças VEMAP</span>
            </a>

            <div class="btn-group me-auto" role="group">
              <button class="btn btn-secondary text-white" type="button" data-bs-toggle="modal" data-bs-target="#radar_modalEnviarArquivo" title="Enviar Arquivo para o Radar">
                <i class="fa-solid fa-upload"></i> <span class="d-none d-xl-inline">Enviar Arquivo</span>
              </button>
              <button id="btnExportarOrcamento" class="btn btn-secondary text-white" type="button" title="Gerar um Orçamento com os selecionados">
                  <i class="fa-solid fa-cart-shopping"></i> <span class="d-none d-xl-inline">Orçamento</span>
              </button>
              <button id="btnExportarExcel" class="btn btn-secondary text-white" type="button" title="Exportar os selecionados para Excel">
                  <i class="fa-solid fa-download"></i> <span class="d-none d-xl-inline">Exportar</span>
              </button>
            </div>
            
            <form method="post" action="radar.php">
              <div class="input-group ms-3" style="max-width: 350px;">
                <input name="CODPECA" id="CODPECA" type="text" class="form-control" placeholder="Pesquisar peça..." autocomplete="off" required>
                <button type="submit" name="acao" value="radar_consultaPeca" class="btn btn-outline-success">
                    <i class="fa-solid fa-search"></i>
                </button>
              </div>
            </form>
                
        </div>
    </nav>
  </header>

    <?php if (isset($_SESSION['RADAR'])): ?>
    <main class="flex-shrink-0 w-100">
      <div class="px-0">
        <div id="dados-complementares" class="text-info pt-1 pb-0 px-3 text-start">
            <span id="info-extra" class="badge bg-secondary text-lg">Selecione uma peça...</span>
        </div>

        <div class="table-responsive-scroll px-3 my-0 py-0">
          <table id="tb_radar" class="table table-dark table-bordered w-100 my-0 py-0">
            <thead>
              <tr>
                <th class="text-center" style="width: 50px;">
                  <i class="fa-regular fa-square text-secondary row-check-all" style="cursor:pointer; font-size: 1.2rem;"></i>
                </th>
                <th>Origem</th>
                <th>Cod Peça</th>
                <th>Descrição</th>
                <th>Marca</th>
                <th>Vide</th>
                <th>Codprod</th>
                <th>Estoque</th>
                <!-- <th style="width: 100px;">Qtd</th> -->
                <th style="text-align: right;">Preço</th>
                <!-- <th style="text-align: right;">Total</th> -->
              </tr>
            </thead>
            <tbody>
            <?php 
            $radar = $_SESSION['RADAR'] ?? [];
            foreach ($radar as $key => $value) {
              $infoFornecedor = (isset($value['FORNECEDOR']) ? $value['CODFORNEC'] . " - " . sanitizeOracleString($value['FORNECEDOR']) : '');
              $locacao = sanitizeOracleString($value['LOCACAO'] ?? '9999');

              // Define a quantidade: se já existir na sessão usa ela, senão 0
              $precoVenda = $value['PVENDA'] ?? 0;
              $qtpedida = $value['QTDPEDIDA'] ?? 0;
              $totalLinha = $precoVenda * $qtpedida;

    
              // Verifica se está marcado na sessão para manter o estado após refresh
              $isChecked = isset($value['SELECIONADO']) && $value['SELECIONADO'] === true;
              $iconClass = $isChecked ? 'fa-solid fa-square-check' : 'fa-regular fa-square';

              echo '<tr data-index="'.htmlspecialchars($key, ENT_QUOTES).'"
                        data-numoriginal="'.htmlspecialchars($value['NUMORIGINAL'], ENT_QUOTES).'" 
                        data-fornecedor="'.htmlspecialchars($infoFornecedor, ENT_QUOTES).'" 
                        data-locacao="'.htmlspecialchars($locacao, ENT_QUOTES).'"
                        data-wint="'.htmlspecialchars($value['WINT'], ENT_QUOTES).'"
                        data-dtultent="'.htmlspecialchars($value['DTULTENT'], ENT_QUOTES).'">';
              // Coluna do Checkbox com classe para o JS e ícone dinâmico
              echo '  <td class="text-center td-check" style="cursor:pointer; font-size: 1.2rem;">
                          <i class="'.$iconClass.' row-check"></i>
                      </td>';
              echo '  <td class="text-start">'.sanitizeOracleString($value['ORIGEM']).'</td>';
              echo '  <td class="text-start">'.sanitizeOracleString($value['NUMORIGINAL']).'</td>';
              echo '  <td class="text-start">'.substr(sanitizeOracleString($value['DESCRICAO']), 0, 15).'</td>';
              echo '  <td class="text-start">'.substr(sanitizeOracleString($value['MARCA']), 0, 15).'</td>';
              echo '  <td class="text-start">'.sanitizeOracleString($value['VIDE']).'</td>';
              echo '  <td class="text-start">'.sanitizeOracleString($value['WINT']).'</td>';
              echo '  <td><center>'.($value['QTDISPONIVEL']>0 ? (int)$value['QTDISPONIVEL'] : '').'</center></td>';
              /*echo '  <td>
                          <input type="number" class="form-control form-control-sm input-qtd js-input-qtd" 
                                value="'.$qtpedida.'" 
                                min="0" 
                                style="width: 80px; background: #343a40; color: white; border: 1px solid #6c757d;">
                        </td>';*/
              echo '  <td class="text-end js-valor-unitario" data-preco="'.$precoVenda.'">'.(($precoVenda>0)?moeda($precoVenda,2):'').'</td>';
              /*echo '  <td class="text-end js-total-linha">'.($totalLinha > 0 ? moeda($totalLinha,2) : '').'</td>';*/              
              echo '</tr>';
            }
            ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>    
    
    <footer class="mt-auto py-3 bg-dark fixed-bottom border-top border-secondary">
       <p class="text-warning mb-0" style="font-size: 0.8rem;">
         <em>Alt+L: Limpar | Alt+V: Vendas | Alt+C: Compras | Alt+O: Orçamentos</em>
       </p>
    </footer>
    <?php endif ?>


    
    <?php exibeModal(); ?>
  </body>
</html>