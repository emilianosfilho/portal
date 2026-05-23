<div class="container-fluid mt-5">
  <div class="row">
    <?php 
    require_once('pages/compras/sidebar.php'); 
    ?>
    <main class="col-11 ms-sm-auto px-4">
      <?php  
      require_once('pages/compras/function.php'); 
      require_once('pages/compras/controller.php'); 
      if ($dados['acao'] == "salvarPedCompra") {
        require_once('pages/compras/pedcompra-salvarPedidos.php'); 
      } else {
        require_once('pages/compras/pedcompra-validaArquivo.php'); 
      }
      ?>
      <div class="row">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2"><i class="fa-solid fa-basket-shopping"></i> Pedidos de compra</h1>
          <div class="float-end">
            <div class="btn-group">
              <button type="button" class="btn btn-secondary">Opções</button>
              <button type="button" class="btn btn-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="visually-hidden">Toggle Dropdown</span>
              </button>
              <ul class="dropdown-menu">
                <li><a href="index.php?op=117" class="dropdown-item"  title="Consulta de Produto"> <i class="fa-solid fa-search"></i> Consulta de Pedidos de compra</a></li>
                <li><em><a href="index.php?op=<?=$dados['op']?>&acao=produto-PDF-consulta" class="dropdown-item"  title="Gerar Etiquetas" <?= ((!isset($_SESSION['PRODUTOS']) || empty($_SESSION['PRODUTOS']))?'onclick="return false;"':'')?> > <i class="fa-solid fa-print"></i> Imprimir </a></em></li>
                <li><em><a href="index.php?op=<?=$dados['op']?>&acao=clear" class="dropdown-item"  title="Limpar Lista"> <i class="fa-solid fa-retweet"></i> Limpar Lista </a></em></li>
                 <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="index.php?op=118"><i class="fa-solid fa-file-excel"></i> Enviar Planilha Pedido de compra</a></li>
                <li><a class="dropdown-item" href="download/MODELO_PEDIDO_COMPRA.xlsx" target="_blanck"><i class="fa-solid fa-file-excel"></i> Baixar Planilha Modelo</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
          <h4><i class="fa-solid fa-file-excel"></i> Planilha Pedido de Compra - PREVIEW</h4>
        </div>
      </div>

      <?php $ERROS = 0; ?>
      <?php if ($_SESSION['PEDIDOS']): ?>
        <?php foreach ($_SESSION['PEDIDOS'] as $pedido): ?>
          <div class="row">
            <div class="table-responsive">
              <table class="table table-bordered table-striped table-hover" style="width: 100%">
                <thead>
                  <tr>
                    <th>FORNECEDOR</th>
                    <th>CODFILIAL</th>
                    <th>NUMPEDIDO</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($cab = reset($pedido)): ?>
                    <?php 
                      if (isset($cab['V_NUMPEDIDO']) || isset($cab['V_CODFILIAL']) || isset($cab['V_CODFORNEC'])){
                        $ERROS++;
                      }
                    ?>
                    <td><?=(isset($cab['V_CODFORNEC'])?'<span class="badge bg-danger" title=": '.$value['V_CODFORNEC'].'"><i class="fa-solid fa-ban"></i></span> ':'<span class="badge bg-success"><i class="fa-solid fa-check"></i></span> ') . $cab['CODFORNEC'] . "- " . $cab['FORNECEDOR']?></td>
                    <td><?=(isset($cab['V_CODFILIAL'])?'<span class="badge bg-danger" title=": '.$value['V_CODFILIAL'].'"><i class="fa-solid fa-ban"></i></span> ':'<span class="badge bg-success"><i class="fa-solid fa-check"></i></span> ') . $cab['CODFILIAL']?></td>
                    <td><?=(isset($cab['V_NUMPEDIDO'])?'<span class="badge bg-danger" title=": '.$value['V_NUMPEDIDO'].'"><i class="fa-solid fa-ban"></i></span> ':'<span class="badge bg-success"><i class="fa-solid fa-check"></i></span> ') . $cab['NUMPEDIDO']?></td>
                  <?php endif ?>
                </tbody>
              </table>
            </div>
          </div>

          <div class="row">
            <div class="table-responsive">
              <table class="table table-bordered table-striped table-hover" style="width: 100%">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>CODPROD</th>
                    <th>DV</th>
                    <th>NUMORIGINAL</th>
                    <th>DESCRICAO</th>
                    <th>MARCA</th>
                    <th>CODFAB</th>
                    <th>QTPEDIDA</th>
                    <th>PCOMPRA</th>
                    <th>TOTAL</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($pedido): ?>
                    <?php $contador = 0; $VLTOTAL = 0; ?>
                    <?php foreach ($pedido as $item): ?>
                      <?php 
                        if (isset($item['V_CODPROD']) || isset($item['V_DV']) || isset($item['V_QTPEDIDO']) || isset($item['V_PCOMPRA']) ){
                          $ERROS++;  
                        }
                        $VLTOTAL = $VLTOTAL + (moedaPHP($item['QTPEDIDO']) * moedaPHP($item['PCOMPRA']));
                      ?>

                      <tr>
                        <td><?=(++$contador)?></td>
                        <td><?=(isset($item['V_CODPROD'])?'<span class="badge bg-danger" title=": '.$item['V_CODPROD'].'"><i class="fa-solid fa-ban"></i></span> ':'<span class="badge bg-success"><i class="fa-solid fa-check"></i></span> ') . $item['CODPROD']?></td>
                        <td><?=(isset($item['V_DV'])?'<span class="badge bg-danger" title=": '.$item['V_DV'].'"><i class="fa-solid fa-ban"></i></span> ':'<span class="badge bg-success"><i class="fa-solid fa-check"></i></span> ') . $item['DV']?></td>
                        <td><?=(isset($item['V_DV'])?'<span class="badge bg-danger" title=": '.$item['V_DV'].'"><i class="fa-solid fa-ban"></i></span> ':'<span class="badge bg-success"><i class="fa-solid fa-check"></i></span> ') . $item['NUMORIGINAL']?></td>
                        <td><?=(isset($item['V_DV'])?'<span class="badge bg-danger" title=": '.$item['V_DV'].'"><i class="fa-solid fa-ban"></i></span> ':'<span class="badge bg-success"><i class="fa-solid fa-check"></i></span> ') . $item['DESCRICAO']?></td>
                        <td><?=(isset($item['V_DV'])?'<span class="badge bg-danger" title=": '.$item['V_DV'].'"><i class="fa-solid fa-ban"></i></span> ':'<span class="badge bg-success"><i class="fa-solid fa-check"></i></span> ') . $item['CODMARCA'] . "- " . $item['MARCA']?></td>
                        <td><?=(isset($item['V_DV'])?'<span class="badge bg-danger" title=": '.$item['V_DV'].'"><i class="fa-solid fa-ban"></i></span> ':'<span class="badge bg-success"><i class="fa-solid fa-check"></i></span> ') . $item['CODFAB']?></td>
                        <td><?=(isset($item['V_QTPEDIDO'])?'<span class="badge bg-danger" title=": '.$item['V_QTPEDIDO'].'"><i class="fa-solid fa-ban"></i></span> ':'<span class="badge bg-success"><i class="fa-solid fa-check"></i></span> ') . $item['QTPEDIDO']?></td>
                        <td><?=(isset($item['V_PCOMPRA'])?'<span class="badge bg-danger" title=": '.$item['V_PCOMPRA'].'"><i class="fa-solid fa-ban"></i></span> ':'<span class="badge bg-success"><i class="fa-solid fa-check"></i></span> ') . 'R$ '.$item['PCOMPRA']?></td>
                        <td>R$ <?=moeda($item['QTPEDIDO'] * $item['PCOMPRA'])?></td>
                        
                    <?php endforeach ?>
                      <tr>
                        <td colspan="9"><h5><b>VALOR TOTAL</b></h5></td>
                        <td><h5><b>R$ <?=moeda($VLTOTAL)?></b></h5></td>
                      </tr>
                  <?php endif ?>
                </tbody>
              </table>
            </div>
          </div>

          <hr>
        <?php endforeach ?>
      <?php endif ?>

      <?php if ($ERROS > 0): ?>
        <div class="row">
          <div class="alert alert-danger" role="alert">
            <h2>Alguns erros foram identificados na planilha enviada!</h2>
            <h5>Favor corrigí-los para poder continuar.</h5>
          </div>
        </div>
      <?php else: ?>
        <div class="row">
          <div class="alert alert-success" role="alert">
            <form method="POST" action="index.php">
              <input type="hidden" name="op" value="120">
              <input type="hidden" name="acao" value="salvarPedCompra">
              <button type="submit" class="btn btn-success float-end">Avançar <i class="fa-solid fa-forward"></i></a>
            </form>
          </div>
        </div>
      <?php endif ?>

    </main>
  </div><!-- row -->
</div><!-- container-fluid -->