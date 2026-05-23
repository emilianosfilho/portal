<div class="container-fluid mt-5">
  <div class="row">
    <?php 
    require_once('pages/admin/function.php'); 
    require_once('pages/admin/controller.php'); 
    require_once('pages/admin/sidebar.php'); 
    ?>
    <main class="col-11 ms-sm-auto px-3">
      <div class="row">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2"><i class="fa-solid fa-ban"></i> Sessões bloqueadas no Oracle</h1>
          <!-- <button href="index.php?op=12" class="btn btn-outline-primary"> <i class="fas fa-tasks"></i> Novo Inventário</button> -->
        </div>
      </div>

      <div class="row">

        <div class="table-responsive">
          <table id="tb_default" class="table table-bordered table-striped table-hover" style="width:100%">
            <thead>
              <tr>
                <th>Sessao_Travadora</th>
                <th>Usuario_Travador</th>
                <th>Sessao_Esperando</th>
                <th>Usuario_Esperando</th>
                <th>lock_type</th>
                <th>mode_held</th>
                <th>mode_requested</th>
                <th>lock_id1</th>
                <th>lock_id2</th>
              </tr>
            </thead>
            <tbody>
              <?php  
              if ($sessoesBloqueadas = buscaSessoesBloqueadas()) {
                foreach ($sessoesBloqueadas as $key => $value) {
                  echo '<tr>';
                  echo '  <td>' . $value['Sessao_Travadora'] . '</td>';
                  echo '  <td>' . $value['Usuario_Travador'] . '</td>';
                  echo '  <td>' . $value['Sessao_Esperando'] . '</td>';
                  echo '  <td>' . $value['Usuario_Esperando'] . '</td>';
                  echo '  <td>' . $value['lock_type'] . '</td>';
                  echo '  <td>' . $value['mode_held'] . '</td>';
                  echo '  <td>' . $value['mode_requested'] . '</td>';
                  echo '  <td>' . $value['lock_id1'] . '</td>';
                  echo '  <td>' . $value['lock_id2'] . '</td>';
                  echo '</tr>';
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
