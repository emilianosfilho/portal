<div class="container-fluid mt-5 text-sm">
  <div class="row">
    <?php 
    require_once('pages/logistica/function.php'); 
    require_once('pages/logistica/controller.php'); 
    require_once('pages/logistica/sidebar.php'); 
    require_once('pages/logistica/vide-aplicarArquivo.php'); 
    ?>
    <main class="col-11 ms-sm-auto px-3">
      <div class="row">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2">
            <i class="fa-solid fa-retweet"></i> Consulta de Vide
          </h1>
          <div class="btn-group float-end">
            <button class="btn btn-outline-secondary px-3"  title="Cadastrar Novo - Vide" data-bs-toggle="modal" data-bs-target="#vide-modalCadastrar">
              <i class="fa-solid fa-star"></i>
            </button>
            <button class="btn btn-outline-secondary px-3"  title="Enviar Arquivo - Vide" data-bs-toggle="modal" data-bs-target="#vide-modalEnviarArquivo">
              <i class="fa-solid fa-upload"></i>
            </button>
            <a href="?op=40&acao=limparLista&nav=logistica&aba=vide" class="btn btn-outline-secondary"  title="Limpar Lista - Vide"> 
              <i class="fa-solid fa-broom"></i>
            </a>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-3">
          <?php if ($resultado): ?>
          <ol class="list-group list-group-numbered">
            <li class="list-group-item d-flex justify-content-between align-items-start">
              <div class="ms-2 me-auto">
                <div class="fw-bold">ATUALIZADOS</div>
              </div>
              <span class="badge bg-primary rounded-pill text-xl"><?= count($resultado['UPDATE']) ?></span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-start">
              <div class="ms-2 me-auto">
                <div class="fw-bold">NOVOS</div>
              </div>
              <span class="badge bg-success rounded-pill text-xl"><?= count($resultado['INSERT']) ?></span>
            </li>
            <?php if (count($resultado['ERRO'])>0): ?>
              <li class="list-group-item d-flex justify-content-between align-items-start">
                <div class="ms-2 me-auto">
                  <div class="fw-bold">COM ERRO</div>
                </div>
                <span class="badge bg-danger rounded-pill text-xl"><?= count($resultado['ERRO']) ?></span>
              </li>
            <?php endif ?>
          </ol>
          <?php endif ?>
        </div>
      </div>

    </main>
  </div><!-- row -->
</div><!-- container-fluid -->