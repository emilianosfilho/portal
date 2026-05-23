<div class="container-fluid mt-5 text-sm">
  <div class="row">
    <?php 
    require_once('pages/logistica/function.php'); 
    require_once('pages/logistica/controller.php'); 
    require_once('pages/logistica/sidebar.php'); 
    ?>
    <main class="col ms-sm-auto px-3">
      <div class="row">
        <?php 
          require_once('pages/logistica/function.php'); 
          require_once('pages/logistica/controller.php'); 
          require_once('pages/logistica/sidebar.php'); 
          require_once('pages/logistica/modalConfirmaTransito.php'); 

          // varDump2($dados);
          $checkin = buscaDadosCheckinID($dados['IDCHECKIN']);
          // varDump2($checkin);
        ?>
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2">
            <i class="fa-solid fa-list-check"></i> Check-in - Conferência de Entrada NF Nr <?=$checkin['CAB']['NUMNOTA']?>
          </h1>
            
          <form class="row" action="index.php" method="POST">
            <input type="hidden" name="op" value="135">
            <input type="hidden" name="aba" value="entrada">
            <input type="hidden" name="IDCHECKIN" value="<?=$checkin['CAB']['IDCHECKIN']?>">
            <input type="hidden" name="NUMNOTA" value="<?=$checkin["CAB"]["NUMNOTA"]?>">
            <input type="hidden" name="NUMTRANSENT" value="<?=$checkin["CAB"]["NUMTRANSENT"]?>">
            <div class="btn-group">
              <button type="submit" class="btn btn-outline-secondary" name="acao" value="checkin_modalConfirmaExcluir" title="Excluir Conferência"><i class="fa-solid fa-trash"></i></button>
              <button type="submit" class="btn btn-outline-secondary" name="acao" value="checkin_espelhoNF" title="Espelho NF"><i class="fa-solid fa-file-pdf"></i></button>
              <button type="submit" class="btn btn-outline-secondary" name="acao" value="checkin_modalConfirmaTransito" title="Em Trânsito"><i class="fa-solid fa-truck-arrow-right"></i></button>
              <button type="submit" class="btn btn-outline-secondary" name="acao" value="checkin_modalConfirmaFinalizar" title="Finalizar Conferência"><i class="fa-solid fa-circle-check"></i></button>
            </div>
          </form>

        </div>
      </div>

      <div class="row">
        <form class="row" action="index.php" method="POST">
          <input type="hidden" name="op" value="135">
          <input type="hidden" name="aba" value="entrada">
          <input type="hidden" name="IDCHECKIN" value="<?=$checkin['CAB']['IDCHECKIN']?>">
            
          <div class=" col-md-3 col-sm-12">
            <div class="input-group mb-3">
              <input type="text" id="CODPROD" name="CODPROD" class="form-control" autofocus autocomplete="off" placeholder="COD PRODUTO" required>
              <button class="btn btn-secondary" type="submit" id="button-addon2" name="acao" value="checkin_modalconferirItemLocacao"><i class="fa-solid fa-search"></i></button>
            </div>
          </div>

        </form>
      </div>      

      <div class="row">
        <div class="col-12">
          
          <table id="tblEditavel" class="table table-bordered table-striped table-hover mt-4 text-xl" style="width: 100%">

            <thead>
              <tr>
                <th>#</th>
                <th>NUMORIGINAL</th>
                <th>CODFAB</th>
                <th>CODPROD</th>
                <th>DESCRIÇÃO</th>
                <th>MARCA</th>
                <th>LOCAÇÃO</th>
                <th>SALDO</th>
                <th>ENTRADA</th>
                <th>QT CONF</th>
                <th>Opção</th>
              </tr>
            </thead>

            <tbody>
            <?php foreach ($checkin['ITENS'] as $key => $value) : ?>
                <tr>
                <td><?= ($key+1) ?></td>
                <td><?= $value['NUMORIGINAL'] ?></td>
                <td><?= $value['CODFAB'] ?></td>
                <td><?= $value['CODPROD'].'-'.$value['DV'] ?></td>
                <td><?= $value['DESCRICAO'] ?></td>
                <td><?= $value['MARCA'] ?></td>
                <td><?= $value['LOCACAO'] ?></td>
                <td class="text-center"><?= ($value['QTSALDO']>0)?$value['QTSALDO']:'' ?></td>
                <td class="text-center"><?= $value['QTPEDIDA'] ?></td>
                <?php 
                  if (!empty($value['OBSERVACAO'])) {
                    echo '<td class="bg-info text-center">';
                  } else {
                    if ($value['QTCONFERIDA'] < $value['QTPEDIDA']) {
                      if ($value['QTCONFERIDA'] < $value['QTPEDIDA']) {
                        echo '<td class="bg-secondary text-center text-white">';
                      } else {
                        echo '<td class="bg-info text-center">';
                      }
                    } else {
                      if ($value['QTCONFERIDA'] > $value['QTPEDIDA']) {
                        echo '<td class="bg-danger text-center">';
                      } else {
                        echo '<td class="bg-success text-white text-center">';
                      }
                    }
                  }
                  echo ($value['QTCONFERIDA']>0)?$value['QTCONFERIDA']:'';
                ?>
                </td>
                <td>
                  <form action="index.php" method="POST">
                    <input type="hidden" name="op" value="135">
                    <input type="hidden" name="aba" value="entrada">
                    <input type="hidden" name="IDCHECKIN" value="<?=$checkin['CAB']['IDCHECKIN']?>">
                    <input type="hidden" name="IDCHECKINI" value="<?=$value['IDCHECKINI']?>">
                    <input type="hidden" name="NUMNOTA" value="<?=$checkin["CAB"]["NUMNOTA"]?>">
                    <input type="hidden" name="NUMTRANSENT" value="<?=$checkin["CAB"]["NUMTRANSENT"]?>">
                    <input type="hidden" name="CODPROD" value="<?=$value['CODPROD']?>">
                    <input type="hidden" name="DV" value="<?=$value['DV']?>">
                    <input type="hidden" name="DESCRICAO" value="<?=$value['DESCRICAO']?>">
                    <input type="hidden" name="MARCA" value="<?=$value['MARCA']?>">
                    <input type="hidden" name="LOCACAO" value="<?=$value['LOCACAO']?>">
                    <input type="hidden" name="QTCONFERIDA" value="<?=$value['QTCONFERIDA']?>">
                    <input type="hidden" name="OBSERVACAO" value="<?=$value['OBSERVACAO']?>">
                    <div class="btn-group">
                      <?php if (empty($checkin['CAB']['DTFIM'])): ?>
                        <button type="submit" name="acao" value="editarCheckin" class="btn btn-outline-primary">
                          <i class="fa-solid fa-edit"></i>
                        </button>
                      <?php endif ?>
                      <button type="submit" name="acao" value="checkin_modalAlterarLocacao" class="btn btn-outline-info">
                        <i class="fa-solid fa-map-location-dot"></i>
                      </button>
                      <?php if ($value['LOCACAO'] != "9999"): ?>
                      <button type="submit" name="acao" value="etiqueta_pdf_produto" class="btn btn-outline-secondary" title="Etiqueta de Produto">
                        <i class="fa-solid fa-tag"></i>
                      </button>
                      <?php endif ?>
                      <button type="submit" name="acao" value="checkin_extratoProduto" class="btn btn-outline-secondary" title="Extrato Produto">
                        <i class="fa-solid fa-file-pdf"></i>
                      </button>
                    </div>
                  </form>
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

<script type="text/javascript">
  document.getElementById("QTCONFERIDA").addEventListener("change", () => {
    document.getElementById("CODPROD").focus();
  });
</script>