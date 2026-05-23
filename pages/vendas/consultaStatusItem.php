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
  if (isset($_POST) && count($_POST)>0) {

    // varDump2($_POST); die();

    $CABECALHO = buscaDadosCabRca($_POST['NUMPEDRCA']);
    // varDump2($CABECALHO);
    if ($CABECALHO == false) {
      exibeMensagem("Este pedido foi cancelado");
      redireciona("index.php?op=62");
    }



    $ITENS     = buscaDadosItem($_POST['NUMPEDRCA']);
    // varDump2($ITENS);

  }

  ?>

  <div class="row">
    <div class="col-md-12">

      <div class="box">
      <!-- /.box-header -->
       <div class="box-header with-border">
          <h4>Cabeçalho</h4>
       </div>

      <div class="box-body">

        <table id="tb_default" class="table table-bordered table-striped">

          <thead>
          <tr>
            <th>#</th>
            <th>IMPORTACAO</th>
            <th>POSICAO</th>
            <th>NR ORC</th>
            <th>NUM PED</th>
            <th>VENDEDOR(a)</th>
            <th>CLIENTE</th>
            <th>RETORNO</th>
          </tr>
          </thead>

          <tbody>
          <?php
            foreach ($CABECALHO as $key => $value) {
              // varDump2($value); die();

              echo '<tr>';
              echo '  <td>'.($key+1).'</td>';
              echo '  <td>'.$value['IMPORTADO'].'</td>';
              echo '  <td>'.$value['POSICAO'].'</td>';
              echo '  <td>'.$value['ORCAMENTOID'].'</td>';
              echo '  <td>'.$value['NUMPED'].'</td>';
              echo '  <td>'.$value['RCA'].'</td>';
              echo '  <td>'.$value['CLIENTE'].'</td>';
              echo '  <td>'.$value['RETORNO'].'</td>';
              echo '</tr>';
            }
          ?>
          </tbody>

        </table>
      </div>
      <!-- /.box-body -->
      </div>
      <!-- /.box -->

    </div><!--/.col (left) -->
  </div>   <!-- /.row -->


  <div class="row">
    <div class="col-md-12">

      <div class="box">
      <!-- /.box-header -->
       <div class="box-header with-border">
          <h4>Itens</h4>
       </div>

      <div class="box-body">

        <table id="tb_clean2" class="table table-bordered table-striped">

          <thead>
          <tr>
            <th>#</th>
            <th>NUMORIGINAL</th>
            <th>CODPROD</th>
            <th>PRODUTO</th>
            <th>MARCA</th>
            <th>PREÇO</th>
            <th>QT PEDIDA</th>
            <th>QT ATENDIDA</th>
            <th>STATUS</th>
            <th>RETORNO</th>
          </tr>
          </thead>

          <tbody>
          <?php
            foreach ($ITENS as $key => $value) {
              // varDump2($value); die();

              echo '<tr>';
              echo '  <td>'.$value['NUMSEQ'].'</td>';
              echo '  <td>'.$value['NUMORIGINAL'].'</td>';
              echo '  <td>'.$value['CODPROD'].'</td>';
              echo '  <td>'.$value['DESCRICAO'].'</td>';
              echo '  <td>'.$value['MARCA'].'</td>';
              echo '  <td>'.moeda($value['PVENDA']).'</td>';
              echo '  <td>'.$value['QT'].'</td>';
              echo '  <td>'.$value['QT_FATURADA'].'</td>';
              echo '  <td>'.$value['STATUS'].'</td>';
              echo '  <td>'.$value['OBSERVACAO_PC'].'</td>';
            }
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
