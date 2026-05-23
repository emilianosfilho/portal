<?php
require("pages/compras/function.php");
require("pages/compras/validaArquivo.php");
?>

  
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    <i class="ion ion-2x ion-ios-cart"></i> Pedido de Compra
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Cadastros de Produtos</li>
  </ol>
</section>

<?php  
if ($_SESSION['ARQUIVO']) {
  foreach ($_SESSION['ARQUIVO'] as $key => $valueArquivo) {
    // varDump2($valueArquivo); die();
    $VLTOTAL = 0;
    echo '<section class="invoice">';
    echo '  <div class="row invoice-info">';

    echo '    <div class="col-sm-5 invoice-col">';
    echo '      FORNECEDOR';
    echo '      <address>';
    echo '        <strong>'.$valueArquivo['FORNEC']['CODFORNEC'].'- '.$valueArquivo['FORNEC']['FORNECEDOR'].'</strong><br>';
    echo '        CNPJ: '.$valueArquivo['FORNEC']['CNPJ'].'<br>';
    echo '        '.$valueArquivo['FORNEC']['CIDADE'].' / '.$valueArquivo['FORNEC']['UF'].'<br>';
    echo '        TIPO FORNECEDOR: '.$valueArquivo['FORNEC']['TIPOFORNEC'].'<br>';
    echo '      </address>';
    echo '    </div>';

    echo '    <div class="col-sm-5 invoice-col">';
    echo '      FILIAL';
    echo '      <address>';
    echo '        <strong>'.$valueArquivo['FILIAL']['CODFILIAL'].'- '.$valueArquivo['FILIAL']['RAZAOSOCIAL'].'</strong><br>';
    echo '        CNPJ: '.$valueArquivo['FILIAL']['CNPJ'].'<br>';
    echo '        '.$valueArquivo['FILIAL']['CIDADE'].' / '.$valueArquivo['FILIAL']['UF'].'<br>';
    echo '      </address>';
    echo '    </div>';
    
    echo '    <div class="col-sm-2 invoice-col">';
    echo '      <b>PEDIDO NR #'.$key.'</b>';
    echo '    </div>';
    echo '  </div>';

    echo '  <div class="row">';
    echo '    <div class="col-xs-12 table-responsive">';
    echo '      <table class="table table-striped">';
    echo '        <thead>';
    echo '        <tr>';
    echo '          <th>#</th>';
    echo '          <th>Codprod</th>';
    echo '          <th>Produto</th>';
    echo '          <th>Numoriginal</th>';
    echo '          <th>Marca</th>';
    echo '          <th>NCM</th>';
    echo '          <th>Qtd</th>';
    echo '          <th>Preço</th>';
    echo '          <th>Subtotal</th>';
    echo '          <th>Trib Ent</th>';
    echo '        </tr>';
    echo '        </thead>';
    echo '        <tbody>';
    $erroTrib = false;
    if ($valueArquivo['ITENS']) {
      foreach ($valueArquivo['ITENS'] as $keyITENS => $valueITENS) {
        $SUBTOTAL = ($valueITENS['REGISTRO']['QTPEDIDO'] * $valueITENS['REGISTRO']['PCOMPRA']);
        $VLTOTAL += $SUBTOTAL;

        echo '        <tr>';
        echo '          <td>'.($keyITENS+1).'</td>';
        echo '          <td>'.$valueITENS['PRODUTO']['CODPROD'].'-'.$valueITENS['PRODUTO']['DV'].'</td>';
        echo '          <td>'.$valueITENS['PRODUTO']['DESCRICAO'].'</td>';
        echo '          <td>'.$valueITENS['PRODUTO']['NUMORIGINAL'].'</td>';
        echo '          <td>'.$valueITENS['PRODUTO']['MARCA'].'</td>';
        echo '          <td>'.$valueITENS['PRODUTO']['NCM'].'</td>';
        echo '          <td>'.$valueITENS['REGISTRO']['QTPEDIDO'].'</td>';
        echo '          <td>R$ '.moeda($valueITENS['REGISTRO']['PCOMPRA']).'</td>';
        echo '          <td>R$ '.moeda($SUBTOTAL).'</td>';
        if ($valueITENS['REGISTRO']['TRIBENT'] == "S") {
          echo '        <td class="bg-success">SIM</td>';
        } else {
          $erroTrib = true;
          echo '        <td class="bg-red">NÃO</td>';
        }
        echo '        </tr>';
      }
    }
    echo '        </tbody>';
    echo '      </table>';
    echo '    </div>';
    echo '  </div>';

    echo '  <div class="row">';
    echo '    <div class="col-xs-8">';
    echo '    </div>';
    echo '    <div class="col-xs-4">';
    echo '      <div class="table-responsive">';
    echo '        <table class="table">';
    echo '          <tr>';
    echo '            <th>TOTAL:</th>';
    echo '            <td style="background: #333; color: #fff; font-size: 20px; font-weight: bold; text-align: center;">R$ '.moeda($VLTOTAL).'</td>';
    echo '          </tr>';
    echo '        </table>';
    echo '      </div>';
    echo '    </div>';
    echo '  </div>';

    echo '</section>';
  }
}
?>


<section class="invoice">
  <div class="row">
    <form role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="op" value="50">
      <div class="box-footer">
        <?php if ($erroTrib): ?>
          <button type="submit" name="gerarTributEntrada" class="btn btn-success pull-right"><i class="fa fa-forward"></i> Processar Arquivo</button>
        <?php else: ?>
          <button type="submit" name="procesarArquivo" class="btn btn-success pull-right"><i class="fa fa-forward"></i> Processar Arquivo</button>
        <?php endif ?>
      </div>
    </form>
  </div>
</section>

<!-- /.content -->
<div class="clearfix"></div>