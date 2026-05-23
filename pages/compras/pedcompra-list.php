<div class="row">
  <div class="col-12 text-lg">
    <table id="tblEditavel" class="table table-bordered table-striped table-hover mt-4" style="width: 100%">
      <thead>
        <tr>
          <th>#</th>
          <th>NUMPED</th>
          <th>CODFORNEC</th>
          <th>FORNECEDOR</th>
          <th>DATA</th>
          <th>QTD ITENS</th>
          <th>TIPO</th>
          <th></th>
        </tr>
      </thead>
      <tbody>

        <?php foreach ($_SESSION['LISTA'] as $key => $value) : ?>

          <tr>
          <td><?= ++$ord ?></td>
          <td><?= $value['NUMPED'] ?></td>
          <td><?= $value['CODFORNEC'] ?></td>
          <td><?= $value['FORNECEDOR'] ?></td>
          <td><?= formataDataOracletoBr($value['DATA']) ?></td>
          <td><?= $value['QTD_ITENS'] ?></td>
          <td><?= $value['TIPO'] ?></td>
          <td>
            <div class="btn-group m-0">
              <a target="_blanck" class="btn btn-outline-primary btn-sm" href="pages/compras/pedcompra-extrato.php?TIPOPESQUISA=<?=$value['TIPO']?>&NUMPED=<?= $value['NUMPED'] ?>" title="Extrato"><i class="fa fa-file"></i> </a>
            </div>
          </td>
          </tr>
        <?php endforeach; ?>

      </tbody>

    </table>

  </div>
</div>