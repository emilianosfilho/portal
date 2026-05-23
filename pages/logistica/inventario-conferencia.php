<div class="container-fluid mt-5 text-sm">
  <div class="row">
    <?php 
    require_once('pages/logistica/function.php'); 
    require_once('pages/logistica/controller.php'); 
    require_once('pages/logistica/sidebar.php'); 
    $_SESSION['INVENTARIO'] = buscaDadosInventario($dados);
    // varDump2($_SESSION['INVENTARIO']);
    ?>
    <main class="col-11 ms-sm-auto px-3">
      <div class="row">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2">
            <i class="fa-solid fa-list-check"></i> Inventário - Conferência de Locação <?=$_SESSION['INVENTARIO']['LOCACAO']?>
          </h1>
        </div>
      </div>

      <div class="row">
        <form class="row" action="index.php" method="POST">
          <input type="hidden" name="op" value="139">
          <input type="hidden" name="aba-inventario">
          <input type="hidden" name="IDINVENTARIO" value="<?=$_SESSION['INVENTARIO']['IDINVENTARIO']?>">
          <input type="hidden" name="LOCACAO" value="<?=$_SESSION['INVENTARIO']['LOCACAO']?>">
            
          <div class="col-md-2">
            <select id="QTCONFERIDA" name="QTCONFERIDA" class="form-control" onChange="setFocusCODPROD();">
              <option value="1" selected>1</option>
              <?php  
              for ($i=2; $i <= 50; $i++) { 
                echo '<option value="'.$i.'">'.$i.'</option>';
              }
              ?>
            </select>
          </div>

          <div class="col-md-4">
            <div class="input-group mb-3">
              <input type="text" id="CODPROD" name="CODPROD" class="form-control" autofocus autocomplete="off" placeholder="COD PRODUTO" required>
              <button class="btn btn-secondary" type="submit" id="button-addon2" name="acao" value="conferirItemINVENTARIO"><i class="fa-solid fa-search"></i></button>
            </div>
          </div>

          <div class="col-md-6 text-end">
            <div class="btn-group">
              <?php if ($_SESSION['login']['PERFIL'] == 'ADMINISTRADOR'): ?>
                <a href="index.php?op=139&aba-inventario&acao=exportarInventarioAdminPDF&IDINVENTARIO=<?=$_SESSION['INVENTARIO']['IDINVENTARIO']?>" class="btn btn-info"><i class="fa-solid fa-file-pdf"></i> PDF admin</a>  
                <a href="index.php?op=139&aba-inventario&acao=excluirInventario&IDINVENTARIO=<?=$_SESSION['INVENTARIO']['IDINVENTARIO']?>" class="btn btn-danger"><i class="fa-solid fa-trash"></i> Excluir</a>  
              <?php else: ?>
                <a href="index.php?op=139&aba-inventario&acao=exportarInventarioPDF&IDINVENTARIO=<?=$_SESSION['INVENTARIO']['IDINVENTARIO']?>" class="btn btn-secondary"><i class="fa-solid fa-file-pdf"></i> PDF</a>  
              <?php endif ?>

              <?php
                $qtPendente = 0; 
                foreach ($_SESSION['INVENTARIO']['ITEM'] as $key => $value) {
                  if (empty($value['IDCONFERENTE'])) {
                    $qtPendente++; 
                  }
                }
                if ($qtPendente == 0) {
                  echo '<a href="index.php?op=139&aba-inventario&acao=finalizarInventario&IDINVENTARIO='.$_SESSION['INVENTARIO']['IDINVENTARIO'].'" class="btn btn-success"><i class="fa-solid fa-circle-check"></i> Finalizar</a>';
                }
              ?>            
            </div>
          </div>

        </form>
      </div>

      <div class="row">
        <div class="col-12">

          <?php if ($_SESSION['INVENTARIO']['ITEM']): ?>

          <table id="tblEditavel" class="table table-bordered table-striped table-hover mt-4 text-xl" style="width: 100%">

            <thead>
              <tr>
                <th>#</th>
                <th>CODPROD</th>
                <th>NUMORIGINAL</th>
                <th>DESCRIÇÃO</th>
                <th>MARCA</th>
                <th>QT CONF</th>
                <th>Opção</th>
              </tr>
            </thead>

            <tbody>
                
            <?php foreach ($_SESSION['INVENTARIO']['ITEM'] as $key => $value) : ?>


                <tr>
                <td><?= ($key+1) ?></td>
                <td><?= 'W'.$value['CODPROD'].'-'.$value['DV'] ?></td>
                <td><?= $value['NUMORIGINAL'] ?></td>
                <td><?= $value['DESCRICAO'] ?></td>
                <td><?= $value['MARCA'] ?></td>
                <?php if ($value['QTCONFERIDA'] == ""): ?>
                  <td class="bg-danger"></td>
                <?php else: ?>
                  <?php if ($value['QTCONFERIDA'] == 0): ?>
                    <td class="text-danger"><center><b><?= $value['QTCONFERIDA'] ?></b></center></td>
                  <?php else: ?>
                    <td><center><b><?= $value['QTCONFERIDA'] ?></b></center></td>
                  <?php endif ?>
                <?php endif ?>
                <td>
                  <form action="index.php" method="POST">
                    <input type="hidden" name="op" value="139">
                    <input type="hidden" name="aba-inventario">
                    <input type="hidden" name="IDINVENTARIO" value="<?=$_SESSION['INVENTARIO']['IDINVENTARIO']?>">
                    <input type="hidden" name="IDINVENTARIOI" value="<?=$value['IDINVENTARIOI']?>">
                    <input type="hidden" name="CODPROD" value="<?=$value['CODPROD']?>">
                    <input type="hidden" name="DV" value="<?=$value['DV']?>">
                    <input type="hidden" name="DESCRICAO" value="<?=$value['DESCRICAO']?>">
                    <input type="hidden" name="MARCA" value="<?=$value['MARCA']?>">
                    <input type="hidden" name="LOCACAO" value="<?=$value['LOCACAO']?>">
                    <input type="hidden" name="QTCONFERIDA" value="<?=$value['QTCONFERIDA']?>">
                    <button type="submit" name="acao" value="editarInventario" class="btn btn-primary">
                      <i class="fa-solid fa-edit"></i>
                    </button>
                    <button type="submit" name="acao" value="alterarLocacao" class="btn btn-info">
                      <i class="fa-solid fa-map-location-dot"></i>
                    </button>
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