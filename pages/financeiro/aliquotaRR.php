<div class="container-fluid mt-5 text-sm">
  <div class="row">
    <?php 
        include('pages/financeiro/function.php');
        include('pages/financeiro/controller.php');
        include('pages/financeiro/sidebar.php');
        include('pages/financeiro/modalAliquotaRR.php');
    ?>
    <main class="col-11 ms-sm-auto px-3">
      <div class="row">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2"><i class="fa-solid fa-percent"></i> Aliquota Especial RR</h1>
          <button class="btn btn-secondary" type="button" data-bs-toggle="modal" data-bs-target="#modalAliquotaRR"><i class="fa-solid fa-plus"></i> Adicionar Cliente</button>
        </div>
      </div>

      <div class="row">
        
        <div class="col-12">
          <table id="tb_order_desc" class="table table-bordered table-striped table-hover">

            <thead>
              <tr>
                <th>CODCLI</th>
                <th>CLIENTE</th>
                <th>MUNICÍPIO</th>
                <th>DT CADASTRO</th>
                <th>USUARIO CADASTRO</th>
                <th>DT EXCLUSAO</th>
                <th>USUARIO EXCLUSAO</th>
                <th>Ação</th>
              </tr>
            </thead>

            <tbody>
              <?php

              foreach (buscaClienteAliquotaRR() as $key => $value) {
                echo '<tr>';
                echo '<td>'.$value['CODCLI'].'</td>';
                echo '<td>'.$value['CLIENTE'].'</td>';
                echo '<td>'.$value['MUNICIPIO'].'</td>';
                echo '<td>'.formataDataOracleToBR($value['DTCADASTRO']).'</td>';
                echo '<td>'.$value['USUARIOCADASTRO'].'</td>';
                echo '<td>'.formataDataOracleToBR($value['DTEXCLUSAO']).'</td>';
                echo '<td>'.$value['USUARIOEXCLUSAO'].'</td>';
                echo '<td><a href="#" class="btn btn-xs p-0 btn-outline-danger"><i class="fa-solid fa-trash"></i> Excluir</a></td>';
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