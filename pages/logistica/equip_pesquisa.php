<div class="container-fluid mt-5 text-sm">
  <div class="row">
    <?php 
    require_once('pages/logistica/function.php'); 
    require_once('pages/logistica/controller.php'); 
    require_once('pages/logistica/sidebar.php'); 
    ?>
    <main class="col ms-sm-auto px-3">
      <div class="row">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2">
            <i class="fa-solid fa-tractor"></i> Equipamentos
          </h1>
          <div class="btn-group float-end">
            <a class="btn btn-outline-secondary" href="index.php?op=149&nav=logistica&aba=equipamento&acao=equip_excel" title="Adicionar Montadora">
              <i class="fa-solid fa-file-excel"></i> Exportar
            </a>
            <a class="btn btn-outline-secondary" href="index.php?op=149&nav=logistica&aba=equipamento&acao=equip_modalMontadora" title="Adicionar Montadora">
              <i class="fa-solid fa-circle-plus"></i> Montadora
            </a>
            <a class="btn btn-outline-secondary" href="index.php?op=149&nav=logistica&aba=equipamento&acao=equip_modalTipoequip" title="Adicionar Tipo Equipamento">
              <i class="fa-solid fa-circle-plus"></i> Tipo Equipamento
            </a>
            <a class="btn btn-outline-secondary" href="index.php?op=149&nav=logistica&aba=equipamento&acao=equip_modalEquipamento" title="Adicionar Equipamento">
              <i class="fa-solid fa-circle-plus"></i> Equipamento
            </a>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table id="tb_default" class="table table-bordered table-striped table-hover mt-4" style="width: 100%">
              <thead>
                <tr>
                  <th>Montadora</th>
                  <th>Tipo Equipamento</th>
                  <th>Equipamento</th>
                  <th width="10%">Ações</th>
                </tr>
              </thead>

              <tbody>

                <?php if ($_SESSION['equipamentos'] = equipamentosListar()): ?>
                <?php foreach ($_SESSION['equipamentos'] as $key => $value) : ?>
               
                <tr>
                  <td><?= $value['MONTADORA'] ?></td>
                  <td><?= $value['TIPOEQUIPAMENTO'] ?></td>
                  <td><?= $value['EQUIPAMENTO'] ?></td>
                  <td>
                    <div class="btn-group m-0">
                      <a class="btn btn-outline py-1 px-2" href="index.php?op=149&nav=logistica&aba=equipamento&acao=equip_modalEquipamento&IDEQUIPAMENTO=<?= $value['IDEQUIPAMENTO'] ?>" title="Editar"><i class="fa fa-edit"></i></a>
                    </div>
                  </td>
                </tr>
                
                <?php endforeach; ?>
                <?php endif ?>

              </tbody>
            </table>
          </div>
        </div>
      </div>

    </main>
  </div><!-- row -->
</div><!-- container-fluid -->