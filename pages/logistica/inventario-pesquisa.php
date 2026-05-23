<div class="container-fluid mt-5 text-sm">
  <div class="row">
    <?php 
    require_once('pages/logistica/function.php'); 
    require_once('pages/logistica/controller.php'); 
    require_once('pages/logistica/sidebar.php'); 
    require_once('pages/logistica/modalNovoInventario.php'); 
    ?>
    <main class="col ms-sm-auto px-3">
      <div class="row">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2">
            <i class="fa-solid fa-list-check"></i> Consulta de Inventário
          </h1>
          <div class="btn-group float-end">
            <button type="button" class="btn btn-outline-secondary" title="Abrir novo inventário" data-bs-toggle="modal" data-bs-target="#modalNovoInventario">
              <i class="fa-solid fa-circle-plus"></i>
            </button>
            <a class="btn btn-outline-secondary" href="index.php?op=<?=$dados['op']?>&acao=limparLista&aba=<?=$dados['aba']?>" title="Limpar lista"><i class="fa-solid fa-broom"></i></a>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <form action="index.php" method="POST">
            <input type="hidden" name="op" value="<?=$dados['op']?>">
            <input type="hidden" name="aba" value="<?=$dados['aba']?>">

            <div class="row g-3">
              <div class="col-2">
                <label class="form-label">DATA INÍCIO</label>
                <div class="span5" id="sandbox-container">
                <div class="input-group date">
                  <input name="DATAINI" type="text" class="form-control" value="<?=(isset($dados['DATAINI']))?$dados['DATAINI']:@date('d/m/Y')?>">
                  <span class="input-group-addon btn btn-secondary"><i class="fa fa-th"></i></span>
                </div>         
                </div> 
              </div>

              <div class="col-2">
                <label class="form-label">DATA FIM</label>
                <div class="span5" id="sandbox-container">
                <div class="input-group date">
                  <input name="DATAFIM" type="text" class="form-control" value="<?=(isset($dados['DATAFIM']))?$dados['DATAFIM']:@date('d/m/Y')?>">
                  <span class="input-group-addon btn btn-secondary"><i class="fa fa-th"></i></span>
                </div>         
                </div> 
              </div>

              <div class="col-2">
                <label class="form-label">CHECK-OUT</label>
                <select name="STATUSINVENTARIO" class="form-select select2">
                  <option value="ALL" <?=(($dados['STATUSINVENTARIO']=='ALL')?'selected':'')?> >Todos</option>
                  <option value="NAOFINALIZADO" <?=(($dados['STATUSINVENTARIO']=='NAOFINALIZADO')?'selected':((!isset($dados['STATUSINVENTARIO']))?'selected':''))?> >Não Finalizado</option>
                  <option value="FINALIZADO" <?=(($dados['STATUSINVENTARIO']=='FINALIZADO')?'selected':'')?> >Finalizado</option>
                </select>
              </div>
              <div class="col-2">
                <label class="form-label">Locação</label>
                <input type="text" name="LOCACAO" class="form-control" autofocus autocomplete="off" placeholder="LOCACAO">
              </div>
              <div class="col-2">
                <label class="form-label">ID Inventário</label>
                <input type="text" name="IDINVENTARIO" class="form-control" autofocus autocomplete="off" placeholder="IDINVENTARIO">
              </div>

              <div class="col-1">
                <div class="d-grid gap-2">
                <button class="btn btn-secondary mt-4" type="submit" name="acao" value="inventario_pesquisar"><i class="fa-solid fa-search"></i></button>
                </div>
              </div>

            </div>
          </form>
        </div>

        <?php if (isset($_SESSION['INVENTARIO']) && !empty($_SESSION['INVENTARIO'])): ?>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover" id="tb_default">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Data</th>
                  <th>Locação</th>
                  <th>Conferente</th>
                  <th>Status</th>
                  <th width="10%">Ações</th>
                </tr>
              </thead>

              <tbody>
                  
              <?php foreach ($_SESSION['INVENTARIO'] as $key => $value) : ?>
                  <?php //varDump2($value); ?>

                  <tr>
                  <td><?= $value['IDINVENTARIO'] ?></td>
                  <td><?= formataDataOracletoBr($value['DATA']) ?></td>
                  <td><?= $value['LOCACAO'] ?></td>
                  <td><?= reset(explode(' ', $value['CONFERENTE'])) ?></td>
                  <td>
                  <?php 
                    if ($value['STATUSINVENTARIO'] == 'FINALIZADO') {
                      echo '<span class="badge bg-success">FINALIZADO</span>';
                    } else {
                      echo '<span class="badge bg-danger">NÃO FINALIZADO</span>';
                    }
                  ?>
                  </td>
                  <td>
                    <form action="index.php" method="POST">
                      <input type="hidden" name="op" value="134">
                      <input type="hidden" name="aba-inventario">
                      <input type="hidden" name="key" value="<?=$key?>">
                      <input type="hidden" name="IDINVENTARIO" value="<?=$value['IDINVENTARIO']?>">
                      <input type="hidden" name="LOCACAO" value="<?=$value['LOCACAO']?>">
                      <div class="btn-group">
                        <?php if ($value['STATUSINVENTARIO'] != 'FINALIZADO'): ?>
                          <button type="submit" name="acao" value="abrirInventario" class="btn btn-sm btn-primary"><i class="fa-solid fa-list-check"></i></button>
                        <?php endif ?>
                        <?php if ($_SESSION['login']['PERFIL'] == 'ADMINISTRADOR'): ?>
                          <button type="submit" name="acao" value="exportarInventarioAdminPDF" class="btn btn-sm btn-info"><i class="fa-solid fa-file-pdf"></i></button>
                        <?php endif ?>
                        <button type="submit" name="acao" value="exportarInventarioPDF" class="btn btn-sm btn-secondary"><i class="fa-solid fa-file-pdf"></i></button>
                      </div>
                    </form>
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