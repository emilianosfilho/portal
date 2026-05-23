

<div class="col-md-12">
  <div class="card">
    <div class="card-body">

      <table id="tb_itensOrcamento" class="table table-bordered table-striped table-hover w-100">

        <thead>
          <tr>
            <TH>#</TH>
            <TH>Peça</TH>
            <TH>NumOrig</TH>
            <TH>Entrega</TH>
            <TH>Descrição</TH>
            <TH>Marca</TH>
            <TH>Qtd</TH>
            <TH align="right">P.Tabela</TH>
            <TH align="right">P.Venda</TH>
            <TH align="right">% Desc.</TH>
            <TH align="right">Total</TH>
            <TH align="right">Opções</TH>
          </tr>
        </thead>
        <tbody>
          <?php $_SESSION['ORCAMENTO']['ITENS'] = buscaItensOrcamento($_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO']); ?>
        <?php foreach ($_SESSION['ORCAMENTO']['ITENS'] as $key => $value): ?>

          <?php 
          // varDump2($value);

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
            if ($value['QTSALDOATUAL'] < $value['QTPEDIDA']) {
              $corTexto = "text-danger";
              $title   .= "QUANTIDADE PEDIDA INFERIOR QUE A DISPONÍVEL ";
            }
            if ($value['TRIBUT'] == "N") {
              $corTexto = "text-danger";
              $title   .= "PRODUTO SEM TRIBUTAÇÃO PARA A UF: ".$_SESSION['ORCAMENTO']['CLIENTE']['UF'];
            }
          ?>
          

          <tr>
            <form id="formPreco<?=$key?>" method="post" action="index.php">
              <input type="hidden" name="op" value="62">
              <input type="hidden" name="key" value="<?= $key ?>">
              <input type="hidden" name="IDORCAMENTO" value="<?= $_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO'] ?>">
              <input type="hidden" name="IDORCAMENTOI" value="<?= $value['IDORCAMENTOI'] ?>">
              <input type="hidden" name="CODPROD" value="<?= $value['CODPROD'] ?>">
              <input type="hidden" name="NUMORIGINAL" value="<?= $value['NUMORIGINAL'] ?>">
              <input type="hidden" name="CODPECA_OLD" value="<?= $value['CODPECA'] ?>">
              <input type="hidden" name="PTABELA" value="<?= $value['PTABELA'] ?>">
              <input type="hidden" name="PVENDAMIN" value="<?= $value['PVENDAMIN'] ?>">
              <input type="hidden" name="PVENDA_OLD" value="<?= $value['PVENDA'] ?>">
              <input type="hidden" name="QTPEDIDA_OLD" value="<?= $value['QTPEDIDA'] ?>">
              <input type="hidden" name="DESCRICAO_OLD" value="<?= $value['DESCRICAO'] ?>">
              <input type="hidden" name="MARCA_OLD" value="<?= $value['MARCA'] ?>">
              <input type="hidden" name="CODCLI" value="<?= $_SESSION['ORCAMENTO']['CLIENTE']['CODCLI'] ?>">
              <td class="fw-bold <?=$corTexto?>"><?= $key+1 ?></td>
              <td class="<?=$corTexto?>">
                <input type="text" name="CODPECA_NEW" value="<?= $value['CODPECA'] ?>" autocomplete="off" style="width: 90px;" class="fw-bold <?=$corTexto?>">
              </td>
              <td class="fw-bold <?=$corTexto?>"><?= $value['NUMORIGINAL'] ?></td>
              <td class="<?=$corTexto?>">
                <input type="text" name="DISPONIBILIDADE" value="<?= $value['DISPONIBILIDADE'] ?>" autocomplete="off" style="width: 100px;" class="fw-bold <?=$corTexto?>">
              </td>
              <td class="<?=$corTexto?>"><input type="text" name="DESCRICAO" value="<?= $value['DESCRICAO'] ?>" autocomplete="off" style="width: 180px;" class="fw-bold <?=$corTexto?>"></td>
              <td class="<?=$corTexto?>"><input type="text" name="MARCA" value="<?= $value['MARCA'] ?>" autocomplete="off" style="width: 120px;" class="fw-bold <?=$corTexto?>"></td>
              <td class="<?=$corTexto?>">
                <input type="number" class="fw-bold <?=$corTexto?>" name="QTPEDIDA_NEW" value="<?= $value['QTPEDIDA'] ?>" autocomplete="off" style="text-align:right;width: 40px;">
              </td>
              <td class="fw-bold <?=$corTexto?>"><?= moeda($value['PTABELA'],2) ?></td>
              <td class="<?=$corTexto?>"><input type="text" name="PVENDA_NEW" value="<?= moeda(floatval($value['PVENDA']),2) ?>" autocomplete="off" style="text-align:right;width: 50px;" class="fw-bold <?=$corTexto?>"></td>
              <td class="<?=$corTexto?>">
              <?php 
              if (moedaPHP($value['PTABELA']) > 0) {
                if (moedaPHP($value['PVENDA']) < moedaPHP($value['PTABELA'])) {
                  echo '<span class="fw-bold badge rounded-pill bg-danger">-'.moeda(((1-(moedaPHP($value['PVENDA'])/moedaPHP($value['PTABELA'])))*100),2).'</span>';
                } else {
                  echo '<span class="fw-bold badge rounded-pill bg-primary">+'.moeda((((moedaPHP($value['PVENDA'])/moedaPHP($value['PTABELA']))-1)*100),2).'</span>';
                }
              } else {
                echo '<span class="fw-bold badge rounded-pill bg-primary">+0,00</span>';
              }
              ?>
              </td>
              <td class="fw-bold <?=$corTexto?>"><?= moeda($SUBTOTAL = (moedaPHP($value['QTPEDIDA'])*moedaPHP($value['PVENDA'])),2) ?></td>
              <td>
                <div class="btn-group" role="group">

                  <button type="submit" name="acao" value="atualizaItemOrcamento" class="btn btn-xs btn-outline-success" title="Atualizar"><i class="fa fa-save"></i></button>

                  <button type="submit" name="acao" value="excluirItemOrcamento" class="btn btn-xs btn-outline-danger" title="Excluir"><i class="fa fa-trash"></i></button>

                  <?php if ($corTexto == "text-danger"): ?>
                    <button class="btn btn-xs btn-outline-danger" title="<?=$title?>" alt="<?=$title?>"><i class="fa-solid fa-land-mine-on"></i></button>
                  <?php else: ?>
                    <button class="btn btn-xs btn-outline-success" disabled><i class="fa-solid fa-circle-check"></i></button>
                  <?php endif ?>

                  <button type="submit" name="acao" value="exibeCotacoes" class="btn btn-xs btn-outline-info"><i class="fas fa-file-invoice"></i></button>

                  <button type="submit" name="acao" value="exibeVendas" class="btn btn-xs btn-outline-info"><i class="fa-solid fa-money-bill"></i></button>
                  
                  <button type="submit" name="acao" value="exibeCompras" class="btn btn-xs btn-outline-info"><i class="fa fa-shopping-cart"></i></button>

                  <button type="submit" name="acao" value="exibeInformacoes" class="btn btn-xs btn-outline-info"><i class="fa fa-info-circle"></i></button>
                  
                </div>
              </td>
            </form>
          </tr>
        <?php endforeach ?>

        </tbody>
      </table>
    </div>
  </div>
</div>
