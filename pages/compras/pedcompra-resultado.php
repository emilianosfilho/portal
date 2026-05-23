<div class="container-fluid mt-5">
  <div class="row">
    <?php 
    require_once('pages/compras/sidebar.php'); 
    ?>
    <main class="col-11 ms-sm-auto px-4">
      <?php  
      require_once('pages/compras/function.php'); 
      require_once('pages/compras/controller.php'); 
      ?>
      <div class="row">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2"><i class="fa-solid fa-dollar"></i> Precificação</h1>
          <div class="float-end">
            <div class="btn-group">
              <button type="button" class="btn btn-secondary">Opções</button>
              <button type="button" class="btn btn-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="visually-hidden">Toggle Dropdown</span>
              </button>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="index.php?op=114"><i class="fa-solid fa-file-excel"></i> Enviar Planilha Precificação</a></li>
                <li><a class="dropdown-item" target="_blanck" href="download/MODELO_PRECIFICACAO.xlsx"><i class="fa-solid fa-file-excel"></i> Baixar Planilha Modelo</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
          <h4><i class="fa-solid fa-file-excel"></i> Planilha Precificação - RESULTADO DO PROCESSAMENTO</h4>
        </div>
      </div>

      <?php if ($_SESSION['PRECIFICACAO']): ?>

      <div class="row">
        <div class="table-responsive">
          <table class="table table-bordered table-striped table-hover" style="width: 100%">
            <thead>
              <tr>
                <th>#</th>
                <th>CODPROD</th>
                <th>NUMORIGINAL</th>
                <th>DESCRIÇÃO</th>
                <th>MARCA</th>
                <th>PVENDA</th>
                <th>RETORNO</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if ($_SESSION['PRECIFICACAO']) {
                $CONTA_ERROS = 0;
                foreach ($_SESSION['PRECIFICACAO'] as $key => $value) {
                  // varDump2($value);  die();
                  echo PHP_EOL;
                  echo '<tr>';
                  echo '<td>'.($key+1).'</td>';
                  echo '<td>'.$value['CODPROD'].'-'.$value['DV'].'</td>';
                  echo '<td>'.$value['NUMORIGINAL'].'</td>';
                  echo '<td>'.$value['DESCRICAO'].'</td>';
                  echo '<td>'.$value['CODMARCA'].'-'.$value['MARCA'].'</td>';
                  echo '<td>'.moeda($value['PVENDA']).'</td>';
                  echo '<td>';
                  if (!$value['VALIDACAO']) {
                    $CONTA_ERROS++;
                    echo '<span class="badge bg-danger"><i class="fa-solid fa-ban"></i></span> '.trim($value['VALIDACAO']);
                  } else {
                    echo '<span class="badge bg-success"><i class="fa-solid fa-check"></i></span> OK';
                  }
                  echo '</td>';
                  echo '</tr>';
                }
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>

      <?php endif ?>

    </main>
  </div><!-- row -->
</div><!-- container-fluid -->