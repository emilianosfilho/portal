<?php
$debug = 0;

// unset($_SESSION['msg']);
if (!isset($_SESSION['msg'])) {
  $_SESSION['msg'] = array();
}

// require ("pages/conf/conectaOracle.php");
require ("pages/vendas/function.php");

if (!empty($_POST)) {
  
  // varDump2($_POST); die();



  if (isset($_POST['pesquisar'])) {
    // varDump2($_POST);

    $pecas =  buscaPecas($_POST['IDMAQUINA']);
    // varDump2($pecas);

  }

  if (isset($_POST['incluirPeca'])) {
    // varDump2($_POST); die();
    $listaPeca = array();
    if (isset($_POST['PECA']) && !empty($_POST['PECA'])) {
      foreach ($_POST['PECA'] as $key => $value) {
        if (isset($value['CHECK'])) {
          array_push($listaPeca, $value);
        }
      }

      // varDump2($listaPeca); die();

      if (!empty($listaPeca)) {
        include("pages/vendas/incluirPecasMaquinaOrcamento.php");
      }

    }

  }



}

$maquina = buscaMaquinas();
// varDump2($maquina);
?>


    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Pesquisa Peça-Máquina
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-home"></i> Home</a></li>
        <li class="active">Pesquisa Peça-Máquina</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <div class="col-md-12">
        <div class="row">
          <div class="box box-primary">
            
            <form method="POST" action="index.php">

              <div class="box-header with-border">
                <h3 class="box-title">Pesquisa por Máquina</h3>
              </div>
              <!-- /.box-header -->
              <div class="box-body">

                <div class="col-md-6">

                  <div class="form-group">
                    <div id="maquina">
                      <label>Selecione a Máquina:</label>
                      <select id="IDMAQUINA" name="IDMAQUINA" class="form-control">
                        <?php

                        if ($maquina != false) {
                          foreach ($maquina as $key => $value) {
                            echo '<option value="'.$value['IDMAQUINA'].'">'.$value['MAQUINA'].'</option>';
                          }
                        }
                        ?>
                      </select>
                    </div>
                  </div>
 
                </div>
                <div class="col-md-2">
                  <label>&nbsp;</label>
                  <input type="submit" name="pesquisar" class="btn btn-primary form-control" value="Pesquisar">

                </div>

                <div class="col-md-12">

                  <?php

                  if (isset($_POST['pesquisar']) && isset($pecas) && $pecas != false) {
                    // varDump2("tem peca");
                    include("pages/vendas/listaPecasMaquina.php");
                  }

                  ?>

                </div>


              </div>
              <!-- /.col -->
              <!-- /.box-body -->

              <div class="box-footer">
                <input type="hidden" name="op" value="63">
                <input type="hidden" name="orcamentoID" value="<?= $_GET['orcamentoID']?>">
                <button name="incluirPeca" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Incluir Peça</button>
              </div>
          </form>
        </div>
      </div>
    </div>

    <!-- /.box -->
  </section>

