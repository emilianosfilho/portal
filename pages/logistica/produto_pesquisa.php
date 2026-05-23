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
            <i class="fa-solid fa-boxes"></i> Consulta de Produto
          </h1>
          <div class="btn-group float-end">
            <?php if ($_SESSION['login']['PERFIL'] == "ADMINISTRADOR"): ?>
              <a class="btn btn-outline-secondary" target="_blanck" href="pages/logistica/exportaPDFEtiquetaProduto.php?op=<?=$dados['op']?>&acao=imprimirConsultaProdutos&nav=logistica&aba=produtos" title="Gerar Etiquetas"><i class="fa-solid fa-tags"></i></a>
            <?php endif ?>
            <a class="btn btn-outline-secondary" href="index.php?op=<?=$dados['op']?>&acao=imprimirConsultaProdutos&nav=logistica&aba=produtos" title="Gerar Relatório"><i class="fa-solid fa-file-pdf"></i></a>
            <a class="btn btn-outline-secondary" href="index.php?op=<?=$dados['op']?>&acao=EXCEL_ConsultaProdutos&nav=logistica&aba=produtos" title="Exportar Excel"><i class="fa-solid fa-file-excel"></i></a>
            <a class="btn btn-outline-secondary" href="index.php?op=<?=$dados['op']?>&acao=limparLista&nav=logistica&aba=produtos" title="Limpar lista"><i class="fa-solid fa-broom"></i></a>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <form action="index.php" method="POST">
            <input type="hidden" name="op" value="131">
            <input type="hidden" name="nav" value="logistica">
            <input type="hidden" name="aba" value="produtos">

            <div class="row g-3">

              <div class="col">
                <label class="form-label">Cód. Peça</label>
                <input type="text" name="CODPECA" class="form-control" autofocus autocomplete="off" placeholder="CODPROD / NUMORIGINAL">
              </div>

              <div class="col">
                <label class="form-label">Descrição</label>
                <input type="text" name="DESCRICAO" class="form-control" autofocus autocomplete="off" placeholder="DESCRIÇÃO">
              </div>

              <div class="col">
                <label class="form-label">Marca</label>
                <input type="text" name="MARCA" class="form-control" autofocus autocomplete="off" placeholder="MARCA">
              </div>

              <div class="col">
                <label class="form-label">LOCAÇÃO</label>
                <input type="text" name="LOCACAO" class="form-control" autocomplete="off" placeholder="LOCAÇÃO">
              </div>

              <div class="col">
                <label class="form-label">NF ENTRADA</label>
                <input type="text" name="NUMNOTA" class="form-control" autocomplete="off" placeholder="NUMNOTA">
              </div>

              <div class="col">
                <label class="form-label">Cód. Fábrica</label>
                <input type="text" name="CODFAB" class="form-control" autofocus autocomplete="off" placeholder="Código no Fornecedor">
              </div>

              <div class="col-1">
                <div class="d-grid gap-2">
                <button class="btn btn-secondary mt-4" type="submit" name="acao" value="pesquisaProduto"><i class="fa-solid fa-search"></i></button>
                </div>
              </div> 

            </div>
          </form>
        </div>

 <?php if ($_SESSION['produtos']): ?>

          <table id="tblEditavel" class="table table-bordered table-striped table-hover mt-4" style="width: 100%">

            <thead>
              <tr>
                <th>#</th>
                <th>Codprod</th>
                <th>Numoriginal</th>
                <th>Vide</th>
                <th>Descrição</th>
                <th>Marca</th>
                <th>Locação</th>
                <?php if ($_SESSION['login']["PERFIL"]=="ADMINISTRADOR"): ?>
                    <th>P. Venda</th>
                <?php endif ?>
                <th>Qt Estoque</th>
                <th>Qt Reserv</th>
                <th width="10%">Etiqueta</th>
                <th width="10%">Ações</th>
              </tr>
            </thead>

            <tbody>

            <?php foreach ($_SESSION['produtos'] as $key => $value) : ?>

              <?php if ($value['CODPROD']<>""): ?>

                <?php 
                // varDump2($value);
                if ($value['QTETIQUETA'] == ""){
                  $value['QTETIQUETA'] = 1;
                }
                ?>
               
                <tr>
                <td><?= ($key+1) ?></td>
                <td><?= $value['CODPROD'].'-'.$value['DV'] ?></td>
                <td><?= $value['NUMORIGINAL'] ?></td>
                <td><?= $value['VIDE'] ?></td>
                <td><?= $value['DESCRICAO'] ?></td>
                <td><?= $value['MARCA'] ?></td>
                <td><?= $value['LOCACAO'] ?></td>
                <?php if ($_SESSION['login']["PERFIL"]=="ADMINISTRADOR"): ?>
                  <td><div class="float-end"><?= moeda($value['PVENDA']) ?></div></td>
                <?php endif ?>
                <td><div class="float-end"><?= $value['QTSALDO'] ?></div></td>
                <td><div class="float-end"><?= $value['QTRESERVADA'] ?></div></td>
                <td><div class="float-end"><?= $value['QTETIQUETA'] ?></div></td>
                <td>
                  <div class="btn-group m-0">
                    <?php if ($_SESSION['login']['PERFIL'] == "ADMINISTRADOR"): ?>
                      <a class="btn btn-outline-primary py-1 px-2" href="index.php?op=131&nav=logistica&tab=produto&acao=modalQTETIQUETA&key=<?= $key ?>&CODPROD=<?= $value['CODPROD'] ?>&DV=<?= $value['DV'] ?>&QTETIQUETA=<?= $value['QTETIQUETA'] ?>" title="Quantidade de Etiquetas"><i class="fa fa-tags"></i></a>
                      <a class="btn btn-outline-secondary py-1 px-2" href="index.php?op=131&nav=logistica&tab=produto&acao=produto_modalEditarProd&key=<?= $key ?>&CODPROD=<?= $value['CODPROD'] ?>&DV=<?= $value['DV'] ?>" title="Editar"><i class="fa fa-edit"></i></a>
                    <?php endif ?>
                    <a class="btn btn-outline-secondary py-1 px-2" target="_blanck" href="pages/logistica/produto-extrato.php?CODPROD=<?= $value['CODPROD'] ?>" title="Extrato"><i class="fa fa-file"></i> </a>
                    <a class="btn btn-outline-secondary py-1 px-2"href="index.php?op=131&nav=logistica&tab=produto&acao=produto_modalAlterarLocacao&CODPROD=<?= $value['CODPROD'] ?>&LOCACAO=<?= $value['LOCACAO'] ?>" title="Alterar Locação"><i class="fa fa-retweet"></i> </a>
                    <a class="btn btn-outline-secondary py-1 px-2" href="index.php?op=131&nav=logistica&tab=produto&acao=historicoLocacao&CODPROD=<?= $value['CODPROD'] ?>" title="Histórico"><i class="fa fa-clock"></i> </a>
                    <a class="btn btn-outline-danger py-1 px-2" href="index.php?op=131&nav=logistica&tab=produto&acao=excluirItem&key=<?=$key?>" title="Remover desta lista"><i class="fa fa-ban"></i></a>
                  </div>
                </td>
                </tr>
                
              <?php endif ?>
            
            <?php endforeach; ?>

          </tbody>

        </table>

        <?php endif ?>

      </div>
    </main>
  </div><!-- row -->
</div><!-- container-fluid -->