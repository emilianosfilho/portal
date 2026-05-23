<main>
  <div class="container">
    <?php  
    $start_time = microtime(true);
    require_once("pages/vendas/function.php");
    require_once("pages/vendas/controller.php");
    require_once("pages/vendas/modalPlpag.php");
    require_once("pages/vendas/modalFrete.php");
    require_once("pages/vendas/modalContato.php");
    require_once("pages/vendas/modalMaquina.php");
    require_once("pages/vendas/modalCobranca.php");
    require_once("pages/vendas/modalObsGerais.php");
    require_once("pages/vendas/modalOrdemCompra.php");
    require_once("pages/vendas/modalAtendimento.php");
    require_once("pages/vendas/modalClienteBalcao.php");
    require_once("pages/vendas/modalSepararPedido.php");
    require_once("pages/vendas/modalExibirCodpeca.php");
    require_once("pages/vendas/modalDuplicarOrcamento.php");
    require_once("pages/vendas/modalConfirmarFaturarOrcamento.php");
    // varDump2($_SESSION['ORCAMENTO']);
    ?>
    <br>
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <form name="form-01" id="form-01" class="row g-3" action="index.php" method="POST">
            <input type="hidden" name="op" value="62">
            <div class="col-md-3">
              <label for="basic-url" class="form-label"><b>CLIENTE</b></label>
              <input type="text" class="form-control" value="<?=$_SESSION['ORCAMENTO']['CLIENTE']['CODCLI']."-".$_SESSION['ORCAMENTO']['CLIENTE']['CLIENTE']?>" disabled>
            </div>
            <div class="col">
              <label for="basic-url" class="form-label"><b>UF</b></label>
              <input type="text" class="form-control" value="<?=$_SESSION['ORCAMENTO']['CLIENTE']['UF']?>" disabled>
            </div>
            <div class="col-md-2">
              <label for="basic-url" class="form-label"><b>VENDEDOR</b></label>
              <input type="text" class="form-control" value="<?=$_SESSION['ORCAMENTO']['VENDEDOR']['CODUSUR']."-".$_SESSION['ORCAMENTO']['VENDEDOR']['NOME']?>" disabled>
            </div>
            <div class="col-md-2">
              <label for="basic-url" class="form-label"><b>FILIAL</b></label>
              <input type="text" class="form-control" value="<?=$_SESSION['ORCAMENTO']['FILIAL']['CODFILIALNF']."-".$_SESSION['ORCAMENTO']['FILIAL']['FANTASIA']?>" disabled>
            </div>
            <div class="col-md-2">
              <label for="basic-url" class="form-label"><b>VALOR TOTAL</b></label>
              <div class="input-group">
                <div class="form-control bg-teal text-sm-center fw-bolder text-xxl p-0">
                  <?= moeda(buscaValorTotal($_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO']),2) ?>
                </div>
              </div>
            </div>
            <div class="col-md-2">
              <label for="basic-url" class="form-label"><b>NR ORCAMENTO</b></label>
              <div class="input-group">
                <div class="form-control bg-warning text-sm-center fw-bolder text-xxl p-0">
                  <?=$_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO']?>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>


    <div class="col-md-12">
      <div class="card">
        <div class="card-body">

          <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link <?= ($active_geral)?' active':'' ?>" id="geral-tab" data-bs-toggle="tab" data-bs-target="#geral" type="button" role="tab" aria-controls="home" aria-selected="true">Geral</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link <?= ($active_importar)?' active':'' ?>" id="importar-tab" data-bs-toggle="tab" data-bs-target="#importar" type="button" role="tab" aria-controls="profile" aria-selected="false">Importar</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link <?= ($active_exportar)?' active':'' ?>" id="exportar-tab" data-bs-toggle="tab" data-bs-target="#exportar" type="button" role="tab" aria-controls="profile" aria-selected="false">Exportar</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link <?= ($active_faturamento)?' active':'' ?>" id="faturar-tab" data-bs-toggle="tab" data-bs-target="#faturar" type="button" role="tab" aria-controls="messages" aria-selected="false">Faturamento</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link <?= ($active_pedidos)?' active':'' ?>" id="pedidos-tab" data-bs-toggle="tab" data-bs-target="#pedidos" type="button" role="tab" aria-controls="messages" aria-selected="false">Pedidos</button>
            </li>
          </ul>

          <div class="tab-content">
            <div class="tab-pane <?= ($active_geral)?' active':'' ?>" id="geral" role="tabpanel" aria-labelledby="geral-tab">
              <?php require_once("pages/vendas/aba_geral.php"); ?>
            </div>
            <div class="tab-pane <?= ($active_importar)?' active':'' ?>" id="importar" role="tabpanel" aria-labelledby="importar-tab">
              <?php require_once("pages/vendas/aba_importar.php"); ?>
            </div>
            <div class="tab-pane <?= ($active_exportar)?' active':'' ?>" id="exportar" role="tabpanel" aria-labelledby="exportar-tab">
              <?php require_once("pages/vendas/aba_exportar.php"); ?>
            </div>
            <div class="tab-pane <?= ($active_faturamento)?' active':'' ?>" id="faturar" role="tabpanel" aria-labelledby="faturar-tab">
              <?php require_once("pages/vendas/aba_faturar.php"); ?>
            </div>
            <div class="tab-pane <?= ($active_pedidos)?' active':'' ?>" id="pedidos" role="tabpanel" aria-labelledby="pedidos-tab">
              <?php require_once("pages/vendas/aba_pedidos.php"); ?>
            </div>
          </div>

        </div>
      </div>
    </div>

    
    <?php 
    require_once("pages/vendas/listaItensColaborador.php");
    ?>

    
  </div>
</main>