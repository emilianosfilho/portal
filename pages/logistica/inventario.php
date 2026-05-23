<div class="container">
  <?php  
    require_once("pages/inventario/function.php");
    require_once("pages/inventario/controller.php");

    if ($_GET['acao'] == "abrir") {
      unset($_SESSION['INVENTARIO']);
    }
    if (!isset($_SESSION['INVENTARIO'])) {
      if (!isset($_GET['IDINVENTARIO'])) {
        exibeMensagem("IDINVENTARIO não identificado!");
      } else {
        $_SESSION['INVENTARIO'] = buscaCabecalhoInventario($_GET['IDINVENTARIO']);
        $_SESSION['INVENTARIO']['ITENS'] = buscaItensInventario($_GET['IDINVENTARIO']);
      }
    }
  ?>

  <div class="col-12">

    <h1 class="bd-title">
      <i class="fas fa-tasks"></i> Conferindo Locação <?=$_SESSION['INVENTARIO']["LOCACAO"]?>
    </h1>


    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-6">
            <label class="form-label">Data Início</label>
            <input type="text" class="form-control" placeholder="Data Início" value="<?=formataDataOracletoBr($_SESSION['INVENTARIO']['DTINICIO'])?>" disabled/>
          </div>
          <div class="col-6">
            <label class="form-label">Tempo de execução</label>
            <input type="text" class="form-control" placeholder="TEMPO" value="<?=$_SESSION['INVENTARIO']['TEMPO']?>" disabled/>
          </div>
          <div class="col-12">
            <label class="form-label">Código Winthor</label>
            <form class="form-horizontal" role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
              <input type="hidden" name="op" value="21">
              <input type="hidden" name="acao" value="conferirItemInventario">
              <input type="hidden" name="LOCACAO" value="<?=$_SESSION['INVENTARIO']['LOCACAO']?>">
              <input type="hidden" name="IDINVENTARIO" value="<?=$_SESSION['INVENTARIO']['IDINVENTARIO']?>">
              <input type="text" name="CODPROD" id="CODPROD" value="" class="form-control" autofocus placeholder="Código do Produto" autocomplete="off">
            </form>
          </div>
        </div>
        <div class="row mt-3">
          <div class="col-12">
            <form class="form-horizontal" role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
              <input type="hidden" name="op" value="20">
              <input type="hidden" name="LOCACAO" value="<?=$_SESSION['INVENTARIO']['LOCACAO']?>">
              <input type="hidden" name="IDINVENTARIO" value="<?=$_SESSION['INVENTARIO']['IDINVENTARIO']?>">
              <div class="btn-group">
                <?php if ($_SESSION['INVENTARIO']['STATUS'] == 'PENDENTE'): ?>
                  <button type="submit" name="acao" value="finalizarInventario" class="btn btn-success">Finalizar Inventário</button>
                <?php endif ?>

                <button type="submit" name="acao" value="gerarEtiquetas" class="btn btn-primary">Gerar Etiquetas Geral</button>
                <button type="submit" name="acao" value="gerarEtiquetasPendentes" class="btn btn-warning">Gerar Etiquetas Pendentes</button>

              </div>
            </form>
          </div>
        </div>
      </div>

      <?php
      
      require_once("pages/inventario/itensInventario.php");
      ?>

    </div>
  </div>

</div>

      
      



