<div class="row">
  <div class="col-12 text-lg">
    <table id="tblEditavel" class="table table-bordered table-striped table-hover mt-4" style="width: 100%">
      <thead>
        <tr>
          <th>#</th>
          <th>Cod Produto</th>
          <th>Núm Original</th>
          <th>Marca</th>
          <th>Descrição</th>
          <th>Cod Fábrica</th>
          <th>Locação</th>
          <th width="10%">Ações</th>
        </tr>
      </thead>
      <tbody>

        <?php foreach ($_SESSION['PRODUTOS'] as $key => $value) : ?>

          <?php if ($value['CODPROD']<>""): ?>

            <?php 
            if (!isset($ord))    $ord = 0;                
            if ($value['QTETIQUETA'] == "") $value['QTETIQUETA'] = 1;
            ?>
           
            <tr>
            <td><?= ++$ord ?></td>
            <td><?= $value['CODPROD'].'-'.$value['DV'] ?></td>
            <td><?= $value['NUMORIGINAL'] ?></td>
            <td><?= $value['MARCA'] ?></td>
            <td><?= $value['DESCRICAO'] ?></td>
            <td><?= $value['CODFAB'] ?></td>
            <td><?= $value['LOCACAO'] ?></td>
            <td>
              <div class="btn-group m-0">
                <a target="_blanck" class="btn btn-outline-success py-1" href="pages/logistica/produto-extrato.php?CODPROD=<?= $value['CODPROD'] ?>" title="Extrato"><i class="fa fa-file"></i> </a>
                <!-- <a href="index.php?op=<?=$dados['op']?>&acao=modalAlterarLocacao&CODPROD=<?= $value['CODPROD'] ?>&LOCACAO=<?= $value['LOCACAO'] ?>" class="btn btn-outline-primary" title="Alterar Locação"><i class="fa fa-retweet"></i> </a>
                <a href="index.php?op=<?=$dados['op']?>&acao=historicoLocacao&CODPROD=<?= $value['CODPROD'] ?>" class="btn btn-outline-info" title="Histórico"><i class="fa fa-clock"></i> </a> -->
                <a href="index.php?op=<?=$dados['op']?>&acao=produto-validacao&CODPROD=<?= $value['CODPROD'] ?>" class="btn btn-outline-primary py-1" title="Exibir validação do produto"><i class="fa-solid fa-magnifying-glass"></i></a>
                <a href="index.php?op=<?=$dados['op']?>&acao=produtoExcluir&key=<?=$key?>" class="btn btn-outline-danger py-1" title="Remover desta lista"><i class="fa fa-ban"></i></a>
              </div>
            </td>
            </tr>
            
          <?php endif ?>
        
        <?php endforeach; ?>

      </tbody>

    </table>

      

  </div>
</div>