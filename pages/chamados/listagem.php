<div class="container-fluid mt-5 text-sm">
  <div class="row">
    <?php 
    require_once('pages/chamados/function.php'); 
    require_once('pages/chamados/controller.php'); 
    require_once('pages/chamados/modalAddChamado.php'); 

    // Buscamos todos os chamados e organizamos em um array multidimensional por status
    $todosChamados = listaChamados();
    // varDump2($todosChamados);
    ?>
    <main class="col ms-sm-auto px-3">
      <div class="row">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2">
            <i class="fa-solid fa-user-headset"></i> Chamados
          </h1>
          <div class="btn-group float-end">
            <button type="button" class="btn btn-info" title="Abrir novo inventário" data-bs-toggle="modal" data-bs-target="#modalAddChamado">
              <i class="fa-solid fa-circle-plus"></i> Novo Chamado
            </button>
          </div>
        </div>
      </div>

      <!--begin::Row-->
      <div class="row">
        <?php if ((($todosChamados['EM ATENDIMENTO'])?count($todosChamados['EM ATENDIMENTO']):0) > 0): ?>
        <!--begin::Col-->
        <div class="col-lg col-6">
          <div class="card shadow-sm">
            <div class="card-body bg-primary text-white position-relative overflow-hidden">
              <h5 class="card-title" style="font-size: 32px; font-weight: bold;"><?= ($todosChamados['EM ATENDIMENTO'])?count($todosChamados['EM ATENDIMENTO']):0 ?></h5>
              <h6 class="card-subtitle mb-2">EM ATENDIMENTO</h6>
              <i class="fa-solid fa-play card-bg-icon"></i>
            </div>
            <div class="card-footer py-0">
              <a href="index.php?op=35&nav=chamados&filtro=EM ATENDIMENTO" class="card-link">Exibir <i class="fa-solid fa-circle-arrow-right"></i></a>
            </div>
          </div>
        </div>
        <?php endif ?>

        <?php if ((($todosChamados['PAUSADO'])?count($todosChamados['PAUSADO']):0) > 0): ?>
        <!--begin::Col-->
        <div class="col-lg col-6">
          <div class="card shadow-sm">
            <div class="card-body bg-info text-white position-relative overflow-hidden">
              <h5 class="card-title" style="font-size: 32px; font-weight: bold;"><?= ($todosChamados['PAUSADO'])?count($todosChamados['PAUSADO']):0 ?></h5>
              <h6 class="card-subtitle mb-2">Pausados</h6>
              <i class="fa-solid fa-pause card-bg-icon"></i>
            </div>
            <div class="card-footer py-0">
              <a href="index.php?op=35&nav=chamados&filtro=PAUSADO" class="card-link">Exibir <i class="fa-solid fa-circle-arrow-right"></i></a>
            </div>
          </div>
        </div>
        <?php endif ?>

        <?php if ((($todosChamados['AGUARDANDO'])?count($todosChamados['AGUARDANDO']):0) > 0): ?>
        <!--begin::Col-->
        <div class="col-lg col-6">
          <div class="card shadow-sm">
            <div class="card-body bg-secondary text-white position-relative overflow-hidden">
              <h5 class="card-title" style="font-size: 32px; font-weight: bold;"><?= ($todosChamados['AGUARDANDO'])?count($todosChamados['AGUARDANDO']):0 ?></h5>
              <h6 class="card-subtitle mb-2">AGUARDANDO</h6>
              <i class="fa-regular fa-hourglass-end card-bg-icon"></i>
            </div>
            <div class="card-footer py-0">
              <a href="index.php?op=35&nav=chamados&filtro=AGUARDANDO" class="card-link">Exibir <i class="fa-solid fa-circle-arrow-right"></i></a>
            </div>
          </div>
        </div>
        <?php endif ?>

        <?php if ((($todosChamados['FINALIZADO'])?count($todosChamados['FINALIZADO']):0) > 0): ?>
        <!--begin::Col-->
        <div class="col-lg col-6">
          <div class="card shadow-sm">
            <div class="card-body bg-success text-white position-relative overflow-hidden">
              <h5 class="card-title" style="font-size: 32px; font-weight: bold;"><?= ($todosChamados['FINALIZADO'])?count($todosChamados['FINALIZADO']):0 ?></h5>
              <h6 class="card-subtitle mb-2">Finalizados</h6>
              <i class="fa-solid fa-check card-bg-icon"></i>
            </div>
            <div class="card-footer py-0">
              <a href="index.php?op=35&nav=chamados&filtro=FINALIZADO" class="card-link">Exibir <i class="fa-solid fa-circle-arrow-right"></i></a>
            </div>
          </div>
        </div>
        <?php endif ?>

      </div>
      <!--end::Row-->

      <div class="row mt-3">
        <div class="table-responsive">
          <table class="table table-hover" id="tb_order_desc">
          <thead>
            <tr>
              <th>Últ. Alt. / Abertura</th>
              <th>Usuário</th>
              <th>Tipo/Categoria</th>
              <th>Título</th>
              <th>Status</th>
              <th>Técnico</th>
              <th>Ações</th>
            </tr>
          </thead>

          <tbody>
            <?php
            $lista = [];
            if (isset($dados['filtro'])){
              if ($dados['filtro'] == 'FINALIZADO') {
                $lista = ($todosChamados['FINALIZADO']) ?? [];
              } else if ($dados['filtro'] == 'PAUSADO') {
                $lista = ($todosChamados['PAUSADO']) ?? [];
              } else if ($dados['filtro'] == 'AGUARDANDO') {
                $lista = ($todosChamados['AGUARDANDO']) ?? [];
              } else {
                $lista = ($todosChamados['EM ATENDIMENTO']) ?? [];
              }
            } else {
              $lista = ($todosChamados['EM ATENDIMENTO']) ?? [];
            }
            ?>

            <?php foreach ($lista as $value): ?>
              <tr>
                <td>
                  <small>
                    <?= isset($value['DATA_ALTERACAO']) ? calcular_tempo_relativo($value['DATA_ALTERACAO']) : $value['DATA_ABERTURA'] ?>
                  </small>
                </td>
                <td><?= obterPrimeiroEUltimoNome($value['USUARIO']) ?></td>
                <td>
                  <span class="badge bg-light text-dark border"><?= $value['TIPO'] ?></span><br>
                  <small class="text-muted"><?= $value['CATEGORIA'] ?></small>
                </td>
                <td><?= $value['TITULO'] ?></td>
                <td>
                  <?php
                    $badgeClass = match($value['STATUS']) {
                      'EM ATENDIMENTO' => 'bg-primary',
                      'PAUSADO' => 'bg-info',
                      'AGUARDANDO' => 'bg-secondary',
                      'FINALIZADO' => 'bg-success',
                      default => 'bg-dark'
                    };
                  ?>
                  <span class="badge rounded-pill <?= $badgeClass ?>"><?= $value['STATUS'] ?></span>
                </td>
                <td>
                  <small class="text-sm"><strong><?= obterPrimeiroEUltimoNome($value['TECNICO']) ?></strong></small>
                </td>
                <td>
                  <a href="index.php?op=36&ID_CHAMADO=<?= $value['ID_CHAMADO'] ?>" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-magnifying-glass"></i>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          </table>


        </div>
      </div>
    </main>
  </div><!-- row -->
</div><!-- container-fluid -->