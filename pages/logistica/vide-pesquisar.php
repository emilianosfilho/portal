<div class="container-fluid mt-5 text-sm">
  <div class="row">
    <?php 
    require_once('pages/logistica/function.php'); 
    require_once('pages/logistica/controller.php'); 
    require_once('pages/logistica/sidebar.php'); 
    require_once('pages/logistica/vide-modalEnviarArquivo.php'); 
    require_once('pages/logistica/vide-modalCadastrar.php'); 
    ?>
    <main class="col ms-sm-auto px-3">
      <div class="row">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2">
            <i class="fa-solid fa-retweet"></i> Consulta de Vide
          </h1>
          <div class="btn-group float-end">
            <button class="btn btn-outline-secondary px-3"  title="Cadastrar Novo - Vide" data-bs-toggle="modal" data-bs-target="#vide-modalCadastrar">
              <i class="fa-solid fa-plus text-success"></i>
            </button>
            <button class="btn btn-outline-secondary px-3"  title="Enviar Arquivo - Vide" data-bs-toggle="modal" data-bs-target="#vide-modalEnviarArquivo">
              <i class="fa-solid fa-upload text-primary"></i>
            </button>
            <a href="?op=40&acao=limparLista&nav=logistica&aba=vide" class="btn btn-outline-secondary"  title="Limpar Lista - Vide"> 
              <i class="fa-solid fa-broom"></i>
            </a>
          </div>
        </div>
      </div>


      <div class="card">
        <div class="card-header">

          <!--begin::Row-->
          <div class="row mb-3">
            <?php  
            $resumo = buscaResumoVides();
            ?>
            <!--begin::Col-->
            <div class="col-lg-4 col-6">
              <div class="card shadow-sm">
                <div class="card-body bg-success text-white position-relative overflow-hidden">
                  <h5 class="card-title" style="font-size: 32px; font-weight: bold;"><?= moeda($resumo['QT_ATIVOS'],0) ?></h5>
                  <h6 class="card-subtitle mb-2">Ativos</h6>
                  <i class="fa-solid fa-check card-bg-icon"></i>
                </div>
              </div>
            </div>
            <!--begin::Col-->
            <div class="col-lg-4 col-6">
              <div class="card shadow-sm">
                <div class="card-body bg-danger text-white position-relative overflow-hidden">
                  <h5 class="card-title" style="font-size: 32px; font-weight: bold;"><?= moeda($resumo['QT_INATIVOS'],0) ?></h5>
                  <h6 class="card-subtitle mb-2">Inativos</h6>
                  <i class="fa-solid fa-ban card-bg-icon"></i>
                </div>
              </div>
            </div>
            <!--begin::Col-->
            <div class="col-lg-4 col-6">
              <div class="card shadow-sm">
                <div class="card-body bg-info text-white position-relative overflow-hidden">
                  <h5 class="card-title" style="font-size: 32px; font-weight: bold;"><?= moeda($resumo['QT_TOTAL'],0) ?></h5>
                  <h6 class="card-subtitle mb-2">Todos</h6>
                  <i class="fa-solid fa-badge card-bg-icon"></i>
                </div>
              </div>
            </div>
            <!--begin::Col-->
          </div>
          <!--end::Row-->
          <form role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="op" value="40">
            <input type="hidden" name="nav" value="logistica">
            <input type="hidden" name="aba" value="vide">

            <div class="row g-3">

              <div class="col-3">
                <label class="form-label">Cód. Peça  / Cód. Vide</label>
                <input type="text" name="CODPECA" class="form-control" autofocus autocomplete="off" placeholder="CODPROD / NUMORIGINAL" required>
              </div>

              <div class="col-3">
                <label class="form-label">OPÇÃO</label>
                <select name="NOMEOPCAO" class="form-select select2">
                  <option value="ALL" selected>Todos</option>
                  <option value="VIDE">VIDE</option>
                  <option value="NPR">NPR</option>
                  <option value="INFO">INFO</option>
                  <option value="OPCAO">OPCAO</option>
                </select>
              </div>

              <div class="col-1">
                <div class="d-grid gap-2">
                <button class="btn btn-secondary mt-4" type="submit" name="acao" value="vide_pesquisar"><i class="fa-solid fa-search"></i></button>
                </div>
              </div> 

            </div>
          </form>
        </div>

        <?php if (isset($_SESSION['VIDE']) && !empty($_SESSION['VIDE'])): ?>

        <div class="table-responsive mt-3">
            <table id="tb_default" class="table table-bordered table-striped table-hover mt-4" style="width: 100%">
                <thead>
                    <tr>
                        <th>CODPECA</th>
                        <th>VIDE</th>
                        <th>APLICMARCA</th>
                        <th>DESCRIÇÃO</th>
                        <th class="text-center">OPÇÃO</th>
                        <th class="text-center">IMPORTADO</th>
                        <th>PREÇO</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['VIDE'] as $key => $value) : ?>
                        <?php if (!empty($value['IDVIDE'])): 
                            // Preparação da URL para evitar repetição
                            $urlEditar = "index.php?op=40&acao=vide-modalAtualizar&IDVIDE=" . $value['IDVIDE'];
                        ?>
                        <tr onclick="location.href='<?= $urlEditar ?>';" style="cursor: pointer;" title="Clique para editar">
                            <td><?= htmlspecialchars($value['CODPECA']) ?></td>
                            <td><?= htmlspecialchars($value['VIDE']) ?></td>
                            <td><?= htmlspecialchars($value['APLICMARCA']) ?></td>
                            <td><?= htmlspecialchars($value['DESCRICAO']) ?></td>
                            <td class="text-center">
                                <?php
                                // Mapeamento de badges otimizado
                                $badges = [
                                    'VIDE'  => 'bg-success',
                                    'INFO'  => 'bg-info text-dark',
                                    'OPCAO' => 'bg-primary',
                                    'NPR'   => 'bg-secondary'
                                ];
                                $classeBadge = $badges[$value['NOMEOPCAO']] ?? 'bg-dark';
                                echo '<span class="badge ' . $classeBadge . '">' . $value['NOMEOPCAO'] . '</span>';
                                ?>
                            </td>
                            <td class="text-center">
                                <span class="badge <?= ($value['IMPORTADO'] == 'S') ? 'bg-light text-dark border' : 'bg-light text-muted border' ?>">
                                    <?= ($value['IMPORTADO'] == "S") ? "SIM" : "NÃO" ?>
                                </span>
                            </td>
                            <td class="fw-bold">R$ <?= number_format((float)$value['PRECO'], 2, ',', '.') ?></td>
                        </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php endif; ?>

      </div>
    </main>
  </div><!-- row -->
</div><!-- container-fluid -->