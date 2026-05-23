<?php  
require_once("pages/logistica/function.php");
require_once("pages/logistica/controller.php");
?>

<div class="container-lg">
  <div class="row mt-4">
    <main class="col-12 m-4">

      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-tasks"></i> Inventários</h1>
        <button href="index.php?op=12" class="btn btn-outline-primary"> <i class="fas fa-tasks"></i> Novo Inventário</button>
      </div>

      <div class="row">
        
        <div class="col-12">
          <form class="row" method="post" enctype="multipart/form-data" action="index.php">
            <input type="hidden" name="op" value="130">
            
            <div class="col-md-3">
              <label for="inputState" class="form-label">Status inventário</label>
              <select name="STATUS" class="form-select select2">
                <option value="TODOS" selected>Todos</option>
                <option value="PENDENTE">SOMENTE PENDENTES</option>
                <option value="FINALIZADO">SOMENTE FINALIZADOS</option>
              </select>
            </div>

            <div class="col-md-3">
              <label for="inputState" class="form-label">Conferente</label>
              <select name="IDUSUARIOCONFERENTE" class="form-select select2">
                <option value="TODOS" selected>Todos</option>
                <?php if ($CONFERENTE = buscaConferentes()): ?>
                  <?php foreach ($CONFERENTE as $key => $value): ?>
                    <option value="<?=$value['IDUSUARIOCONFERENTE']?>"><?=$value['CONFERENTE']?></option>
                  <?php endforeach ?>
                <?php endif ?>
              </select>
            </div>

            <div class="col-2">
              <label class="form-label">Data Início</label>
              <div class="span5" id="sandbox-container">
                <div class="input-group date">
                  <input name="dataini" type="text" class="form-control" value="<?=(isset($_POST['dataini']))?$_POST['dataini']:@date('d/m/Y')?>">
                  <span class="input-group-addon btn btn-primary"><i class="fa fa-th"></i></span>
                </div>         
              </div>
            </div>
            <div class="col-2">
              <label class="form-label">Data Fim</label>
              <div class="span5" id="sandbox-container">
                <div class="input-group date">
                  <input name="datafim" type="text" class="form-control" value="<?=(isset($_POST['datafim']))?$_POST['datafim']:@date('d/m/Y')?>">
                  <span class="input-group-addon btn btn-primary"><i class="fa fa-th"></i></span>
                </div>         
              </div>
            </div>
            <div class="col-2 mt-4">
              <button type="submit" class="btn btn-primary float-md-end" name="acao" value="pesquisaInventario"><i class="fa-solid fa-search"></i> Pesquisar</button>
            </div>

          </form>

        </div>

      </div>

      <hr />

      <?php if ($dados['acao'] == 'pesquisaInventario') : ?>


      <div class="row">
        
        <div class="col-12">
          <div class="table-responsive">
            <table id="tb_ficha" class="table table-bordered table-striped table-hover">

              <thead>
                <tr>
                  <th>#</th>
                  <th>Locação</th>
                  <th>Conferente</th>
                  <th>Início</th>
                  <th>Fim</th>
                  <th>Status</th>
                  <th>ação</th>
                </tr>
              </thead>

              <tbody>
              <?php
              if ($inventarios = buscainventarios($dados)) {
                foreach ($inventarios as $key => $value) {
                  echo '<tr>';
                  echo '<td>'.$value['IDINVENTARIO'].'</td>';
                  echo '<td>'.$value['LOCACAO'].'</td>';
                  echo '<td>'.$value['CONFERENTE'].'</td>';
                  echo '<td>'.formataDataOracletoBr($value['DTINICIO']).'</td>';
                  echo '<td>'.formataDataOracletoBr($value['DTFIM']).'</td>';
                  if ($value['DTFIM'] == "") {
                    echo '<td><spam class="badge rounded-pill bg-warning text-dark">PENDENTE</spam></td>';
                    echo '<td><a href="index.php?op=131&acao=continuar&LOCACAO='.$value['LOCACAO'].'&IDINVENTARIO='.$value['IDINVENTARIO'].'" class="btn btn-outline-primary btn-sm" title="Abrir Inventário" target="_blanck"><i class="fa fa-play"></i> Abrir Inventário</a></td>';
                  } else {
                    echo '<td><spam class="badge rounded-pill bg-success">FINALIZADO</spam></td>';
                    echo '<td><a href="index.php?op=130&acao=extrato&LOCACAO='.$value['LOCACAO'].'&IDINVENTARIO='.$value['IDINVENTARIO'].'" class="btn btn-outline-success btn-sm" title="Extrato Inventário"><i class="fa fa-file"></i> Extrato Inventário</a></td>';
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

    <?php endif; ?>

    </main>
  </div>
</div>