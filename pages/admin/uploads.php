<div class="container-fluid mt-5 text-sm">
  <div class="row">
    <?php 
    require_once('pages/admin/function.php'); 
    require_once('pages/admin/controller.php'); 
    require_once('pages/admin/usuario_modalNovo.php'); 
    require_once('pages/admin/sidebar.php'); 
    ?>
    <main class="col ms-sm-auto px-3">

      <div class="row">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 border-bottom">
          <h1 class="h2">
            <i class="fa-solid fa-file-arrow-down"></i> Arquivos Enviados
          </h1>
        </div>
      </div>

      <div class="row mt-3">
      <?php
      // Caminho da pasta de uploads
      $diretorio = __DIR__ . '/../../upload';

      // Validação do diretório
      if (!is_dir($diretorio)) {
          die('Diretório de upload não encontrado.');
      }

      // Lê os arquivos da pasta
      $arquivos = array_diff(scandir($diretorio), ['.', '..']);
      ?>

      <div class="table-responsive">
        <table id="tb_default" class="table table-bordered table-striped table-hover">

          <thead>
            <tr>
              <th>#</th>
              <th>NOME</th>
              <th>DATA</th>
              <th>ação</th>
            </tr>
          </thead>

          <tbody>
          <?php
          if (empty($arquivos)) {
              echo '<tr><td colspan="3" class="text-center">Nenhum arquivo encontrado.</td></tr>';
          } else {
              $contador = 0;
              foreach ($arquivos as $arquivo) {

                  $caminhoArquivo = $diretorio . '/' . $arquivo;

                  // Garante que é um arquivo válido
                  if (is_file($caminhoArquivo)) {

                      $dataEnvio = date('d/m/Y H:i:s', filemtime($caminhoArquivo));
                      $nomeSeguro = htmlspecialchars($arquivo);
          ?>
                      <tr>
                          <td><?= ++$contador ?></td>
                          <td><?= $nomeSeguro ?></td>
                          <td><?= $dataEnvio ?></td>
                          <td class="text-center">
                              <a 
                                  href="upload/<?= urlencode($arquivo) ?>" 
                                  class="btn btn-sm btn-primary" 
                                  download
                              >
                                  Download
                              </a>
                          </td>
                      </tr>
          <?php
                  }
              }
          }
          ?>
          </tbody>

        </table>
      </div>
      </div>

    </main>
  </div><!-- row -->
</div><!-- container-fluid -->