<div class="container-fluid mt-5 text-sm">
  <div class="row">
    <?php 
    require_once('pages/logistica/function.php'); 
    require_once('pages/logistica/controller.php'); 
    require_once('pages/logistica/sidebar.php'); 
    if (isset($dados['IDCHECKOUT']) && !empty($dados['IDCHECKOUT'])) {
      $_SESSION['CHECKOUT']['CAB'] = buscaOMGCHECKOUTC($dados['IDCHECKOUT']);
      $_SESSION['CHECKOUT']['ITEM'] = buscaOMGCHECKOUTI($dados['IDCHECKOUT']);
    }
    if (empty($_SESSION['CHECKOUT']['CAB']['PREVISAO'])) {
      require_once('pages/logistica/modalPrevisao.php'); 
    }
    require_once('pages/logistica/modalConfirmaTransito.php'); 
    ?>
    <main class="col-11 ms-sm-auto px-3">
      <div class="row">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2">
            <i class="fa-solid fa-list-check"></i> Check-out - Conferência de Pedido de venda <?=$_SESSION['CHECKOUT']['CAB']['NUMPED']?>
          </h1>
          <div class="btn-group">
            <a href="index.php?op=137&aba-saida&NUMPED=<?=$_SESSION['CHECKOUT']['CAB']['NUMPED']?>&acao=add5Minutos" class="btn btn-outline-info" title="Adicionar +5 minutos"><i class="fa-solid fa-clock"></i></a>
            <a class="btn btn-xs btn-outline-secondary py-1" target="_black" href="pages/logistica/exportaPDF-pedidoConferencia.php?NUMPED=<?=$_SESSION['CHECKOUT']['CAB']['NUMPED']?>"><i class="fas fa-file"></i></a>
            <a href="?op=137&acao=EXCEL_pedidoConferencia&NUMPED=<?=$_SESSION['CHECKOUT']['CAB']['NUMPED']?>&NUMPEDRCA=<?=$_SESSION['CHECKOUT']['CAB']['NUMPEDRCA']?>" target="_blanck" class="btn btn-outline-secondary" title="Pedido Conferência Excel"><i class="fa-regular fa-file-excel"></i></a>
            <a href="pages/logistica/exportarPDF-etiquetaVenda2.php?NUMPED=<?=$_SESSION['CHECKOUT']['CAB']['NUMPED']?>&NUMPEDRCA=<?=$_SESSION['CHECKOUT']['CAB']['NUMPEDRCA']?>" target="_blanck" class="btn btn-outline-primary" title="Abrir Etiquetas de Venda"><i class="fas fa-tags"></i></a>
            <a href="pages/logistica/exportarPDF-etiquetaVenda3.php?NUMPED=<?=$_SESSION['CHECKOUT']['CAB']['NUMPED']?>&NUMPEDRCA=<?=$_SESSION['CHECKOUT']['CAB']['NUMPEDRCA']?>" target="_blanck" class="btn btn-outline-danger" title="Abrir Etiquetas de Venda Completa"><i class="fas fa-tags"></i></a>
          </div>
        </div>
      </div>

      <?php if (empty($_SESSION['CHECKOUT']['CAB']['DTFIM'])): ?>
      <div class="row">
        <form class="row" action="index.php" method="POST">
          <input type="hidden" name="op" value="137">
          <input type="hidden" name="aba-saida">
          <input type="hidden" name="NUMPED" value="<?=$_SESSION['CHECKOUT']['CAB']['NUMPED']?>">
          <input type="hidden" name="IDCHECKOUT" value="<?=$_SESSION['CHECKOUT']['CAB']['IDCHECKOUT']?>">
            
          <div class="col-6">
            <div class="input-group mb-3">
              <input type="text" id="CODPROD" name="CODPROD" class="form-control" autofocus autocomplete="off" placeholder="COD PRODUTO" required>
              <button class="btn btn-secondary" type="submit" id="button-addon2" name="acao" value="conferirItemCHECKOUT"><i class="fa-solid fa-search"></i></button>
            </div>
          </div>

          <div class="col text-end">
            <div class="btn-group">
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalConfirmaTransito">
                <i class="fa-solid fa-truck-arrow-right"></i> Em Trânsito
              </button>
              <a href="index.php?op=137&aba-saida&acao=finalizarCHECKOUT&IDCHECKOUT=<?=$_SESSION['CHECKOUT']['CAB']['IDCHECKOUT']?>&NUMPED=<?=$_SESSION['CHECKOUT']['CAB']['NUMPED']?>" class="btn btn-success"><i class="fa-solid fa-circle-check"></i> Finalizar</a>
            </div>
          </div>

        </form>
      </div>
      <?php else: ?>
        <div class="col text-end">
          <div class="btn-group">
            <button class="btn btn-success" disabled><i class="fas fa-check"></i> CHECK-OUT FINALIZADO</button>
            <a href="index.php?op=137&aba-saida&acao=resetCHECKOUT&IDCHECKOUT=<?=$_SESSION['CHECKOUT']['CAB']['IDCHECKOUT']?>&NUMPED=<?=$_SESSION['CHECKOUT']['CAB']['NUMPED']?>" class="btn btn-secondary"><i class="fa-solid fa-retweet"></i> Reset</a>
          </div>
        </div>
      <?php endif ?>


      <div class="row">
        <div class="col-12">
          
          <?php if ($_SESSION['CHECKOUT']['ITEM'] ): ?>

          <table id="tblEditavel" class="table table-bordered table-striped table-hover mt-4 text-xl" style="width: 100%">

            <thead>
              <tr>
                <th>#</th>
                <th>CODPROD</th>
                <th>NUMORIGINAL</th>
                <th>DESCRIÇÃO</th>
                <th>MARCA</th>
                <th>LOCAÇÃO</th>
                <th>QT NOTA</th>
                <th>QT CONF</th>
                <th>Opção</th>
              </tr>
            </thead>

            <tbody>
                
            <?php foreach ($_SESSION['CHECKOUT']['ITEM'] as $key => $value) : ?>


                <tr>
                <td><?= ($key+1) ?></td>
                <td><?= 'W'.$value['CODPROD'].'-'.$value['DV'] ?></td>
                <td><?= $value['NUMORIGINAL'] ?></td>
                <td><?= $value['DESCRICAO'] ?></td>
                <td><?= $value['MARCA'] ?></td>
                <td><?= $value['LOCACAO'] ?></td>
                <td class="text-center"><?= $value['QTPEDIDA'] ?></td>
                <?php 
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
                  echo $value['QTCONFERIDA'];
                ?>
                </td>
                <td>
                  <form action="index.php" method="GET">
                    <input type="hidden" name="op" value="137">
                    <input type="hidden" name="aba-saida">
                    <input type="hidden" name="IDCHECKOUT" value="<?=$_SESSION['CHECKOUT']['CAB']['IDCHECKOUT']?>">
                    <input type="hidden" name="IDCHECKOUTI" value="<?=$value['IDCHECKOUTI']?>">
                    <input type="hidden" name="CODPROD" value="<?=$value['CODPROD']?>">
                    <input type="hidden" name="DV" value="<?=$value['DV']?>">
                    <input type="hidden" name="LOCACAO" value="<?=$value['LOCACAO']?>">
                    <input type="hidden" name="QTCONFERIDA" value="<?=$value['QTCONFERIDA']?>">
                    <?php if (empty($_SESSION['CHECKOUT']['CAB']['DTFIM'])): ?>
                      <button type="submit" name="acao" value="editarCHECKOUT" class="btn btn-primary">
                        <i class="fa-solid fa-edit"></i>
                      </button>
                    <?php endif ?>
                    <button type="submit" name="acao" value="alterarLocacao" class="btn btn-info">
                      <i class="fa-solid fa-retweet"></i>
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

  const inputProd = document.getElementById('CODPROD');
  let lastKeyTime = Date.now();
  let inputBuffer = "";

  inputProd.addEventListener('keydown', (e) => {
      const currentTime = Date.now();
      const timeDiff = currentTime - lastKeyTime;
      
      // Se o tempo entre teclas for muito longo, provavelmente é digitação humana
      if (timeDiff > 30) {
          inputBuffer = ""; 
      }

      // Se for 'Enter' (o leitor geralmente envia Enter no final)
      if (e.key === 'Enter') {
          inputProd.value = inputBuffer;
          inputBuffer = "";
          // Opcional: submeter o formulário automaticamente
          // document.querySelector('form').submit();
      } else if (e.key.length === 1) {
          inputBuffer += e.key;
      }

      lastKeyTime = currentTime;
  });

  // Evita colar texto (Ctrl+V)
  inputProd.addEventListener('paste', (e) => e.preventDefault());

  // Bloqueia qualquer tentativa de mudar o valor manualmente
  inputProd.addEventListener('input', (e) => {
      if (e.inputType !== 'insertText' && !e.isTrusted) return; 
      // Se não houver buffer (leitor), limpa o campo
      if (inputBuffer === "") {
          inputProd.value = "";
      }
  });


</script>