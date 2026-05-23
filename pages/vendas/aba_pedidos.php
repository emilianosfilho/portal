<?php 
  $listaPedidosWinthor = buscaPedidosWinthor($_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO']); 
  foreach ($listaPedidosWinthor as $key => $value) {
    if ($value['IMPORTADO'] == 'REJEITADO') {
      // varDump2($value);
      insereModalRejeitado($value);
    }
  }
?>

<a href="index.php?op=62&tab=pedidos&IDORCAMENTO=<?=$_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO']?>" class="btn btn-xs btn-secondary text-md my-2"><i class="fa-solid fa-arrows-rotate"></i> Atualizar</a>

<table class="table table-striped table-hover w-100">
  <thead>
    <tr>
      <th scope="col">Numpedrca</th>
      <th scope="col">Numped</th>
      <th scope="col">Data</th>
      <th scope="col">Cobrança</th>
      <th scope="col">Plano Pagamento</th>
      <th scope="col">Itens</th>
      <th scope="col">Valor</th>
      <th scope="col">Importado</th>
      <th scope="col">Posição</th>
      <th scope="col">Separação</th>
      <th scope="col" style="widows: 10%;">Ação</th>
    </tr>
  </thead>
  <tbody>
    
    <?php if ($listaPedidosWinthor): ?>
      <?php foreach ($listaPedidosWinthor as $key => $value): ?>

        <tr>
          <th scope="row"><?=$value['NUMPEDRCA']?></th>
          <td><?=$value['NUMPED']?></td>
          <td><?=$value['DATA']?></td>
          <td><?=$value['CODCOB']?></td>
          <td><?=$value['CODPLPAG']?></td>
          <td><?=$value['QTD_ITENS']?></td>
          <td><?=moeda($value['VLTOTAL'])?></td>
          <td>
            <?php
            switch ($value['IMPORTADO']) {
              case 'AGUARDANDO':
                echo '<span class="badge bg-secondary text-md">AGUARDANDO</span>';
                break;
              case 'SUCESSO':
                echo '<span class="badge bg-success text-md">SUCESSO</span>';
                break;
              case 'REJEITADO':
                echo '<span class="badge bg-danger text-md">REJEITADO</span>';
                break;
              case 'PROCESSANDO':
                echo '<span class="badge bg-warning text-md">PROCESSANDO</span>';
                break;
            }
            ?>
          </td>
          <td>
            <?php
            switch ($value['POSICAO']) {
              case 'REJEITADO':   echo '<span class="badge bg-danger text-md">REJEITADO</span>';  break;
              case 'CANCELADO':   echo '<span class="badge bg-danger text-md">CANCELADO</span>';  break;
              case 'MONTADO':     echo '<span class="badge bg-primary text-md">MONTADO</span>';  break;
              case 'FATURADO':    echo '<span class="badge bg-success text-md">FATURADO</span>';  break;
              default:            echo '<span class="badge bg-secondary text-md">PENDENTE</span>';  break;
            }
            ?>
          </td>
          <td>
            <?php
            switch ($value['SEPARACAO']) {
              case 'PENDENTE':   echo '<span class="badge bg-secondary text-md">PENDENTE</span>';  break;
              case 'SEPARANDO':     echo '<span class="badge bg-primary text-md">SEPARANDO ['.$value['SEPARADOR'].']</span>';  break;
              case 'FINALIZADO':    echo '<span class="badge bg-success text-md">FINALIZADO ['.$value['SEPARADOR'].']</span>';  break;
              case 'EM TRANSITO':    echo '<span class="badge bg-warning text-md">EM TRANSITO</span>';  break;
            }
            ?>
          </td>
          <td>
            <div class="btn-group">
              <a href="index.php?op=62&acao=exportaPDF-pedidoConferencia&NUMPED=<?=$value['NUMPED']?>&NUMPEDCLI=<?=$value['NUMPEDCLI']?>&NUMPEDRCA=<?=$value['NUMPEDRCA']?>" target="_blanck" class="btn btn-outline-secondary p-1"><i class="fas fa-file"></i> Pedido</a>
              <?php
              if(validaClienteAliquotaRR($value['CODCLI'], $value['NUMPED'])){
                echo '<a href="index.php?op=62&acao=ajustarAliquotaRR&CODCLI='.$value['CODCLI'].'&NUMPED='.$value['NUMPED'].'" class="btn btn-outline-danger p-1"><i class="fas fa-percent"></i> Aliquota</a>';
              }
              ?>
            </div>
          </td>
        </tr>

      <?php endforeach ?>
    <?php endif ?>

  </tbody>
</table>