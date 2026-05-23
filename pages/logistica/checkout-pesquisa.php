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
            <i class="fa-solid fa-right-from-bracket"></i> Consulta Saídas
          </h1>
          <div class="btn-group float-end">
            <a class="btn btn-outline-secondary" href="index.php?op=<?=$dados['op']?>&nav=<?=$dados['nav']?>&aba=<?=$dados['aba']?>&acao=limparLista" title="Limpar lista"><i class="fa-solid fa-broom"></i></a>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <form action="index.php" method="POST">
            <input type="hidden" name="op" value="<?=$dados['op']?>">
            <input type="hidden" name="nav" value="<?=$dados['nav']?>">
            <input type="hidden" name="aba" value="<?=$dados['aba']?>">

            <div class="row g-3">
              <div class="col-2">
                <label class="form-label">DATA INÍCIO</label>
                <div class="span5" id="sandbox-container">
                <div class="input-group date">
                  <input name="DATAINI" type="text" class="form-control" value="<?=(isset($dados['DATAINI']))?$dados['DATAINI']:@date('d/m/Y',strtotime("-7 days"))?>">
                  <span class="input-group-addon btn btn-secondary"><i class="fa fa-th"></i></span>
                </div>         
                </div> 
              </div>

              <div class="col-2">
                <label class="form-label">DATA FIM</label>
                <div class="span5" id="sandbox-container">
                <div class="input-group date">
                  <input name="DATAFIM" type="text" class="form-control" value="<?=(isset($dados['DATAFIM']))?$dados['DATAFIM']:@date('d/m/Y')?>">
                  <span class="input-group-addon btn btn-secondary"><i class="fa fa-th"></i></span>
                </div>         
                </div> 
              </div>

              <div class="col">
                <label class="form-label">CHECK-OUT</label>
                <select name="STATUSCHECKOUT" class="form-select select2">
                  <option value="ALL" <?=(($dados['STATUSCHECKOUT']=='ALL')?'selected':'')?> >Todos</option>
                  <option value="NAOFINALIZADO" <?=(($dados['STATUSCHECKOUT']=='NAOFINALIZADO')?'selected':((!isset($dados['STATUSCHECKOUT']))?'selected':''))?> >Não Finalizado</option>
                  <option value="FINALIZADO" <?=(($dados['STATUSCHECKOUT']=='FINALIZADO')?'selected':'')?> >Finalizado</option>
                  <option value="EM TRANSITO" <?=(($dados['STATUSCHECKOUT']=='EM TRANSITO')?'selected':'')?> >Em Trânsito</option>
                </select>
              </div>

              <div class="col">
                <label class="form-label">FATURAMENTO</label>
                <select name="FATURAMENTO" class="form-select select2">
                  <option value="ALL" <?=(($dados['FATURAMENTO']=='ALL')?'selected':'')?> >Todos</option>
                  <option value="NAOFATURADOS" <?=(($dados['FATURAMENTO']=='NAOFATURADOS')?'selected':((!isset($dados['FATURAMENTO']))?'selected':''))?> >Não Faturados</option>
                  <option value="FATURADOS" <?=(($dados['FATURAMENTO']=='FATURADOS')?'selected':'')?> >Faturados</option>
                </select>
              </div>

              <div class="col">
                <label class="form-label">VENDEDOR</label>
                <select name="CODUSUR" class="form-select select2">
                  <option value="" selected>Todos</option>
                  <?php
                  if ($vendedores = buscaVendedores()) {
                      foreach ($vendedores as $key => $value) {
                        echo '<option value="'.$value['CODUSUR'].'"';
                        if ($dados['CODUSUR'] == $value['CODUSUR']) {
                          echo ' selected';
                        }
                        echo '>'.substr($value['NOME'], 0, 25).'</option>';
                      }
                    }  
                  ?>
                </select>
              </div>

              <div class="col-1">
                <label class="form-label">NÚM. PEDIDO</label>
                <input type="text" name="NUMPED" class="form-control" autofocus autocomplete="off" placeholder="NUMPED / IDORCAMENTO">
              </div>

              <div class="col-1">
                <div class="d-grid gap-2">
                <button class="btn btn-secondary mt-4" type="submit" name="acao" value="checkout_pesquisar"><i class="fa-solid fa-search"></i></button>
                </div>
              </div>

            </div>
          </form>
        </div>

        <?php if (!isset($checkout_lista) || empty($checkout_lista)): ?>
          <div class="card-body">
            <?php if (empty($checkout_lista) && ($dados['acao'] == 'checkout_pesquisar')): ?>
              <h5>Nenhum registro encontrado para a pesquisa</h5>
            <?php endif ?>
          </div>
        <?php else: ?>

          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover" id="tb_default">

                <thead>
                  <tr>
                    <th>Numped</th>
                    <th>Orcamento</th>
                    <th>Nr Checkout</th>
                    <th>Data</th>
                    <th>Vendedor</th>
                    <th>Cliente</th>
                    <th>C. Balcão</th>
                    <th>Separar</th>
                    <th>Faturamento</th>
                    <th>Check-out</th>
                    <th>Conferente</th>
                    <th>ação</th>
                  </tr>
                </thead>

                <tbody>
                <?php
                foreach ($checkout_lista as $key => $value) {
                  // varDump2($value); 
                  // die();
                  echo '<tr>';
                  echo '<td>'.$value['NUMPED'].'</td>';
                  echo '<td>'.$value['IDORCAMENTO'].'</td>';
                  echo '<td>'.$value['IDCHECKOUT'].'</td>';
                  echo '<td>'.formataDataOracletoBr($value['DATAPEDIDO']).'</td>';
                  echo '<td>'.$value['VENDEDOR'].'</td>';
                  echo '<td>'.$value['CLIENTE'].'</td>';
                  echo '<td>'.decodeClienteBalcao($value['CLIENTEBALCAO']).'</td>';
                  echo '<td>'.decodeSepararPedido($value['SEPARARPEDIDO']).'</td>';
                  echo '<td>'.decodePosicao($value['POSICAO']).'</td>';
                  echo '<td>'.decodeStatusLogistica($value['STATUSCHECKOUT']).'</td>';
                  echo '<td>'.(empty($value['CONFERENTE'])?'':reset(explode(" ", $value['CONFERENTE']))).'</td>';
                  echo '<td><div class="btn-group">';
                  echo '<a class="btn btn-xs btn-outline-secondary py-1" target="_black" href="pages/logistica/exportaPDF-pedidoConferencia.php?NUMPED='.$value['NUMPED'].'"><i class="fa-solid fa-file"></i></a>';
                  switch ($value["STATUSCHECKOUT"]) {
                    case 'PENDENTE':
                      echo '<a class="btn btn-xs btn-outline-info py-1" href="index.php?op='.$dados['op'].'&nav='.$dados['nav'].'&aba='.$dados['aba'].'&acao=checkout_confirmaAbrir&NUMPED='.$value['NUMPED'].'&IDCHECKOUT='.$value['IDCHECKOUT'].'"><i class="fa-solid fa-list-check"></i></a>';
                      break;

                    case 'SEPARANDO':
                      if ($_SESSION['login']['IDUSUARIO'] == $value['IDUSURCONFERENTE'] || $_SESSION['login']['IDUSUARIO'] == "1") {
                        echo '<a class="btn btn-xs btn-outline-info py-1" href="index.php?op='.$dados['op'].'&nav='.$dados['nav'].'&aba='.$dados['aba'].'&acao=checkout_abrir&IDCHECKOUT='.$value['IDCHECKOUT'].'"><i class="fa-solid fa-list-check"></i></a>';
                      }
                      if ($_SESSION['login']['PERFIL'] == "ADMINISTRADOR") {
                        echo '<a class="btn btn-xs btn-outline-danger py-1" href="index.php?op='.$dados['op'].'&nav='.$dados['nav'].'&aba='.$dados['aba'].'&acao=checkout_confirmaResetarModal&NUMPED='.$value['NUMPED'].'&IDCHECKOUT='.$value['IDCHECKOUT'].'"><i class="fa-solid fa-trash"></i></a>';
                      }
                      break;

                    case 'TRANSITO':
                      if ($_SESSION['login']['IDUSUARIO'] == $value['IDUSURCONFERENTE']) {
                        echo '<a class="btn btn-xs btn-outline-info py-1" href="index.php?op='.$dados['op'].'&nav='.$dados['nav'].'&aba='.$dados['aba'].'&acao=checkout_abrir&IDCHECKOUT='.$value['IDCHECKOUT'].'"><i class="fa-solid fa-list-check"></i></a>';
                      }
                      if ($_SESSION['login']['PERFIL'] == "ADMINISTRADOR") {
                        echo '<a class="btn btn-xs btn-outline-danger py-1" href="index.php?op='.$dados['op'].'&nav='.$dados['nav'].'&aba='.$dados['aba'].'&acao=checkout_confirmaResetarModal&NUMPED='.$value['NUMPED'].'&IDCHECKOUT='.$value['IDCHECKOUT'].'"><i class="fa-solid fa-trash"></i></a>';
                      }
                      break;

                    case 'FINALIZADO':
                      if ($_SESSION['login']['PERFIL'] == "ADMINISTRADOR") {
                        echo '<a class="btn btn-xs btn-outline-danger py-1" href="index.php?op='.$dados['op'].'&nav='.$dados['nav'].'&aba='.$dados['aba'].'&acao=checkout_confirmaResetarModal&NUMPED='.$value['NUMPED'].'&IDCHECKOUT='.$value['IDCHECKOUT'].'"><i class="fa-solid fa-trash"></i></a>';
                      }
                      break;
                  }

                  echo '</div></td>';
                  echo '</tr>';
                }
                ?>
                </tbody>
              </table>
            </div>
          </div>

        <?php endif ?>

      </div>
    </main>
  </div><!-- row -->
</div><!-- container-fluid -->