<div class="container-fluid mt-5">
  <div class="row">
    <?php 
    require_once('pages/compras/function.php'); 
    require_once('pages/compras/controller.php'); 
    require_once('pages/compras/sidebar.php'); 
    require_once('pages/compras/precificacao-modalEnviarArquivo.php'); 
    require_once('pages/compras/precificacao-aplicarArquivo.php'); 
    if ($_SESSION['login']['MATRICULA'] == "") {
      echo '<div class="alert alert-danger" role="alert">O seu cadastro de usuário está incompleto.</h3>É obrigatório que o campo Matrícula Winthor do seu usuário esteja preenchido corretamente<br><a class="btn btn-primary" href="index.php?op=12&edit&id='.$_SESSION['login']['IDUSUARIO'].'">Clique aqui para atualizar o seu cadastro</div>';
    }
    ?>
    <main class="col ms-sm-auto px-3">
      <div class="row">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2"><i class="fa-solid fa-dollar"></i> Precificação - Resultado</h1>
          <div class="float-end">
            <div class="btn-group">
              <button class="btn btn-outline-secondary px-3"  title="Enviar Planilha - Produtos" data-bs-toggle="modal" data-bs-target="#precificacao-modalEnviarArquivo">
                <i class="fa-solid fa-upload"></i> 
              </button>
              <a href="index.php?op=114&nav=compras&aba=precificacao&acao=clear" class="btn btn-outline-secondary"  title="Limpar Lista"> 
                <i class="fa-solid fa-broom"></i>
              </a>
            </div>
          </div>
        </div>
      </div>

      <div class="row mt-3">
        <div class="col"></div>

        <div class="col-6">
          <?php if ($_SESSION['RESULTADO']): ?>
          <ol class="list-group list-group-numbered">
            <li class="list-group-item d-flex justify-content-between align-items-start">
              <div class="ms-2 me-auto">
                <div class="fw-bold">ATUALIZADOS</div>
              </div>
              <span class="badge bg-success rounded-pill text-xl"><?= count($_SESSION['RESULTADO']['UPDATE']) ?></span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-start">
              <div class="ms-2 me-auto">
                <div class="fw-bold">COM ERRO</div>
              </div>
              <span class="badge bg-danger rounded-pill text-xl"><?= count($_SESSION['RESULTADO']['ERRO']) ?></span>
            </li>
          </ol>
          <?php endif ?>
        </div>

        <div class="col"></div>
      </div>

    </main>
  </div>
</div>