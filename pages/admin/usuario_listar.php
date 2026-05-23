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
            <i class="fa-solid fa-users"></i> Lista Usuários
          </h1>
          <div class="btn-group float-end">
            <a class="btn btn-outline-secondary" href="index.php?op=11&nav=admin&aba=usuarios&acao=liberarAcessoDadosFornec">Liberar Fornecedores</a>
            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#usuario_modalNovo">
            <i class="fas fa-plus"></i> Novo
          </button>
          </div>
        </div>
      </div>


      <!--begin::Row-->
      <div class="row">
        <?php  
        $resumo = buscaResumoUsuarios();
        // varDump2($resumo);
        ?>
        <!--begin::Col-->
        <div class="col-lg-4 col-6">
          <div class="card shadow-sm">
            <div class="card-body bg-success text-white position-relative overflow-hidden">
              <h5 class="card-title" style="font-size: 32px; font-weight: bold;"><?= $resumo['QT_ATIVOS'] ?></h5>
              <h6 class="card-subtitle mb-2">Ativos</h6>
              <i class="fa-solid fa-check card-bg-icon"></i>
            </div>
            <div class="card-footer py-0">
              <a href="index.php?op=11&acao=limparLista&aba=usuarios&filtro=ativos" class="card-link">Exibir <i class="fa-solid fa-circle-arrow-right"></i></a>
            </div>
          </div>
        </div>
        <!--begin::Col-->
        <div class="col-lg-4 col-6">
          <div class="card shadow-sm">
            <div class="card-body bg-danger text-white position-relative overflow-hidden">
              <h5 class="card-title" style="font-size: 32px; font-weight: bold;"><?= $resumo['QT_INATIVOS'] ?></h5>
              <h6 class="card-subtitle mb-2">Inativos</h6>
              <i class="fa-solid fa-ban card-bg-icon"></i>
            </div>
            <div class="card-footer py-0">
              <a href="index.php?op=11&acao=limparLista&aba=usuarios&filtro=inativos" class="card-link">Exibir <i class="fa-solid fa-circle-arrow-right"></i></a>
            </div>
          </div>
        </div>
        <!--begin::Col-->
        <div class="col-lg-4 col-6">
          <div class="card shadow-sm">
            <div class="card-body bg-info text-white position-relative overflow-hidden">
              <h5 class="card-title" style="font-size: 32px; font-weight: bold;"><?= $resumo['QT_TOTAL'] ?></h5>
              <h6 class="card-subtitle mb-2">Todos</h6>
              <i class="fa-solid fa-badge card-bg-icon"></i>
            </div>
            <div class="card-footer py-0">
              <a href="index.php?op=11&acao=limparLista&aba=usuarios&filtro=todos" class="card-link">Exibir <i class="fa-solid fa-circle-arrow-right"></i></a>
            </div>
          </div>
        </div>
        <!--begin::Col-->
      </div>
      <!--end::Row-->

      <div class="row mt-3">
      <?php 
        if (isset($dados['filtro'])) {
          if ($dados['filtro'] == "inativos") {
            $lista = buscaUsuariosInativos(); 
          } else if ($dados['filtro'] == "todos") {
            $lista = buscaTodosUsuarios(); 
          } else {
            $lista = buscaUsuariosAtivos(); 
          }
        } else {
          $lista = buscaUsuariosAtivos(); 
        }
        
      ?>

      <div class="table-responsive">
        <table id="tb_default" class="table table-bordered table-striped table-hover">

          <thead>
            <tr>
              <th>#</th>
              <th>IMAGEM</th>
              <th>NOME</th>
              <th>EMAIL</th>
              <th>LOGIN</th>
              <th>PERFIL</th>
              <th>COD RCA</th>
              <th>MAT. WINTHOR</th>
              <th>Dt Cadastro</th>
              <th>Últ. Login</th>
              <th>STATUS</th>
              <th>ação</th>
            </tr>
          </thead>

          <tbody>
            <?php
            foreach ($lista as $key => $value) {
              // varDump2($value); die();
              echo '<tr>';
              echo '<td>'.$value['IDUSUARIO'].'</td>';
              echo '<td><img class="rounded-circle border border-dark" width="25px" src="'.@DIR_IMG.''.$value['AVATAR'].'"></td>';
              echo '<td>'.$value['NOME'].'</td>';
              echo '<td>'.$value['EMAIL'].'</td>';
              echo '<td>'.$value['LOGIN'].'</td>';
              echo '<td>'.$value['PERFIL'].'</td>';
              echo '<td style="text-align: center">'.$value['CODUSUR'].'</td>';
              echo '<td style="text-align: center">'.$value['MATRICULA'].'</td>';
              echo '<td style="text-align: center">'.formataDataOracleToBR($value['DTCADASTRO']).'</td>';
              echo '<td style="text-align: center">'.diferencaTempo($value['DTULTLOGIN']).'</td>';
              if ($value['STATUS'] == "A") {
                echo  '<td><span class="badge bg-success">ATIVO</span></td>';
              } else {
                echo '<td><span class="badge bg-danger">INATIVO</span></td>';
              }
              echo '<td>',
                   ' <form method="post" enctype="multipart/form-data" action="index.php">',
                   '   <input type="hidden" name="op" value="10">',
                   '   <input type="hidden" name="aba" value="usuarios">',
                   '   <input type="hidden" name="IDUSUARIO" value="'.$value['IDUSUARIO'].'">',
                   '   <button type="submit" class="btn btn-outline-secondary btn-xs" title="Editar usuário"><i class="fa fa-edit"></i> Editar</button>',
                   ' </form>',
                   '</td>';

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