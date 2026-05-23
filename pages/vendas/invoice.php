<?php
  include("pages/vendas/modalAcompanhaFaturado.php");
  include("pages/vendas/modalDuplicar.php");
  if ($_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO'] <> null) {
    $CABECALHO = buscaInvoiceCab($_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO'], $_SESSION['ORCAMENTO']['CAB']['NUMPEDRCA']);
    // varDump2($CABECALHO); die();

    $ITEM = buscaInvoiceItem($_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO'], $_SESSION['ORCAMENTO']['CAB']['NUMPEDRCA']);
    // varDump2($ITEM); die();

  } else {
    die("IDORCAMENTO não localizado");
  }
?>
<div class="box box-primary">
  <div class="box-body">

    <section class="invoice">
      <!-- info row -->
      <div class="row invoice-info">
        <div class="col-sm-4 invoice-col">
          Cliente
          <address>
            <strong><?=mb_strtoupper($CABECALHO['CLIENTE_NOME'],'UTF-8')?></strong><br>
            <?=mb_strtoupper($CABECALHO['CLIENTE_ENDERECO'],'UTF-8')?><br>
            <?=mb_strtoupper($CABECALHO['CLIENTE_CIDADE'],'UTF-8')?><br>
            <?=mb_strtoupper($CABECALHO['CLIENTE_FONE'],'UTF-8')?><br>
            <?=mb_strtolower($CABECALHO['CLIENTE_EMAIL'],'UTF-8')?>
          </address>
        </div>
        <!-- /.col -->
        <div class="col-sm-4 invoice-col">
          Filial Venda
          <address>
            <strong><?=mb_strtoupper($CABECALHO['FILIAL_NOME'],'UTF-8')?></strong><br>
            <?=mb_strtoupper($CABECALHO['FILIAL_ENDERECO'],'UTF-8')?><br>
            <?=mb_strtoupper($CABECALHO['FILIAL_CIDADE'],'UTF-8')?><br>
            <?=mb_strtolower($CABECALHO['FILIAL_FONE'],'UTF-8')?><br>
            <?=mb_strtolower($CABECALHO['FILIAL_EMAIL'],'UTF-8')?>
          </address>
        </div>
        <!-- /.col -->
        <div class="col-sm-2 invoice-col">
          <b>Cobrança:</b><br> <?= $CABECALHO['COBRANCA'] ?><br>
          <b>Plano Pagamento:</b><br> <?= (strstr($CABECALHO['PLPAG'],'BOLETO'))?'<img src="'.@DIR_IMG.'barcode.png"> ':'';?><?=$CABECALHO['PLPAG'] ?><br>

        </div>
        <!-- /.col -->
        <div class="col-sm-2 invoice-col">
          <b>Orçamento:</b> <?= $CABECALHO['IDORCAMENTO'] ?><br>
          <b>Numpedrca:</b> <?= $CABECALHO['NUMPEDRCA'] ?><br>
          <b>Data:</b> <?= formataDataOracletoBr($CABECALHO['DATA']) ?><br>
          <b>Vendedor:</b><br><?= $CABECALHO['VENDEDOR'] ?>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

      <!-- Table row -->
      <div class="row">

        <div class="col-xs-12 table-responsive">
          <table class="table table-striped text-center">
            <thead>
            <tr>
              <th>Qtd</th>
              <th>Codprod</th>
              <th>Produto</th>
              <th>Marca</th>
              <th>Preço Uit.</th>
              <th>Sub Total</th>
              <th>Retorno</th>
            </tr>
            </thead>
            <tbody>
              <?php  
              $VLTOTAL = 0;
              if ($ITEM) {
                foreach ($ITEM as $key => $value) {
                  echo '<tr>';
                  echo '  <td>'.$value['QT'].'</td>';
                  echo '  <td>'.$value['CODPROD'].'</td>';
                  echo '  <td>'.$value['PRODUTO'].'</td>';
                  echo '  <td>'.$value['MARCA'].'</td>';
                  echo '  <td>R$ '.moeda($value['PVENDA']).'</td>';
                  echo '  <td>R$ '.moeda($value['QT'] * $value['PVENDA']).'</td>';
                  echo '  <td>'.trim($value['OBSERVACAO_PC']).'</td>';
                  echo '</tr>';
                  if ($value['OBSERVACAO_PC'] == "OK") {
                    $VLTOTAL += ($value['QT'] * $value['PVENDA']);
                  }
                }
              }
              ?>
            </tbody>
          </table>
        </div>
        <!-- /.col -->

      </div>

      <div class="row">

        <pre class="col-xs-6">
          <?= $CABECALHO['OBSERVACAO_PC'] ?> 
        </pre>



        <div class="col-xs-4">
          <p class="lead"><b>Totalizadores</b></p>

          <div class="table-responsive" style="font-size: 16px; margin: 0px; padding: 0px;">
            <table class="table text-center">
              <tr>
                <th style="width:40%">Subtotal:</th>
                <td>R$ <?= moeda($VLTOTAL,2) ?></td>
              </tr>
              <tr>
                <th>Frete:</th>
                <td>R$ <?= moeda($CABECALHO['VALORFRETE'],2) ?></td>
              </tr>
              <tr>
                <th>Total:</th>
                <td>R$ <?= moeda(($VLTOTAL + $CABECALHO['VALORFRETE']),2) ?></td>
              </tr>
            </table>
          </div>
        </div>
        <!-- /.col -->



        <div class="col-md-2">
          <p class="lead"><b>Ações</b></p>
          <form method="post" action="#">
            <input type="hidden" name="op" value="62">
            <input type="hidden" name="IDORCAMENTO" value="<?=$CABECALHO['IDORCAMENTO']?>">
            <input type="hidden" name="NUMPEDRCA" value="<?=$CABECALHO['NUMPEDRCA']?>">
            <input type="hidden" name="CODUSUR" value="<?=$CABECALHO['CODUSUR']?>">
            <div class="btn-group-vertical">
              <?php  
              $posicao_rejeitadas = array('C', 'R');
              if (!in_array($CABECALHO["POSICAO_ATUAL"], $posicao_rejeitadas)) { 
                ?>
                <button type="submit" class="btn btn-sm btn-social btn-flickr hidden-print" name="exportaPDFPedidoVenda">
                  <span class="fa fa-file-o" aria-hidden="true"></span> Pedido de Venda (Rel 317)
                </button>
                <button type="submit" class="btn btn-sm btn-social btn-dropbox hidden-print" name="exportaPDFEtiquetaVenda">
                  <i class="fa fa-tag"></i> Etiquetas de venda
                </button>
              <?php
              }
              ?>
              <button type="submit" class="btn btn-sm btn-social btn-bitbucket hidden-print" name="exportaPDF">
                <i class="fa fa-print"></i> Exporta Orçamento PDF
              </button>
              <?php if (($CABECALHO['IMPORTADO'] == '1') || ($CABECALHO['IMPORTADO'] == '3')): ?>
                <button type="submit" class="btn btn-sm btn-social btn-primary hidden-print" name="reprocessarIntegradora">
                  <i class="fa fa-retweet"></i> Reprocessar
                </button>
              <?php endif ?>
            </div>
          </form>
        </div>

      </div>
      <!-- /.row -->

    </section>
  </div>
</div>