    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Produtos
        <small>Valida</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-home"></i> Home</a></li>
        <li class="active">Usuarios</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">


        <?php
        include "pages/admin/function.php";

        if (isset($_SESSION['msg'])) {
          echo $_SESSION['msg'];
          unset($_SESSION['msg']);
        }

        if (isset($_SESSION['erro'])) {
          varDump2($_SESSION['erro']);
          unset($_SESSION['erro']);
        }

        ?>
          
          <div class="box">
            <!-- /.box-header -->
            <div class="box-body">

              <?php
                require_once('pages/conf/function.php');
                $produtos = buscaProdutosRadar();
                // varDump2($produtos);
              ?>


              <table id="example1" class="table table-bordered table-striped">

                <thead>
                <tr>
                  <th>#</th>
                  <th>NUMORIGINAL</th>
                  <th>CODIGO</th>
                  <th>STATUS</th>
                </tr>
                </thead>

                <tbody>
                <?php
                $existentes = buscaProdutosExistentes($produtos);
                // varDump2($existentes); 
                die();

                foreach ($produtos as $key => $value) {

                  if ($validacao == false) {
                    echo '<tr>';
                    echo '<td>'.$value['id'].'</td>';
                    echo '<td>'.$value['numoriginal'].'</td>';
                    echo '<td>'.$value['codigo'].'</td>';
                    if (in_array($value['numoriginal'], $existentes, true)) {
                      echo '<td><p style="padding:0px 10px; font-size: 90%;" class="label label-success">Cadastrado</p></td>';
                    } else {
                      echo '<td><p style="padding:0px 10px; font-size: 90%;" class="label label-danger">Não Cadastrado</p></td>';
                    }
                    echo '</tr>';
                  }
                }
                ?>
                </tbody>

              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>


<!-- jQuery 2.2.3 -->
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- page script -->
<script>
  $(function () {
    $('#example1').DataTable({
      "paging": true,
      "lengthChange": true,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": true
    });
  });
</script>
