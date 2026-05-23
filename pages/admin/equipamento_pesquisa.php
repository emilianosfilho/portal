<div class="container-fluid mt-5 text-sm">
  <div class="row">
    <?php 
    require_once('pages/admin/function.php'); 
    require_once('pages/admin/controller.php'); 
    require_once('pages/admin/sidebar.php'); 
    ?>
    <main class="col ms-sm-auto px-3">
      <div class="row">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2">
            <i class="fa-solid fa-tractor"></i> Consulta de Equipamentos
          </h1>
          <div class="btn-group float-end">
            <a class="btn btn-outline-secondary" href="index.php?op=<?=$dados['op']?>&acao=limparLista&aba=<?=$dados['aba']?>" title="Limpar lista"><i class="fa-solid fa-broom"></i></a>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <form action="index.php" method="POST">
            <input type="hidden" name="op" value="<?=$dados['op']?>">
            <input type="hidden" name="aba" value="<?=$dados['aba']?>">

            <div class="row g-3">

              <div class="col">
                <label class="form-label">MONTADORA</label>
                <select name="IDMONTADORA" class="form-select select2">
                  <option value="">Selecione a Montadora</option>
                  <?php if ($montadoras = buscaMontadoras()): ?>
                  <?php varDump2 ($montadoras); ?>
                  <?php foreach ($montadoras as $key => $value): ?>
                    <option value="<?= $value['IDMONTADORA'];?>" <?=(($dados['IDMONTADORA']==$value['IDMONTADORA'])?'selected':'')?> >
                      <?= $value['MONTADORA'];?>
                    </option>
                  <?php endforeach ?>
                  <?php endif ?>
                </select>
              </div>

              <div class="col">
                <label class="form-label">TIPO</label>
                <select name="IDGRUPO" class="form-select select2">
                  <option value="">Selecione a Montadora</option>
                </select>
              </div>

              <div class="col">
                <label class="form-label">EQUIPAMENTO</label>
                <select name="IDEQUIPAMENTO" class="form-select select2">
                  <option value="">Selecione a Montadora</option>
                </select>
              </div>

              <div class="col">
                <label class="form-label">GRUPO</label>
                <select name="IDGRUPO" class="form-select select2">
                  <option value="">Selecione a Montadora</option>
                </select>
              </div>

              <div class="col">
                <label class="form-label">Núm. Original</label>
                <input type="text" name="NUMNOTA" class="form-control" autofocus autocomplete="off" placeholder="NUMNOTA">
              </div>

              <div class="col-1">
                <div class="d-grid gap-2">
                <button class="btn btn-secondary mt-4" type="submit" name="acao" value="equipamento_pesquisar"><i class="fa-solid fa-search"></i></button>
                </div>
              </div> 

            </div>
          </form>
        </div>

 <?php if ($_SESSION['EQUIPAMENTOS']): ?>

          <table id="tblEditavel" class="table table-bordered table-striped table-hover mt-4" style="width: 100%">

            <thead>
              <tr>
                <th>#</th>
                <th>Codprod</th>
                <th>Numoriginal</th>
                <th>Descrição</th>
                <th>Marca</th>
                <th>Locação</th>
                <th>Vide</th>
                <th>Qt Estoque</th>
                <th>Qt Reserv</th>
                <th width="10%">Etiqueta</th>
                <th width="10%">Ações</th>
              </tr>
            </thead>

            <tbody>

            <?php foreach ($_SESSION['EQUIPAMENTOS'] as $key => $value) : ?>

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
                <td><?= $value['DESCRICAO'] ?></td>
                <td><?= $value['MARCA'] ?></td>
                <td><?= $value['LOCACAO'] ?></td>
                <td><?= $value['VIDE'] ?></td>
                <td><?= $value['QTSALDO'] ?></td>
                <td><?= $value['QTRESERVADA'] ?></td>
                <td><?= $value['QTETIQUETA'] ?></td>
                <td>
                  <div class="btn-group m-0">
                    <a class="btn btn-outline-primary py-1 px-2" href="index.php?op=131&acao=modalQTETIQUETA&key=<?= $key ?>&CODPROD=<?= $value['CODPROD'] ?>&DV=<?= $value['DV'] ?>&QTETIQUETA=<?= $value['QTETIQUETA'] ?>" title="Editar"><i class="fa fa-edit"></i></a>
                    <a class="btn btn-outline-secondary py-1 px-2" target="_blanck" href="pages/logistica/produto-extrato.php?CODPROD=<?= $value['CODPROD'] ?>" title="Extrato"><i class="fa fa-file"></i> </a>
                    <a class="btn btn-outline-secondary py-1 px-2"href="index.php?op=131&acao=produto_modalAlterarLocacao&CODPROD=<?= $value['CODPROD'] ?>&LOCACAO=<?= $value['LOCACAO'] ?>" title="Alterar Locação"><i class="fa fa-retweet"></i> </a>
                    <a class="btn btn-outline-secondary py-1 px-2" href="index.php?op=131&acao=historicoLocacao&CODPROD=<?= $value['CODPROD'] ?>" title="Histórico"><i class="fa fa-clock"></i> </a>
                    <a class="btn btn-outline-danger py-1 px-2" href="index.php?op=131&acao=excluirItem&key=<?=$key?>" title="Remover desta lista"><i class="fa fa-ban"></i></a>
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