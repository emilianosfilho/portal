<div class="container-fluid mt-5">
  <div class="row">
    <?php 
    require_once('pages/cliente/function.php'); 
    require_once('pages/cliente/controller.php'); 
    require_once('pages/cliente/sidebar.php'); 
    ?>

    <main class="col-11 ms-sm-auto px-3 mt-3">

      <div class="card w-100 shadow">
        <div class="card-header">
          <h2 class="">
            <div class="float-start">
              <i class="fa-solid fa-file"></i> Orçamento 
            </div>
            <div class="float-end">
              #<?=$_SESSION['ORC_CLIENTE']['CAB']['IDORCAMENTO']?>
            </div>
          </h2>
        </div>

        <div class="card-body">

          <div class="row">
            <div class="col">
              <div class="bd-example">
                <address class="mb-0">
                  <strong>CLIENTE</strong><br>
                  <?=$_SESSION['ORC_CLIENTE']['CLIENTE']['CODCLI'].' - '.$_SESSION['ORC_CLIENTE']['CLIENTE']['CLIENTE']?><br>
                  <?=$_SESSION['ORC_CLIENTE']['CLIENTE']['CGCENT']?><br>
                  <?=$_SESSION['ORC_CLIENTE']['CLIENTE']['MUNICIPIO'].' / '.$_SESSION['ORC_CLIENTE']['CLIENTE']['UF']?><br>
                </address>
              </div>
            </div>

            <div class="col">
              <div class="bd-example">
                <address class="mb-0">
                  <strong>VENDEDOR</strong><br>
                  <?=$_SESSION['ORC_CLIENTE']['VENDEDOR']['CODUSUR'].' - '.$_SESSION['ORC_CLIENTE']['VENDEDOR']['NOME']?><br>
                </address>
              </div>
            </div>

            <div class="col"></div>

            <div class="col-3">
              <div class="bd-example">
                <address class="mb-0">
                  <strong>CONTATOS</strong><br>
                  <a target="_blanck" class="badge rounded-pill bg-primary link-light" href="mailto:atendimento@vemap.com.br&subject=Portal%20VEMAP%20-%20Cotação%20<?=$_SESSION['ORC_CLIENTE']['CAB']['IDORCAMENTO']?>">atendimento@vemap.com.br</a>
                  <a target="_blanck" class="badge rounded-pill bg-success mt-1 link-light" href="https://wa.me/559236516000?text=Portal%20VEMAP%20-%20Cotação%20<?=$_SESSION['ORC_CLIENTE']['CAB']['IDORCAMENTO']?>">Whatsapp: (92) 3651-6000</a><br>
                </address>
              </div>
            </div>

          </div>

          <div class="row">
            <form name="form-02" id="form-02" class="row" action="index.php" method="POST">
              <input type="hidden" name="op" value="151">
              <input type="hidden" name="IDORCAMENTO" value="<?=$_SESSION['ORC_CLIENTE']['CAB']['IDORCAMENTO']?>">
              
              <div class="col-1">
                <label for="basic-url" class="form-label"><b>QTD</b></label>
                <div class="input-group">
                  <input type="number" name="QTPEDIDA" value="1" class="form-control" placeholder="Qtd" required>
                </div>
              </div>
              <div class="col-3">
                <label for="basic-url" class="form-label"><b>CODPECA</b></label>
                <div class="input-group">
                  <input type="text" name="CODPECA" class="form-control" value="" placeholder="Código da Peça" required autofocus>
                  <button class="btn btn-outline-primary" type="submit" name="acao" value="consultaPeca" onautocomplete="off">
                    <i class="fa-solid fa-search"></i>
                  </button>
                </div>
              </div>

              <div class="col"></div>

              <div class="col-3">
                <div class="btn-group mt-2">
                  <a href="#" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#modalObservacao" title="Observações do orçamento">
                    <i class="fa-solid fa-edit"></i> Observações
                  </a>
                  <a href="index.php?op=151&IDORCAMENTO=<?=$_SESSION['ORC_CLIENTE']['CAB']['IDORCAMENTO']?>&acao=exportarPDF" class="btn btn-sm btn-outline-secondary" title="Imprimir orçamento">
                    <i class="fa-solid fa-print"></i> Imprimir
                  </a>
                  <a href="index.php?op=151&IDORCAMENTO=<?=$_SESSION['ORC_CLIENTE']['CAB']['IDORCAMENTO']?>&acao=faturar" class="btn btn-sm btn-outline-success" title="Enviar o orçamento para faturamento">
                    <i class="fa-solid fa-cart-shopping"></i> Faturar
                  </a>
                </div>
              </div>
            </form>
          </div>

          <div class="row mt-4">
            <div class="col-12">
              <div class="table-responsive">
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
                          <input type="number" class="<?=$corTexto?>" name="QTPEDIDA_NEW" value="<?= $value['QTPEDIDA'] ?>" autocomplete="off" style="text-align:right;width: 60px;">
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
              </div>
            </div>
          </div>

        </div>

      </div>

    </main>
  </div><!-- row -->
</div><!-- container-fluid -->