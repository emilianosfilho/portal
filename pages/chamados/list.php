<div class="container">
  <div class="col-12 mt-5">
    <?php 
    require_once("pages/chamados/function.php");
    require_once("pages/chamados/controller.php");

    // Buscamos todos os chamados e organizamos em um array multidimensional por status
    $todosChamados = array_merge(buscaChamadosPendentes() ?: [], buscaChamadosFinalizados() ?: []);
    $chamadosPorStatus = [];
    foreach ($todosChamados as $c) {
        $chamadosPorStatus[$c['STATUS']][] = $c;
    }
    // Ordenação alfabética dos status para as abas
    ksort($chamadosPorStatus);
    ?>
    
    <main>
      <div class="row">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2"><i class="fa-solid fa-user-headset"></i> Chamados</h1>
          <div class="btn-group">
            <?php if (array_search('ADM_CHAMADOS', $_SESSION['login']['PERMISSOES']) !== false): ?>
              <a href="index.php?op=35&acao=modalRelatorioAtividades" class="btn btn-outline-info"><i class="fas fa-file"></i> Relatório</a>
            <?php endif ?>
            <?php if ($_SESSION['login']['IDUSUARIO'] == "1"): ?>
              <a href="index.php?op=35&acao=modalAddTipo" class="btn btn-outline-secondary"><i class="fas fa-plus"></i> Novo Tipo</a>
              <a href="index.php?op=35&acao=modalAddCategoria" class="btn btn-outline-secondary"><i class="fas fa-plus"></i> Nova Categoria</a>
            <?php endif ?>
            <a href="index.php?op=35&acao=modalAddChamado" class="btn btn-secondary"><i class="fas fa-plus"></i> Abrir chamado</a>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col">
          <div class="card">
            <div class="card-header">
              <ul class="nav nav-pills" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link active" id="tab-todos" data-bs-toggle="pill" data-bs-target="#pane-todos" type="button" role="tab">TODOS</button>
                </li>
                <?php foreach (array_keys($chamadosPorStatus) as $status): 
                  $statusID = str_replace(' ', '_', $status); ?>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-<?= $statusID ?>" data-bs-toggle="pill" data-bs-target="#pane-<?= $statusID ?>" type="button" role="tab">
                      <?= $status ?> <span class="badge bg-light text-dark ms-1"><?= count($chamadosPorStatus[$status]) ?></span>
                    </button>
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>
            
            <div class="card-body">
              <div class="tab-content" id="pills-tabContent">
                
                <div class="tab-pane fade show active" id="pane-todos" role="tabpanel">
                  <?php renderTableChamados($todosChamados); ?>
                </div>

                <?php foreach ($chamadosPorStatus as $status => $lista): 
                  $statusID = str_replace(' ', '_', $status); ?>
                  <div class="tab-pane fade" id="pane-<?= $statusID ?>" role="tabpanel">
                    <?php renderTableChamados($lista); ?>
                  </div>
                <?php endforeach; ?>

              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>

<?php
/**
 * Função auxiliar para renderizar a tabela e evitar repetição de HTML
 */
function renderTableChamados($dados) {
    if (empty($dados)) {
        echo '<p class="text-center py-3">Nenhum chamado encontrado.</p>';
        return;
    }
    ?>
    <div class="table-responsive p-0 text-md">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Últ. Alt. / Abertura</th>
            <th>Usuário</th>
            <th>Tipo/Categoria</th>
            <th>Título</th>
            <th>Status</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($dados as $value): ?>
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
                    'AGUARDANDO' => 'bg-secondary',
                    'EM ATENDIMENTO' => 'bg-primary',
                    'FINALIZADO' => 'bg-success',
                    default => 'bg-dark'
                  };
                ?>
                <span class="badge rounded-pill <?= $badgeClass ?>"><?= $value['STATUS'] ?></span>
                <?php if (!empty($value['TECNICO'])): ?>
                    <br><small class="text-sm"><strong><?= obterPrimeiroEUltimoNome($value['TECNICO']) ?></strong></small>
                <?php endif; ?>
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
    <?php
}
?>