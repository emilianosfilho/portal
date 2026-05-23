<?php
require_once("pages/vendas/function.php");
require_once("pages/vendas/controller.php");
?>

<!-- Main content -->
<section class="content-header">
</section>
<!-- Main content -->
<section class="content">

  <div class="row">
    <div class="col-md-12">
      <div class="box box-primary">
        <div class="box-body">

          <div class="col-md-2">
            <b>NR ORÇAMENTO</b><br>
            <div class="bg-yellow text-black" style="text-align: center; font-size: 2em; font-weight: bold; padding: 0px;">
              <?= $_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO'] ?>
            </div>
          </div>

          <div class="col-md-6">

            <div class="col-sm-4">
              <b>CLIENTE</b><br>
              <input type="text" class="form-control" disabled value="<?= $_SESSION['ORCAMENTO']['CLIENTE']['CODCLI'].' - '.substr($_SESSION['ORCAMENTO']['CLIENTE']['FANTASIA'], 0, 40) ?>">
            </div>

            <div class="col-sm-4">
              <b>VENDEDOR</b><br>
              <input type="text" class="form-control" disabled value="<?= $_SESSION['ORCAMENTO']['VENDEDOR']['CODUSUR'].' '.$_SESSION['ORCAMENTO']['VENDEDOR']['NOME'] ?>">
            </div>

            <div class="col-sm-4">
              <br>
              <a href="#" class="btn bg-purple" data-toggle="modal" data-target="#modalDuplicar">
                <i class="fa fa-copy"></i> Duplicar Orçamento
              </a>
            </div>

          </div>

          <div class="col-md-4">
            <div class="col-sm-6">
              <b>STATUS</b><br>
              <?php
              switch ($_SESSION['ORCAMENTO']['CAB']['STATUS']) {
                  case 'ORCAMENTO': 
                    $bgcollor =  'bg-gray';  
                    $labelStatus = 'ORÇAMENTO'; 
                    break;
                  case 'CANCELADO': 
                    $bgcollor =  'bg-red';    
                    $labelStatus = 'CANCELADO'; 
                    break;
                  case 'REJEITADO': 
                    $bgcollor =  'bg-red';    
                    $labelStatus = 'REJEITADO'; 
                    break;
                  case 'PENDENTE':         
                    $bgcollor =  'bg-yellow';  
                    $labelStatus = 'PENDENTE'; 
                    break;
                  case 'BLOQUEADO': 
                    $bgcollor =  'bg-yellow';    
                    $labelStatus = 'BLOQUEADO'; 
                    break;
                  case 'MONTADO':   
                    $bgcollor =  'bg-blue';   
                    $labelStatus = 'MONTADO'; 
                    break;
                  case 'LIBERADO':  
                    $bgcollor =  'bg-blue';   
                    $labelStatus = 'LIBERADO'; 
                    break;
                  case 'FATURADO':  
                    $bgcollor =  'bg-green';  
                    $labelStatus = 'FATURADO'; 
                    break;
                  default:
                    $bgcollor =  'bg-gray';  
                    $labelStatus = 'ORCAMENTO'; 
                    $_SESSION['ORCAMENTO']['CAB']['STATUS'] = 'ORCAMENTO';
                    break;
                } 
              ?>
              <div class="<?=$bgcollor?> text-white" style="text-align: center; font-size: 1.7em; font-weight: bold; padding: 2px 0px;">
                <?= $labelStatus ?>
              </div>
            </div>

            <div class="col-sm-6">
              <b>TOTAL</b><br>
              <div class="bg-navy text-white" id="valorTotal" style="text-align: center; font-size: 2em; font-weight: bold; padding: 0px;">
                <?php
                  defineValorTotalOrcamento($_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO']);
                ?>
              </div>
            </div>
          </div>


          <?php  
          if ($_SESSION['ORCAMENTO']['CAB']['STATUS'] == 'CANCELADO') {
          ?>
          <div class="col-md-8">
          </div>
          <div class="col-md-4">
            <br><b>Motivo do Cancelamento</b><br>
            <div class="text-bold text-red">
              <?= buscaMotivoCancelamento($_SESSION['ORCAMENTO']['CAB']['NUMPEDRCA']); ?>
            </div>
          </div>
          <?php
          }
          ?>

        </div>
      </div>
    </div>
  </div>


  <?php  
    if ($_SESSION['ORCAMENTO']['CAB']['STATUS'] == 'ORCAMENTO') {
      include("pages/vendas/box_pesquisaPeca.php");
    }

    if (isset($_SESSION['ORCAMENTO']['ITEM'])) {
      unset($_SESSION['ORCAMENTO']['ITEM']);
    }

    if ($_SESSION['ORCAMENTO']['CAB']['STATUS'] == 'ORCAMENTO') {
      
      $_SESSION['ORCAMENTO']['ITEM'] = buscaItensOrcamento($_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO']);
      defineValorTotalOrcamento($_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO']);
      include_once("pages/vendas/listaItensColaborador.php");
    
    } else {
    
      include_once("pages/vendas/invoice.php");
    
    }

  ?>

</section>

<?php  
  include("pages/vendas/modalCliente.php");
  include("pages/vendas/modalVendedor.php");
  include("pages/vendas/modalCobranca.php");
  include("pages/vendas/modalPlpag.php");
  include("pages/vendas/modalAtendimento.php");
  include("pages/vendas/modalObservacao.php");
  include("pages/vendas/modalOrdemCompra.php");
  include("pages/vendas/modalFrete.php");
  include("pages/vendas/modalMaquina.php");
  include('pages/vendas/modalContato.php');
  include('pages/vendas/modalClienteBalcao.php');        
?>