<div class="container-fluid mt-5 text-sm">
  <div class="row">
    <?php 
    require_once('pages/logistica/function.php'); 
    require_once('pages/logistica/controller.php'); 
    require_once('pages/logistica/sidebar.php'); 
    ?>
    <main class="col ms-sm-auto px-3">
      <div class="row">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2">
            <i class="fa-solid fa-tags"></i> Consulta de Locação
          </h1>
          <div class="btn-group float-end">
            <?php if (isset($_SESSION['LOCACAO'])): ?>
                <a class="btn btn-outline-secondary" target="_blanck" href="pages/logistica/exportaPDFEtiquetaLocacao.php" title="Etiquetas Locação"><i class="fa-solid fa-tags"></i></a>
            <?php endif ?>
            <a class="btn btn-outline-secondary" href="index.php?op=<?=$dados['op']?>&acao=limparLista&nav=<?=$dados['nav']?>&aba=<?=$dados['aba']?>" title="Limpar lista"><i class="fa-solid fa-broom"></i></a>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <form action="index.php" method="POST">
            <input type="hidden" name="op" value="<?=$dados['op']?>">
            <input type="hidden" name="nav" value="<?=$dados['nav']?>">
            <input type="hidden" name="aba" value="<?=$dados['aba']?>">

            <div class="row g-3">

              <div class="col-3">
                <label class="form-label">Locação</label>
                <div class="input-group mb-3">
                  <input type="text" name="LOCACAO" class="form-control" placeholder="Locação" aria-label="Locação" aria-describedby="button-addon2" required autocomplete="off">
                  <button class="btn btn-secondary" type="submit" id="button-addon2" name="acao" value="locacao_pesquisa"><i class="fa-solid fa-search"></i></button>
                </div>
              </div>

            </div>
          </form>
        </div>

        <?php if (isset($_SESSION['LOCACAO']) && !empty($_SESSION['LOCACAO'])): ?>
          <div class="card-body">
            <div class="table-responsive col-6">
              <table class="table table-hover" id="tb_default">

                <thead>
                  <tr>
                    <th>Locação</th>
                    <th width="10%">Ações</th>
                  </tr>
                </thead>

                <tbody>
                  <?php foreach ($_SESSION['LOCACAO'] as $key => $value) : ?>
                     
                      <tr>
                      <td><?= $value['LOCACAO'] ?></td>
                      <td>
                        <div class="btn-group m-0">
                          <a target="_blanck" class="btn btn-primary" href="index.php?op=131&acao=pesquisaProduto&LOCACAO=<?= $value['LOCACAO'] ?>" title="Lista Produtos da locação"><i class="fa fa-boxes"></i> </a>
                          <a href="index.php?op=138&acao=excluirItemLocacao&key=<?=$key?>" class="btn btn-outline-danger" title="Remover desta lista"><i class="fa fa-trash"></i></a>
                        </div>
                      </td>
                      </tr>
                      
                  <?php endforeach; ?>
                </tbody>

              </table>
            </div>
          </div>
        <?php endif ?>
      </div>
    </main>
  </div><!-- row -->
</div><!-- container-fluid -->