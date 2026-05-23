<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    <i class="ion ion-2x ion-ios-cog"></i> Parâmetros de novos produtos
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Cadastros de Produtos</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">

    <!-- left column -->
    <div class="col-md-9">
      <!-- general form elements -->
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="ion ion-2x ion-ios-cog"></i> Parâmetros de novos produtos</h3>
        </div>
        <br>
        <!-- form start -->
        <form class="form-horizontal" action="index.php" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="op" value="30">
          <div class="box-body">
            <?php 
            require('pages/compras/function.php');
            $param = buscaParametros();
            if ($param) {
              $cont=0;
              echo '<div class="row">';
              foreach ($param as $key => $value) {
                // varDump2($value); die();
                echo '<div class="form-group">';
                echo '  <label class="col-sm-4 control-label">'.$value['parametro'].'</label>';
                echo '  <div class="col-sm-4">';
                echo '    <input type="text" class="form-control" name="'.$value['parametro'].'" value="'.$value['valor'].'">';
                echo '  </div>';
                echo '  <div class="col-sm-4">';
                echo '    Valor Padrão: <b>'.$value['valor'].'</b> [<i>'.utf8_encode($value['comentario']).'</i>]';
                echo '  </div>';
                echo '</div>';

              }
              echo '</div>';
            }
            ?>
          </div>
          <div class="box-footer">
            <button name="salvarPrarametros" type="submit" class="btn btn-primary pull-right"><i class="fa fa-upload"></i> Enviar Arquivo</button>
          </div>
        </form>
      </div>
      <!-- /.box -->
    </div>


    <div class="col-md-3">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-info-circle"></i> Ajuda</h3>
        </div>
        <div class="box-body" style="text-align: center">
          <div class="btn-group-vertical">
            <a class="btn btn-block btn-social btn-bitbucket" href="index.php?op=31"><i class="fa fa-upload"></i>Enviar Arquivo</a>
            <a class="btn btn-block btn-social btn-google" href="index.php?op=36"><i class="fa fa-list"></i> Parâmetros</a>
            <a class="btn btn-block btn-social btn-twitter" href="download/PRODUTO.xls" target="_blanck"><i class="fa fa-download"></i> Baixar Modelo</a>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>
<?php  
if(isset($_SESSION['LISTAPRODUTOSSALVOS'])) 
  unset($_SESSION['LISTAPRODUTOSSALVOS']);
if(isset($_SESSION['VALIDACADASTROS'])) 
  unset($_SESSION['VALIDACADASTROS']);

if ($_SESSION['login']['MATRICULA'] == "") {
  $modal['type'] = 'danger';
  $modal['msg']  = '<h3>O seu cadastro de usuário está incompleto.</h3>';
  $modal['msg'] .= 'É obrigatório que o campo Matrícula Winthor do seu usuário esteja preenchido corretamente<br>';
  $modal['msg'] .= '<a class="btn btn-primary" href="index.php?op=12&edit&id='.$_SESSION['login']['IDUSUARIO'].'">Clique aqui para atualizar o seu cadastro</a><br>';
  insereModal($modal);
}
?>
