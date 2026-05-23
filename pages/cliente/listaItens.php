<div class="col-md-12">
  <div class="card shadow ">
    <div class="card-body">

      <table class="table table-striped table-hover w-100">

        <thead>
          <tr>
            <TH>#</TH>
            <TH>Peça</TH>
            <TH>Descrição</TH>
            <TH>Marca</TH>
            <TH>Disponibilidade</TH>
            <TH align="right">Preço</TH>
            <TH align="right">Quantidade</TH>
            <TH align="right">Total</TH>
            <TH>Ação</TH>
          </tr>
        </thead>
        <tbody>
          <?php $VALORTOTAL = 0; ?>

        <?php foreach ( buscaItensOrcamento($_SESSION['ORC_CLIENTE']['CAB']['IDORCAMENTO']) as $key => $value): ?>

          <?php 

            $corTexto = "";
            $title    = "";

            if ($value['PVENDA'] <= 0.001) {
              $corTexto = "text-danger";
              $title   .= "PREÇO DE VENDA INVÁLIDO ";
            }
            if ($value['QTPEDIDA'] <= 0.001) {
              $corTexto = "text-danger";
              $title   .= "QUANTIDADE PEDIDA INVÁLIDO ";
            }
            if ($value['QTDISPONIVEL'] < $value['QTPEDIDA']) {
              $corTexto = "text-danger";
              $title   .= "QUANTIDADE PEDIDA INFERIOR QUE A DISPONÍVEL ";
            }
            if ($value['TRIBUT'] == "N") {
              $corTexto = "text-danger";
              $title   .= "PRODUTO SEM TRIBUTAÇÃO PARA A UF: ".$_SESSION['ORC_CLIENTE']['CLIENTE']['UF'];
            }

            $VALORTOTAL += (intval($value['QTPEDIDA'])*floatval($value['PVENDA']));

          ?>
          

          <tr>
            <form id="formPreco<?=$key?>" method="post" action="index.php">
              <input type="hidden" name="op" value="151">
              <input type="hidden" name="key" value="<?= $key ?>">
              <input type="hidden" name="IDORCAMENTO" value="<?= $_SESSION['ORC_CLIENTE']['CAB']['IDORCAMENTO'] ?>">
              <input type="hidden" name="IDORCAMENTOI" value="<?= $value['IDORCAMENTOI'] ?>">
              <input type="hidden" name="CODPROD" value="<?= $value['CODPROD'] ?>">
              <input type="hidden" name="NUMORIGINAL" value="<?= $value['NUMORIGINAL'] ?>">
              <input type="hidden" name="CODPECA_OLD" value="<?= $value['CODPECA'] ?>">
              <input type="hidden" name="CODPECA_NEW" value="<?= $value['CODPECA'] ?>">
              <input type="hidden" name="PTABELA" value="<?= $value['PTABELA'] ?>">
              <input type="hidden" name="PVENDA_OLD" value="<?= $value['PVENDA'] ?>">
              <input type="hidden" name="PVENDA_NEW" value="<?= $value['PVENDA'] ?>">
              <input type="hidden" name="QTPEDIDA_OLD" value="<?= $value['QTPEDIDA'] ?>">
              <input type="hidden" name="DESCRICAO_OLD" value="<?= $value['DESCRICAO'] ?>">
              <input type="hidden" name="DESCRICAO_NEW" value="<?= $value['DESCRICAO'] ?>">
              <input type="hidden" name="MARCA_OLD" value="<?= $value['MARCA'] ?>">
              <input type="hidden" name="MARCA_NEW" value="<?= $value['MARCA'] ?>">
              <input type="hidden" name="CODCLI" value="<?= $_SESSION['ORC_CLIENTE']['CLIENTE']['CODCLI'] ?>">
              <td class="<?=$corTexto?>"><?= $key+1 ?></td>
              <td class="<?=$corTexto?>"><?= $value['CODPECA'] ?></td>
              <td class="<?=$corTexto?>"><?= $value['DESCRICAO'] ?></td>
              <td class="<?=$corTexto?>"><?= $value['MARCA'] ?></td>
              <td class="<?=$corTexto?>"><?= $value['DISPONIBILIDADE'] ?></td>
              <td class="<?=$corTexto?>"><?= moeda(floatval($value['PVENDA']),2) ?></td>
              <td class="<?=$corTexto?>">
                <input type="number" class="<?=$corTexto?>" name="QTPEDIDA_NEW" value="<?= $value['QTPEDIDA'] ?>" autocomplete="off" style="text-align:right;width: 40px;">
              </td>
              <td class="<?=$corTexto?>"><?= moeda((intval($value['QTPEDIDA'])*floatval($value['PVENDA'])),2) ?></td>
              <td style="width: 10%;">
                <div class="btn-group" role="group">

                  <button type="submit" name="acao" value="atualizaItemOrcamento" class="btn btn-xs btn-outline-success" title="Atualizar"><i class="fa fa-save"></i></button>

                  <button type="submit" name="acao" value="excluirItemOrcamento" class="btn btn-xs btn-outline-danger" title="Excluir"><i class="fa fa-trash"></i></button>

                
                </div>
              </td>
            </form>
          </tr>
        <?php endforeach ?>

        </tbody>
      </table>

      <div class="row">
        <div class="col-6"></div>
        <div class="col-6">
          <ul class="list-group h3">
            <li class="list-group-item d-flex justify-content-between align-items-start">
              <div class="ms-2 me-auto">
                <div class="fw-bold">Valor Total</div>
              </div>
              <span>R$ <?=moeda($VALORTOTAL)?></span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
