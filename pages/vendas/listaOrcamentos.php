  <table id="tb_orcamento" class="table table-bordered table-striped table-hover w-100">

    <thead>
    <tr>
      <th>Nr Orc</th>
      <th>Data</th>
      <th>Cliente</th>
      <th>UF</th>
      <th>Vendedor</th>
      <th>Máquina</th>
      <th>Valor</th>
      <th>Status</th>
      <th>Ação</th>
    </tr>
    </thead>

    <tbody>
    <?php
    if ($LISTAORCAMENTO = pesquisaOrcamento($dados)) {
      foreach ($LISTAORCAMENTO as $key => $value) {
        // varDump2($value); die();
		    echo '<tr>';
        echo '  <td>'.$value['IDORCAMENTO'].'</td>';
        echo '  <td>'.formataDataOracletoBr($value['DATA']).'</td>';
        echo '  <td>'.$value['CODCLI'].'- '.$value['CLIENTE'].'</td>';
        echo '  <td>'.$value['UF'].'</td>';
        echo '  <td>'.$value['CODUSUR'].'- '.$value['RCA'].'</td>';
        echo '  <td>'.$value['MAQUINA'].'</td>';
        echo '  <td style="text-align: right">R$ '.moeda($value['VALORTOTAL']).'</td>';
        echo '  <td>';
        switch ($value['POSICAO_ATUAL']) {
            case '': echo '<span class="badge bg-secondary">ORCAMENTO</spam>'; break;
            case 'ORCAMENTO': echo '<span class="badge bg-secondary">ORCAMENTO</spam>'; break;
            case 'PENDENTE': echo '<span class="badge bg-primary">PENDENTE</spam>'; break;
            case 'BLOQUEADO': echo '<span class="badge bg-warning">BLOQUEADO</spam>'; break;
            case 'MONTADO': echo '<span class="badge bg-primary">MONTADO</spam>'; break;
            case 'LIBERADO': echo '<span class="badge bg-primary">LIBERADO</spam>'; break;
            case 'FATURADO': echo '<span class="badge bg-success">FATURADO</spam>'; break;
            case 'CANCELADO': echo '<span class="badge bg-danger">CANCELADO</spam>'; break;
            case 'REJEITADO': echo '<span class="badge bg-danger">REJEITADO</spam>'; break;
            default: echo '<span class="badge bg-info">'.$value['POSICAO_ATUAL'].'</spam>'; break;
          } 
        echo ' </td>';
        echo ' <td align="center">',
             '  <form name="form_'.$key.'" method="post" enctype="multipart/form-data" action="index.php">',
             '  <input type="hidden" name="op" value="62">',
             '  <input type="hidden" name="IDORCAMENTO" value="'.$value['IDORCAMENTO'].'">',
             '  <button type="submit" name="acao" value="consultaOrcamento" class="btn btn-primary btn-xs" ><i class="fa fa-search"></i></button>';
        echo '  </form>';
        echo ' </td>';
		    echo '</tr>'.PHP_EOL;
      }
    }
    ?>
    </tbody>

  </table>