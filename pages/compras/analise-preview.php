<div class="container-fluid mt-5">
  <div class="row">
    <?php 
    require_once('pages/compras/function.php'); 
    require_once('pages/compras/sidebar.php'); 
    ?>
    <main class="col-11 ms-sm-auto px-4">
      <?php  
      ?>
      <div class="row">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2"><i class="fa-solid fa-magnifying-glass-dollar"></i> Análise de Compra</h1>
          <div class="float-end">
            <a class="btn btn-secondary" href="pages/compras/excel_analiseArquivoCompra.php" target="_blanck"><i class="fa-solid fa-file-excel"></i> Exportar Excel</a>
          </div>
        </div>
      </div>
      <?php  
      require_once('pages/compras/controller.php'); 
      ?>
      <div class="row">
        <div class="table-responsive">
          <table class="table table-bordered table-striped table-hover" style="width: 100%">

              <?php
              if ($_SESSION['ARQUIVO']) {
                foreach ($_SESSION['ARQUIVO'] as $key => $value) {
                  if ($ret = consultaSugestaoCompraArquivo($value)){
                    $_SESSION['ARQUIVO'][$key] = reset($ret);
                  } else {
                    $_SESSION['ARQUIVO'][$key]["DESCRICAO"] = '';
                    $_SESSION['ARQUIVO'][$key]["SALDO"] = '';
                    $_SESSION['ARQUIVO'][$key]["2022"] = '';
                    $_SESSION['ARQUIVO'][$key]["2023"] = '';
                    $_SESSION['ARQUIVO'][$key]["2024"] = '';
                    $_SESSION['ARQUIVO'][$key]["2025"] = '';
                    $_SESSION['ARQUIVO'][$key]["TODOS"] = '';
                    $_SESSION['ARQUIVO'][$key]["DT_ULTIMA_ENT"] = '';
                    $_SESSION['ARQUIVO'][$key]["PCOMPRA"] = '';
                    $_SESSION['ARQUIVO'][$key]["MARCA"] = '';
                    $_SESSION['ARQUIVO'][$key]["FORNECEDOR"] = '';
                  }
                }
                // varDump2($_SESSION['ARQUIVO']);
                foreach ($_SESSION['ARQUIVO'] as $keyRow => $valueRow) {
                  if ($keyRow == 0) {
                    echo '<thead>';
                    echo '<tr>';
                    foreach ($valueRow as $keyCol => $valueCol) {
                      echo '<th>'.$keyCol.'</th>';
                    }
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';
                  }

                  echo '<tr>';
                  foreach ($valueRow as $keyCol => $valueCol) {
                    if ($valueCol<>"" && ($keyCol=="VL_ANUAL" || $keyCol=="PCOMPRA")) {
                      echo '<td>R$ '.moeda($valueCol).'</td>';
                    } else {
                      echo '<td>'.$valueCol.'</td>';
                    }
                  }
                  echo '</tr>';
                }
                echo '</tbody>';
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>

    </main>
  </div><!-- row -->
</div><!-- container-fluid -->