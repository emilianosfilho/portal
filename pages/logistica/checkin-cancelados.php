<div class="container-fluid mt-5">
  <div class="row">
    <?php 
    require_once('pages/logistica/function.php'); 
    require_once('pages/logistica/controller.php'); 
    require_once('pages/logistica/sidebar.php'); 
    require_once('pages/logistica/modalIdentificarLocacaoProduto.php'); 
    ?>
    <main class="col-11 ms-sm-auto px-3">
      <div class="row">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2">
            <i class="fa-solid fa-list-check"></i> Check-in - Conferência de Itens cancelados
          </h1>
        </div>
      </div>

      <div class="row">
        <form class="row" action="index.php" method="POST">
          <input type="hidden" name="op" value="130">
          <input type="hidden" name="aba-entrada">

          <div class="col-md-3">
            <div class="input-group">
              <input type="text" id="CODPROD" name="CODPROD" class="form-control" autofocus autocomplete="off" placeholder="COD PRODUTO" required>
              <button class="btn btn-secondary" type="submit" id="button-addon2" name="acao" value="conferirItemCancelados"><i class="fa-solid fa-search"></i></button>
            </div>
          </div>

          <div class="col-md-6 text-end"></div>

          <div class="col-md-3 text-end">
            <a href="index.php?op=130&acao=gerarExtratoCancelados" class="btn btn-secondary"><i class="fa-solid fa-file-pdf"></i> Extrato</a>
          </div>

        </form>
      </div>

      <div class="row">
        <div class="col-12">
          
          <?php if ($_SESSION['CANCELADOS']): ?>

          <table id="tb_full" class="table table-bordered table-striped table-hover mt-4 text-xl mt-3" style="width: 100%">

            <thead>
              <tr>
                <th>#</th>
                <th>CODPROD</th>
                <th>DESCRIÇÃO</th>
                <th>MARCA</th>
                <th>LOCAÇÃO</th>
                <th>QT</th>
                <th>QT CONF</th>
                <th>Opção</th>
              </tr>
            </thead>

            <tbody>
                
            <?php foreach ($_SESSION['CANCELADOS'] as $key => $value) : ?>

                <tr>
                <td><?= $key+1 ?></td>
                <td><?= 'W'.$value['CODPROD'].'-'.$value['DV'] ?></td>
                <td><?= $value['DESCRICAO'] ?></td>
                <td><?= $value['MARCA'] ?></td>
                <td><?= $value['LOCACAO'] ?></td>
                <td class="text-center"><?= $value['QT'] ?></td>
                <?php 
                  if (($value['QTCONFERIDA'] <> $value['QT']) && ($value['OBSERVACAO']=="")) {
                    echo '<td class="bg-danger text-white text-center">';
                  } else {
                    if ($value['QTCONFERIDA'] < $value['QT']) {
                      echo '<td class="bg-info text-center">';
                    } else {
                      if ($value['QTCONFERIDA'] > $value['QT']) {
                        echo '<td class="bg-danger text-white text-center">';
                      } else {
                        echo '<td class="bg-success text-white text-center">';
                      }
                    }
                  }

                  echo $value['QTCONFERIDA'];
                ?>
                </td>
                <td>
                  <form action="index.php" method="GET">
                    <input type="hidden" name="op" value="<?=$dados['op']?>">
                    <input type="hidden" name="aba-entrada">
                    <input type="hidden" name="CODPROD" value="<?=$value['CODPROD']?>">
                    <input type="hidden" name="NUMPED" value="<?=$value['NUMPED']?>">
                    <input type="hidden" name="DESCRICAO" value="<?=$value['DESCRICAO']?>">
                    <input type="hidden" name="MARCA" value="<?=$value['MARCA']?>">
                    <input type="hidden" name="LOCACAO" value="<?=$value['LOCACAO']?>">
                    <input type="hidden" name="QTCONFERIDA" value="<?=$value['QTCONFERIDA']?>">
                    <input type="hidden" name="OBSERVACAO" value="<?=$value['OBSERVACAO']?>">
                    <input type="hidden" name="DATAINI" value="<?=$value['DATAINI']?>">
                    <input type="hidden" name="DATAFIM" value="<?=$value['DATAFIM']?>">
                    <div class="btn-group">
                      <button type="submit" name="acao" value="alterarLocacao" class="btn btn-info">
                        <i class="fa-solid fa-map-location-dot"></i>
                      </button>
                      <button type="submit" name="acao" value="editarCancelados" class="btn btn-outline-danger">
                        <i class="fa-solid fa-edit"></i>
                      </button>
                    </div>
                  </form>
                </td>
                </tr>
                
            <?php endforeach; ?>


            </tbody>

          </table>

            
          <?php endif ?>

        </div>
      </div>

    </main>
  </div><!-- row -->
</div><!-- container-fluid -->


<script type="text/javascript">
  document.getElementById("QTCONFERIDA").addEventListener("change", () => {
    document.getElementById("CODPROD").focus();
  });
</script>