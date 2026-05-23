<div class="container-fluid mt-5">
  <div class="row">
    <?php 
    require_once('pages/cliente/function.php'); 
    require_once('pages/cliente/controller.php'); 
    require_once('pages/cliente/sidebar.php'); 
    ?>

    <main class="col-11 ms-sm-auto px-3 mt-3">

      <div class="card w-100 shadow">
        <div class="card-header">
          <h2><i class="fa-solid fa-list "></i> Meus Orçamentos recentes</h2>
        </div>

        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped table-hover" id="tb_order_desc">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Data</th>
                  <th scope="col">Cliente</th>
                  <th scope="col">Máquina</th>
                  <th scope="col">Valor</th>
                  <th scope="col">Ação</th>
                </tr>
              </thead>
              <tbody>
                <?php  
                  $hrefNovoOrcamento = "index.php?op=150&acao=novo";
                  $lista = buscaOrcamentosRecentesCliente($_SESSION['login']['CODCLI']);
                ?>
                <?php if ($lista): ?>
                  <?php foreach ($lista as $key => $value): ?>
                    <tr>
                      <th scope="row"><?=$value['IDORCAMENTO']?></th>
                      <td><?=formataDataOracletoBr($value['DATA'])?></td>
                      <td><?=$value['CLIENTE']?></td>
                      <td><?=$value['MAQUINA']?></td>
                      <td><?=moeda($value['VALORTOTAL'], 2)?></td>
                      <td><a href="index.php?op=<?=($_SESSION['login']['PERFIL'] == 'CLIENTE')?'151':'62'?>&acao=consultaOrcamento&IDORCAMENTO=<?=$value['IDORCAMENTO']?>" class=" p-0 btn btn-xs btn-outline-success "><i class=" fa-solid fa-forward"></i> Abrir</a></td>
                    </tr>
                  <?php endforeach ?>
                <?php endif ?>
              </tbody>
            </table>
          </div>
        </div>

      </div>

    </main>
  </div><!-- row -->
</div><!-- container-fluid -->


