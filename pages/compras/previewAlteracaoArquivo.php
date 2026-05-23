<main>
  <div class="container">

    <div class="row">
      <div class="col-md-12">
        <?php  
        include_once("pages/compras/function.php");
        include_once('pages/compras/validaArquivoAlteracaoMassa.php');
        ?>

        <div class="card">
          <div class="card-header">
            <h2><i class="fa-solid fa-file-circle-plus"></i> Preview de Produtos</h2>
          </div>

          <div class="card-body">
              
            <table class="table table-bordered table-striped table-hover" style="width: 100%">

              <thead>
                <tr>
                  <th>#</th>
                  <th>CODPROD</th>
                  <th>NUMORIGINAL</th>
                  <th>MARCA</th>
                  <th>DESCRICAO</th>
                  <th>LOCACAO</th>
                  <th>DEPTO</th>
                  <th>SESSÃO</th>
                </tr>
              </thead>

              <tbody>
                <?php
                $ERRO = 0;
                foreach ($_SESSION['PRODUTOS'] as $key => $value) {
                  // varDump2($value); die();
                  echo PHP_EOL;
                  echo '<tr>';
                  echo '<td>'.($key+1).'</td>';
                  if ($value['V_CODPROD']) {
                    if ($value['CODPROD']=="") {
                      echo '<td><span class="badge bg-danger"><i class="fa-solid fa-ban"></i></span> '.$value['V_CODPROD'].'</td>';
                      $ERRO++;
                    } else {
                      echo '<td><span class="badge bg-warning text-black"><i class="fa-solid fa-exclamation"></i></span> '.$value['V_CODPROD'].'</td>';
                    }
                  } else {
                    echo '<td><span class="badge bg-success"><i class="fa-solid fa-check"></i></span> '.$value['CODPROD'].'</td>';
                  }

                  if ($value['V_NUMORIGINAL']) {
                    if ($value['NUMORIGINAL'] == "") {
                      echo '<td><span class="badge bg-info"><i class="fa-solid fa-exclamation"></i></span> '.$value['V_NUMORIGINAL'].'</td>';
                    } else {
                      echo '<td><span class="badge bg-danger" title="'.$value['V_NUMORIGINAL'].'"><i class="fa-solid fa-ban"></i></span> '.$value['NUMORIGINAL'].'</td>';
                      $ERRO++;
                    }
                  } else {
                    echo '<td><span class="badge bg-success"><i class="fa-solid fa-check"></i></span> '.$value['NUMORIGINAL'].'</td>';
                  }

                  if ($value['V_CODMARCA']) {
                    if ($value['MARCA'] == "") {
                      echo '<td><span class="badge bg-info"><i class="fa-solid fa-exclamation"></i></span> '.$value['V_CODMARCA'].'</td>';
                    } else {
                      echo '<td><span class="badge bg-danger"><i class="fa-solid fa-ban"></i></span> '.$value['V_CODMARCA'].'</td>';
                      $ERRO++;
                    }
                  } else {
                    echo '<td><span class="badge bg-success"><i class="fa-solid fa-check"></i></span> '.$value['CODMARCA'].'</td>';
                  }

                  if ($value['V_DESCRICAO']) {
                    echo '<td><span class="badge bg-info text-black" title="A descrição não será alterada"><i class="fa-solid fa-exclamation"></i></span> '.$value['V_DESCRICAO'].'</td>';
                  } else {
                    echo '<td><span class="badge bg-success" title="'.$value['V_DESCRICAO'].'"><i class="fa-solid fa-check"></i></span> '.$value['DESCRICAO'].'</td>';
                  }
                  if ($value['V_LOCACAO']) {
                    echo '<td><span class="badge bg-info text-black"><i class="fa-solid fa-exclamation"></i></span> '.$value['V_LOCACAO'].'</td>';
                  } else {
                    echo '<td><span class="badge bg-success"><i class="fa-solid fa-check"></i></span> '.$value['LOCACAO'].'</td>';
                  }

                  if ($value['V_CODEPTO']) {
                    if ($value['CODEPTO']=="") {
                      echo '<td><span class="badge bg-info text-black"><i class="fa-solid fa-exclamation"></i></span> '.$value['V_CODEPTO'].'</td>';
                    } else {
                      if ($value['DEPARTAMENTO']=="") {
                        $ERRO++;
                        echo '<td><span class="badge bg-danger"><i class="fa-solid fa-ban"></i></span> '.$value['V_CODEPTO'].'</td>';
                      } else {
                        echo '<td><span class="badge bg-success"><i class="fa-solid fa-check"></i></span> '.$value['CODEPTO'].'-'.$value['DEPARTAMENTO'].'</td>';
                      }
                    }
                  } else {
                    echo '<td><span class="badge bg-success"><i class="fa-solid fa-check"></i></span> '.$value['CODEPTO'].'-'.$value['DEPARTAMENTO'].'</td>';
                  }
                  if ($value['V_CODSEC']) {
                    if ($value['CODSEC']=="") {
                      echo '<td><span class="badge bg-info text-black"><i class="fa-solid fa-exclamation"></i></span> '.$value['V_CODSEC'].'</td>';
                    } else {
                      if ($value['SECAO']=="") {
                        $ERRO++;
                        echo '<td><span class="badge bg-danger"><i class="fa-solid fa-ban"></i></span> '.$value['V_CODSEC'].'</td>';
                      } else {
                        echo '<td><span class="badge bg-success"><i class="fa-solid fa-check"></i></span> '.$value['CODSEC'].'-'.$value['SECAO'].'</td>';
                      }
                    }
                  } else {
                    echo '<td><span class="badge bg-success"><i class="fa-solid fa-check"></i></span> '.$value['CODSEC'].'-'.$value['SECAO'].'</td>';
                  }
                  echo '</tr>';

                }
                ?>
              </tbody>
            </table>
          </div>

          <div class="card-footer">
            <?php if ($ERRO>0): ?>
              <a class="btn btn-danger" href="index.php?op=116">Enviar arquivo Corrigido</a>
            <?php else: ?>
              <a class="btn btn-primary" href="index.php?op=118&acao=aplicarAlteracaoMassa">Aplicar Atualizações</a>
            <?php endif ?>
          </div>

        </div>
      </div>
    </div>
  </div>
</main>