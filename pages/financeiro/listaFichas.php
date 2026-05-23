<div class="container-fluid mt-5 text-sm">
  <div class="row">
    <?php 
        include('pages/financeiro/function.php');
        include('pages/financeiro/controller.php');
        include('pages/financeiro/sidebar.php');
    ?>
    <main class="col-11 ms-sm-auto px-3">
      <div class="row">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2"><i class="fa-solid fa-copy"></i> Fichas Cadastrais</h1>
          <a href="ficha.php" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Nova Ficha Cadastral</a>
        </div>
      </div>

      <div class="row">
        
        <div class="col-12">
          <table id="tb_order_desc" class="table table-bordered table-striped table-hover">

            <thead>
              <tr>
                <th>#</th>
                <th>DATA</th>
                <th>MUNICÍPIO/UF</th>
                <th>TIPO</th>
                <th>NOME/RAZAO SOCIAL</th>
                <th>CPF/CNPJ</th>
                <th>Ações</th>
              </tr>
            </thead>

            <tbody>
              <?php

              foreach (buscaFichasRecentes() as $key => $value) {
                echo '<tr>';
                echo '<td>'.$value['IDFICHACADASTRAL'].'</td>';
                echo '<td>'.formataDataOracleToBR($value['DATA']).'</td>';
                echo '<td>'.$value['MUNICIPIO'].'/'.$value['UF'].'</td>';
                if ($value['TIPOFJ']=="J") {
                  $name = somenteNumeros($value['CNPJ']).'_FICHACADASTRAL'.'.pdf';
                  echo '<td>P. JURÍDICA</td>';
                  echo '<td>'.$value['RAZAOSOCIAL'].'</td>';
                  echo '<td>'.formatCnpj($value['CNPJ']).'</td>';
                } else {
                  $name = somenteNumeros($value['CPF']).'_FICHACADASTRAL'.'.pdf';
                  echo '<td>P. FÍSICA</td>';
                  echo '<td>'.$value['NOME'].'</td>';
                  echo '<td>'.formatCpf($value['CPF']).'</td>';
                }
                echo '<td><div class="btn-group">';
                $filename = @DIR_UPLOAD.$name; 

                echo '<a target="_blanck" href="pages/financeiro/gera_ficha_cadastral.php?IDFICHACADASTRAL='.$value['IDFICHACADASTRAL'].'" class="btn btn-secondary"><i class="fa-solid fa-file-pdf"></i></a>';
                if (file_exists($filename)) {
                  echo ' <a target="_blanck" href="index.php?op=143&acao=enviarEmail&IDFICHACADASTRAL='.$value['IDFICHACADASTRAL'].'" class="btn btn-success"><i class="fa-solid fa-paper-plane"></i></a>';
                }

                if (!empty($value['COMPENDERECO1'])) {
                  $filename = explode(".", $value['COMPENDERECO1']);
                  $filename = @DIR_UPLOAD . reset($filename).".".strtolower(end($filename));
                  if (file_exists($filename)) {
                    echo ' <a target="_blanck" href="'.$filename.'" class="btn btn-primary"><i class="fa-solid fa-file-arrow-down"></i></a>';
                  }
                }
                if (!empty($value['COMPENDERECO2'])) {
                  $filename = explode(".", $value['COMPENDERECO2']);
                  $filename = @DIR_UPLOAD . reset($filename).".".strtolower(end($filename));
                  if (file_exists($filename)) {
                    echo ' <a target="_blanck" href="'.$filename.'" class="btn btn-primary"><i class="fa-solid fa-file-arrow-down"></i></a>';
                  }
                }
                if (!empty($value['COMPDOCUMENTO1'])) {
                  $filename = explode(".", $value['COMPDOCUMENTO1']);
                  $filename = @DIR_UPLOAD . reset($filename).".".strtolower(end($filename));
                  if (file_exists($filename)) {
                    echo ' <a target="_blanck" href="'.$filename.'" class="btn btn-primary"><i class="fa-solid fa-file-arrow-down"></i></a>';
                  }
                }
                if (!empty($value['COMPDOCUMENTO2'])) {
                  $filename = explode(".", $value['COMPDOCUMENTO2']);
                  $filename = @DIR_UPLOAD . reset($filename).".".strtolower(end($filename));
                  if (file_exists($filename)) {
                    echo ' <a target="_blanck" href="'.$filename.'" class="btn btn-primary"><i class="fa-solid fa-file-arrow-down"></i></a>';
                  }
                }
                echo '</div></td>';
                echo '</tr>';
              }
              ?>
            </tbody>

          </table>
        </div>
      </div>

    </main>
  </div><!-- row -->
</div><!-- container-fluid -->







<div class="container-lg">
  <div class="row mt-4">
    <main class="col-lg-12 m-4">
      <?php  

      ?>
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div class="col-9">
          <h1 class="h2"></h1>
        </div>
        <div class="col-3">
          
        </div>
      </div>



    </main>
  </div>
</div>
