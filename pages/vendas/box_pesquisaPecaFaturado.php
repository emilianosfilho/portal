<div class="divider"></div>

<div class="row">
  <div class="col-md-12">
    <div class="box box-primary">
      <div class="box-body">
        <div class="row">
          <div class="col-sm-2">
            <b>NUMPEDRCA</b><br>
            <?= $_SESSION['ORCAMENTO']['CAB']['NUMPEDRCA'] ?>
          </div>
          <div class="col-sm-2">
            <b>Cobrança</b></br>
            <?= $_SESSION['ORCAMENTO']['COBRANCA']['CODCOB'].'- '.$_SESSION['ORCAMENTO']['COBRANCA']['COBRANCA'] ?>
          </div>
          <div class="col-sm-2">
            <b>Plano Pagamento</b><br>
            <?= $_SESSION['ORCAMENTO']['PLPAG']['CODPLPAG'].'- '.$_SESSION['ORCAMENTO']['PLPAG']['DESCRICAO']?>
          </div>
          <div class="col-sm-6">
            <b>Ações</b><br>
            <form method="post" action="#" class="btn-group">
              <input type="hidden" name="op" value="62">
              <input type="hidden" name="IDORCAMENTO" value="<?=$_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO']?>">
              <input type="hidden" name="NUMPEDRCA" value="<?=$_SESSION['ORCAMENTO']['CAB']['NUMPEDRCA']?>">
              <button type="submit" class="btn btn-primary btn-sm" name="exportaPDFPedidoVenda">
                <i class="fa fa-file-o"></i> Rel 317
              </button>
              <button type="submit" class="btn btn-primary btn-sm" name="exportaPDFEtiquetaVenda">
                <i class="fa fa-print"></i> Etiquetas
              </button>
              <button type="submit" class="btn btn-primary btn-sm" name="exportaPDF">
                <i class="fa fa-tag"></i> exportaPDF
              </button>
              <button type="submit" class="btn btn-success btn-sm" name="duplicarOrcamento">
                <i class="fa fa-copy"></i> Duplicar
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<?php  
include("pages/vendas/modalAcompanhaFaturado.php");
include("pages/vendas/modalDuplicar.php");
?>
