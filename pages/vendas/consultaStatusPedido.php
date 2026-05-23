<section class="content-header">
  <h1>
    Orçamento
    <small>Consulta</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="index.php"><i class="fa fa-home"></i> Home</a></li>
    <li class="active">Orçamento</li>
  </ol>
</section>


<!-- Main content -->
<section class="content">

  <?php
  require_once('pages/vendas/function.php');
  if (isset($_GET) && count($_GET)>0) {
    // varDump2($_GET);
    $CABECALHO = buscaDadosCabecalhoStatus($_GET['ORCAMENTOID']);
    // varDump2($CABECALHO);

    if ( ($CABECALHO["POSICAO_ATUAL"] == "REJEITADO") && (trim($CABECALHO["OBSERVACAO"]) == "Nenhum item gravado") ) {
      if(reprocessaPedido($CABECALHO["CODUSUR"], $CABECALHO["NUMPEDRCA"])){
        $CABECALHO = buscaDadosCabecalhoStatus($_GET['ORCAMENTOID']);
      }
    }
  }
  ?>

  <div class="row">
    <div class="col-md-12">

      <div class="box">
      <!-- /.box-header -->
      <div class="box-body">

        <table id="tb_default" class="table table-bordered table-striped">

          <thead>
          <tr>
            <th>#</th>
            <th>NR ORC</th>
            <th>NUM PED</th>
            <th>POSICAO</th>
            <th>USUARIO(a)</th>
            <th>VENDEDOR(a)</th>
            <th>CLIENTE</th>
            <th>RETORNO</th>
            <th></th>
          </tr>
          </thead>

          <tbody>
          <?php
            echo '<tr>';
            echo '  <td>'.($key+1).'</td>';
            echo '  <td>'.$CABECALHO['IDORCAMENTO'].'</td>';
            echo '  <td>'.$CABECALHO['NUMPED'].'</td>';
            echo '  <td>';
            switch ($CABECALHO['POSICAO_ATUAL']) {
                case 'ORCAMENTO': echo '<a style="padding:5px" class="label label-default">ORCAMENTO</a>'; break;
                case 'BLOQUEADO': echo '<a style="padding:5px" class="label label-warning">BLOQUEADO</a>'; break;
                case 'MONTADO': echo '<a style="padding:5px" class="label label-primary">MONTADO</a>'; break;
                case 'LIBERADO': echo '<a style="padding:5px" class="label label-primary">LIBERADO</a>'; break;
                case 'FATURADO': echo '<a style="padding:5px" class="label label-success">FATURADO</a>'; break;
                case 'CANCELADO': echo '<a style="padding:5px" class="label label-danger">CANCELADO</a>'; break;
                case 'REJEITADO': echo '<a style="padding:5px" class="label label-danger">REJEITADO</a>'; break;
              }  
            echo '  </td>';
            echo '  <td>'.$CABECALHO['USUARIO'].'</td>';
            echo '  <td>'.$CABECALHO['CODUSUR'].''.$CABECALHO['VENDEDOR'].'</td>';
            echo '  <td>'.$CABECALHO['CODCLI'].'- '.$CABECALHO['CLIENTE'].'</td>';
            echo '  <td>'.$CABECALHO['OBSERVACAO'].'</td>';
            echo '  <td align="right">',
                  '<table>',
                  '<form method="post" enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].'"  target="_blank">',
                  '<input type="hidden" name="op" value="65">',
                  '<input type="hidden" name="IDORCAMENTO" value="'.$CABECALHO['ORCAMENTOID'].'">',
                  '<input type="hidden" name="NUMPEDRCA" value="'.$CABECALHO['NUMPEDRCA'].'">',
                  '<tr>';
            echo  '<td style="padding-right:5px">';
            echo  '   <button type="submit" name="detalhePedido" class="btn btn-primary btn-xs btn-block" title="Detalhe Pedido">';
            echo  '     <i class="fa fa-search"></i> Detalhes';
            echo  '   </button>';
            echo  '</td>';
            echo '</tr></form>';
            echo '</table>';
            echo '<a target="_blank" class="btn btn-info btn-xs btn-block" href="pages/vendas/exportaPDFPedidoVenda.php?NUMPEDRCA='.$CABECALHO['NUMPEDRCA'].'"><i class="fa fa-mail-forward"></i> Rel 317</a>';
            echo '<a target="_blank" class="btn btn-info btn-xs btn-block" href="pages/vendas/exportaPDFEtiquetaVenda.php?NUMPEDRCA='.$CABECALHO['NUMPEDRCA'].'"><i class="fa fa-print"></i> Etiquetas</a>';
            echo '</td>';
            echo '</tr>';
          ?>
          </tbody>

        </table>
      </div>
      <!-- /.box-body -->
      </div>
      <!-- /.box -->

    </div><!--/.col (left) -->
  </div>   <!-- /.row -->

</section><!-- /.content -->
