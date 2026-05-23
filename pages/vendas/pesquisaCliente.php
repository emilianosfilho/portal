
<main>
  <div class="container">
    <?php  
    require_once("pages/vendas/function.php");
    require_once("pages/vendas/controller.php");
    ?>
    <h2>
      <i class="fa-solid fa-plus"></i> Novo Orçamento
      <div class="float-sm-end">
        <a href="index.php?op=68" class="btn btn-sm btn-primary">
          <i class="fa-solid fa-search"></i> Consulta Orçamento
        </a>
      </div>
    </h2>    
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <form class="row g-3" action="index.php" method="POST">
            <input type="hidden" name="op" value="61">
            <div class="col-md-2">
              <label class="form-label">Cód. Cliente</label>
              <input type="text" name="CODCLI" class="form-control" autocomplete="off" value="<?=($_POST['CODCLI']<>"")?$_POST['CODCLI']:""?>">
            </div>
            <div class="col-md-3">
              <label class="form-label">CGC Cliente</label>
              <input type="text" name="CGCENT" class="form-control" autocomplete="off" value="<?=($_POST['CGCENT']<>"")?$_POST['CGCENT']:""?>">
            </div>
            <div class="col-md-4">
              <label class="form-label">Nome Cliente</label>
              <input type="text" name="NOMECLIENTE" class="form-control" autocomplete="off" value="<?=($_POST['NOMECLIENTE']<>"")?$_POST['NOMECLIENTE']:""?>">
            </div>
            <div class="col-md-3">
              <br>
              <button name="pesquisarCliente" type="submit" class="btn btn-primary btn-block w-100">Pesquisar</button>
            </div>
          </form>
        </div>
        <div class="card-body">

          <table id="tb_cliente" class="mt-2 table table-bordered table-striped table-hover">

            <thead>
            <tr>
              <th>#</th>
              <th>Fantasia</th>
              <th>Cliente</th>
              <th>Tipo</th>
              <th>CGC</th>
              <th>Município</th>
              <th>Excluído</th>
              <th>Bloqueado</th>
              <th>% Fast</th>
              <th>Limite</th>
              <th>Crédito</th>
              <th>#</th>
            </tr>
            </thead>

            <tbody>
            
            <?php
            if ( $LISTACLIENTES = pesquisarCliente($dados)){

              foreach ($LISTACLIENTES as $key => $value) {
                // varDump2($value); 
                // die();

                $CODCLI       = $value['CODCLI'];
                $FANTASIA     = $value['FANTASIA'];
                $CLIENTE      = $value['CLIENTE'];
                $CGCENT       = $value['CGCENT'];
                $TIPOFJ       = $value['TIPOFJ'];
                $CODFILIALNF  = $value['CODFILIALNF'];
                $TIPOFJ       = ($TIPOFJ=='F') ? 'FÍSICA' : 'JURÍDICA';
                $MUNICIPIO    = $value['MUNICIPIO'];
                $UF           = $value['UF'];
                $EXCLUIDO     = ($value['EXCLUIDO']=='S')
                                ?'<span class="badge rounded-pill bg-danger">Sim</span>'
                                :'<span class="badge rounded-pill bg-success">Não</span>';
                $BLOQUEADO    = ($value['BLOQUEADO']=='S')
                                ?'<span class="badge rounded-pill bg-danger">Sim</span> '.$value['MOTIVOBLOQ']
                                :'<span class="badge rounded-pill bg-success">Não</span>';
                $FAST         = moeda($value['FAST'], 2);
                $VLCREDDISP   = moeda($value['VLCREDDISP'], 0);
                $VLCREDITO    = moeda($value['VLCREDITO'], 0);


                echo PHP_EOL.'<tr>'.
                      '<td>'.$CODCLI.'</td>'.
                      '<td>'.$FANTASIA.'</td>'.
                      '<td>'.$CLIENTE.'</td>'.
                      '<td>'.$TIPOFJ.'</td>'.
                      '<td>'.$CGCENT.'</td>'.
                      '<td>'.$MUNICIPIO.' - '.$UF.'</td>'.
                      '<td>'.$EXCLUIDO.'</td>'.
                      '<td>'.$BLOQUEADO.'</td>'.
                      '<td style="text-align: right;">'.$FAST.'</td>'.
                      '<td style="text-align: right;">'.$VLCREDDISP.'</td>'.
                      '<td style="text-align: right;">'.$VLCREDITO.'</td>';
                echo ' </td>';
                echo ' <td align="center">',
                     '  <form name="form_'.$key.'" method="post" enctype="multipart/form-data" action="index.php">',
                     '  <input type="hidden" name="op" value="62">',
                     '  <input type="hidden" name="CODCLI" value="'.$CODCLI.'">',
                     '  <input type="hidden" name="CODUSUR" value="'.$_SESSION['login']['CODUSUR'].'">',
                     '  <input type="hidden" name="CODFILIALNF" value="'.$CODFILIALNF.'">',
                     '  <button type="submit" name="abrirNovoOrcamento" class="btn btn-xs btn-success"   target="_blank"><i class="fa-solid fa-forward"></i></button>';
                echo '  </form>';
                echo ' </td>';
                echo '</tr>';
              }
            }

            ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</main>


