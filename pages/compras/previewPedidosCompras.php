<main>
  <div class="container">
    <?php  
    require_once("pages/compras/function.php");
    require_once("pages/compras/controller.php");
    // varDump2($_SESSION['pedidos']);

    ?>

    <h2>
      <i class="fa-solid fa-file-arrow-up"></i> Preview - Pedido de compras
    </h2>

    <div class="col-md-12">
      <div class="card">

        <div class="card-body">

          <?php foreach ($_SESSION['pedidos'] as $keyPed => $pedido): ?>
            
          <h3 class="mt-0">Pedido Nr <?=$keyPed?>  <div class="float-end">Fornecedor:  <?=buscaNomeFornecedor($_SESSION['pedidos'][$keyPed][0]['CODFORNEC'])?></div></h3>
          <table class="table table-bordered table-striped table-hover" style="width: 100%">

            <tbody>
              <tr>
                <th>ORD</th>
                <th>CODPROD</th>
                <th>PRODUTO</th>
                <th>MARCA</th>
                <th>QTPEDIDO</th>
                <th>PCOMPRA</th>
              </tr>

              <?php
              // varDump2($_SESSION['ARQUIVO']);
              foreach ($pedido as $keyItem => $value) {
                // varDump2($value);  die();
                $produto = buscaProduto($value['CODPROD']);
                $_SESSION['pedidos'][$keyPed][$keyItem]['PRODUTO'] = $produto;

                echo PHP_EOL;
                echo '<tr>';
                echo '<td>'.($keyItem+1).'</td>';
                echo '<td>'.$value['CODPROD'].'-'.$value['DV'].'</td>';
                echo '<td>'.$produto['DESCRICAO'].'</td>';
                echo '<td>'.$produto['MARCA'].'</td>';
                echo '<td>'.$value['QTPEDIDO'].'</td>';
                echo '<td>'.$value['PCOMPRA'].'</td>';
              }
              ?>
            </tbody>

          </table>

          <?php endforeach ?>
        </div>

        <div class="card-footer">
          <a class="btn btn-primary" href="index.php?op=122">Enviar Pedidos para o Winthor</a>
        </div>

      </div>
    </div>
    
  </div>
</main>