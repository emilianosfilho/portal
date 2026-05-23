<main>
  <div class="container">
    <?php  
    require_once("pages/logistica/function.php");
    require_once("pages/logistica/controller.php");
    ?>

    <h2>
      <i class="fa-solid fa-list"></i> Lista Inventários da locação <?=$LOCACAO?>
<!--       <div class="float-sm-end">
        <a href="index.php?op=12" class="btn btn-primary"> Novo Usuário</a>
      </div> -->
    </h2>
    
    <div class="row">

      <div class="col-md-12">

        <div class="card">
          <div class="card-body">
              

            <table style="width:100%" class="table table-bordered table-striped table-hover">

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
                ?>
              </tbody>

            </table>

          </div>

        </div>

      </div>

    </div>
  </div>
</main>