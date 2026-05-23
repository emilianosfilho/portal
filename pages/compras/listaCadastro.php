<main>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
      <?php  
      $debug = false;
      // $debug = true;
      include("pages/compras/function.php");
      include("pages/compras/controller.php");
      ?>
        <div class="card">
          <div class="card-header">
            <h2>
              <i class="fa-solid fa-file-circle-plus"></i> Lista de Produtos
              <a class="btn btn-primary btn-xs float-sm-end" href="index.php?op=115&acao=exportarListaProdutos">Exportar</a>
            </h2>
          </div>

          <div class="card-body">
            <?php  
              if (count($_SESSION['chave_duplicada'])>0) {
                echo '<div class="alert alert-danger col-8" role="alert">
                        <h4>O arquivo possui produtos com chave duplicada</h4>
                        <h4>(CODFAB + CODFORNEC)</h4>
                        <h5>Recomendamos proceder com a entrada manual</h5>
                        <table class="table">
                          <thead>
                            <tr>
                              <th scope="col">NUMORIGINAL</th>
                              <th scope="col">DESCRICAO</th>
                              <th scope="col">MARCA</th>
                              <th scope="col">CODFAB</th>
                              <th scope="col">CODFORNEC</th>
                            </tr>
                          </thead>
                          <tbody>';
                foreach ($_SESSION['chave_duplicada'] as $keyDup => $valueDup) {
                  echo '    <tr>';
                  echo '      <td>'.$valueDup['NUMORIGINAL'].'</td>';
                  echo '      <td>'.$valueDup['DESCRICAO'].'</td>';
                  echo '      <td>'.$valueDup['MARCA'].'</td>';
                  echo '      <td>'.$valueDup['CODFAB'].'</td>';
                  echo '      <td>'.$valueDup['CODFORNEC'].'</td>';
                  echo '    </tr>';
                }
                echo '  </table>';
                echo '</div>';
              }
            ?>
              
            <table class="table table-bordered table-striped table-hover" style="width: 100%">

              <thead>
                <tr>
                  <th>CODPROD</th>
                  <th>NUMORIGINAL</th>
                  <th>DESCRICAO</th>
                  <th>MARCA</th>
                  <th>CODFAB</th>
                  <th>NCM</th>
                  <th>V_PCDEPTO</th>
                  <th>V_PCSECAO</th>
                  <th>V_PCCODFABRICA</th>
                  <th>V_PCPRODFILIAL</th>
                  <th>V_PCEST</th>
                  <th>V_PCTRIBENTRADA</th>
                  <th>V_PCTABTRIB</th>  
                  <th>V_PCTABPR</th>
                </tr>
              </thead>

              <tbody>
                <?php
                // varDump2($_SESSION['ARQUIVO']);
                $LISTA_CODPROD = "";
                foreach ($_SESSION['PRODUTOS'] as $key => $value) {
                  $LISTA_CODPROD .= $value['CODPROD'].", ";
                }
                $LISTA_CODPROD = substr($LISTA_CODPROD, 0, -2);

                if ($validacaoGeral = validacaoGeral($LISTA_CODPROD)) {

                  foreach ($validacaoGeral as $key => $value) {
                    echo PHP_EOL;
                    echo '<tr>';
                    echo '<td>'.$value['CODPROD'].'</td>';
                    echo '<td>'.$value['NUMORIGINAL'].'</td>';
                    echo '<td>'.$value['DESCRICAO'].'</td>';
                    echo '<td>'.$value['MARCA'].'</td>';
                    echo '<td>'.$value['CODFAB'].'</td>';
                    echo '<td>'.$value['NCM'].'</td>';
                    if ($value['V_PCDEPTO'] == 1) {
                      echo '<td><span class="badge bg-success">OK</span></td>';
                    } else {
                      echo '<td><span class="badge bg-danger">ERRO</span></td>';
                    }
                    if ($value['V_PCSECAO'] == 1) {
                      echo '<td><span class="badge bg-success">OK</span></td>';
                    } else {
                      echo '<td><span class="badge bg-danger">ERRO</span></td>';
                    }
                    if ($value['V_PCCODFABRICA'] == 1) {
                      echo '<td><span class="badge bg-success">OK</span></td>';
                    } else {
                      echo '<td><span class="badge bg-danger">ERRO</span></td>';
                    }
                    if ($value['V_PCPRODFILIAL'] == 1) {
                      echo '<td><span class="badge bg-success">OK</span></td>';
                    } else {
                      echo '<td><span class="badge bg-danger">ERRO</span></td>';
                    }
                    if ($value['V_PCEST'] == 1) {
                      echo '<td><span class="badge bg-success">OK</span></td>';
                    } else {
                      echo '<td><span class="badge bg-danger">ERRO</span></td>';
                    }
                    if ($value['V_PCTRIBENTRADA'] == 1) {
                      echo '<td><span class="badge bg-success">OK</span></td>';
                    } else {
                      echo '<td><span class="badge bg-danger">ERRO</span></td>';
                    }
                    if ($value['V_PCTABTRIB'] == 1) {
                      echo '<td><span class="badge bg-success">OK</span></td>';
                    } else {
                      echo '<td><span class="badge bg-danger">ERRO</span></td>';
                    }
                    if ($value['V_PCTABPR'] == 1) {
                      echo '<td><span class="badge bg-success">OK</span></td>';
                    } else {
                      echo '<td><span class="badge bg-danger">ERRO</span></td>';
                    }
                    echo '</tr>';
                  }
                }
                ?>
              </tbody>

            </table>

          </div>

        </div>

      </div>

    </div>
  </div>
</main>