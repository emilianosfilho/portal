
    
  <table class="table table-bordered table-striped table-hover" style="width: 100%">

    <thead>
      <tr>
        <th>Numoriginal</th>
        <th>Codprod</th>
        <th style="width:50%">Descrição</th>
        <th style="width:50%">Marca</th>
        <th>Locação</th>
        <th>Estoque</th>
        <th>Etiqueta</th>
        <th>Ações</th>
      </tr>
    </thead>

    <tbody>
    <?php 
      if (isset($dados['acao'])) {
        if ($_SESSION['produtos']) {
          foreach ($_SESSION['produtos'] as $key => $value) {
            // varDump2($value);  die();

            if (!isset($value['QTETIQUETAS'])) {
              $_SESSION['produtos'][$key]['QTETIQUETAS'] = 1;
              $value['QTETIQUETAS'] = 1;
            }

            echo PHP_EOL;
            echo '<tr>';
            echo '<td>'.$value['NUMORIGINAL'].'</td>';
            echo '<td>'.$value['CODPROD'].'-'.$value['DV'].'</td>';
            echo '<td>'.$value['DESCRICAO'].'</td>';
            echo '<td>'.$value['MARCA'].'</td>';
            echo '<td>'.$value['LOCACAO'].'</td>';
            echo '<td>'.$value['QTESTOQUE'].'</td>';
            echo '<td>';
            echo '<div class="btn-group"><a href="index.php?op=132&acao=decEtiqueta&CODPROD='.$value['CODPROD'].'" class="btn btn-danger btn-xs p-1"><i class="fa fa-minus"></i></a><a href="#" class="btn btn-xs p-1">'.$value['QTETIQUETAS'].'</a><a href="index.php?op=132&acao=acrEtiqueta&CODPROD='.$value['CODPROD'].'" class="btn btn-success btn-xs p-1"><i class="fa fa-plus"></i></a></div></td>';
            echo '<td>';
            echo '  <div class="btn-group m-0">';
            echo '    <a target="_blanck" class="btn btn-outline-success btn-xs p-1" href="pages/logistica/extratoProduto.php?CODPROD='.$value['CODPROD'].'" title="Extrato"><i class="fa fa-file"></i> </a>';
            echo '    <a href="index.php?op=132&acao=alterarLocacao&CODPROD='.$value['CODPROD'].'&LOCACAO='.$value['LOCACAO'].'" class="btn btn-outline-primary btn-xs p-1" title="Alterar Locação"><i class="fa fa-retweet"></i> </a>';
            echo '    <a href="index.php?op=132&acao=historicoLocacao&CODPROD='.$value['CODPROD'].'" class="btn btn-outline-info btn-xs p-1" title="Histórico"><i class="fa fa-clock"></i> </a>';
            echo '    <a href="index.php?op=132&acao=deleteItemLista&key='.$key.'" class="btn btn-outline-danger btn-xs p-1" title="Remover desta lista"><i class="fa fa-trash"></i> </a>';
            echo '  </div>';
            echo '</td>';
            echo '</tr>';
          }
        }
      }
      ?>
    </tbody>

  </table>
