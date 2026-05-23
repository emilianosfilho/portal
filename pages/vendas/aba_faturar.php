<form name="form-04" id="form-04"class="row g-3" action="index.php" method="POST">
  <input type="hidden" name="op" value="62">
  <div class="col">
    <label for="basic-url" class="form-label"><b>COBRANÇA</b></label>
    <div class="input-group">
      <input type="text" class="form-control" value="<?= $_SESSION['ORCAMENTO']['COBRANCA']['CODCOB'].'- '.$_SESSION['ORCAMENTO']['COBRANCA']['COBRANCA'] ?>" disabled>
      <button class="btn btn-outline-info" type="button" data-bs-toggle="modal" data-bs-target="#modalCobrança">
        <i class="fa-solid fa-arrows-rotate"></i>
      </button>
    </div>
  </div>
  <div class="col">
    <label for="basic-url" class="form-label"><b>PLANO PAGAMENTO</b></label>
    <div class="input-group">
      <input type="text" class="form-control" value="<?= $_SESSION['ORCAMENTO']['PLPAG']['DESCRICAO']." [VM ".moeda($_SESSION['ORCAMENTO']['PLPAG']['VLMINPEDIDO'])."]" ?>" disabled>
      <button class="btn btn-outline-info" type="button" data-bs-toggle="modal" data-bs-target="#modalPlPag">
        <i class="fa-solid fa-arrows-rotate"></i>
      </button>
    </div>
  </div>
  <div class="col">
    <label for="basic-url" class="form-label"><b>Frete / Obsercações de Entrega</b></label>
    <div class="input-group">
      <input type="text" class="form-control" value="<?= 'R$ '.moeda($_SESSION['ORCAMENTO']['CAB']['VALORFRETE']).' '.decodeFreteDespacho($_SESSION['ORCAMENTO']['CAB']['FRETEDESPACHO']) ?>" disabled>
      <button class="btn btn-outline-info" type="button" data-bs-toggle="modal" data-bs-target="#modalFrete">
        <i class="fa-solid fa-arrows-rotate"></i>
      </button>
    </div>
  </div>
  <div class="col">
    <label for="basic-url" class="form-label"><b>Cliente no balcão?</b></label>
    <div class="input-group">
      <input type="text" class="form-control" value="<?= decodeSN($_SESSION['ORCAMENTO']['CAB']['CLIENTEBALCAO']) ?>" disabled>
      <button class="btn btn-outline-info" type="button" data-bs-toggle="modal" data-bs-target="#modalClienteBalcao">
        <i class="fa-solid fa-arrows-rotate"></i>
      </button>
    </div>
  </div>
  <div class="col">
    <label for="basic-url" class="form-label"><b>Separar pedido?</b></label>
    <div class="input-group">
      <input type="text" class="form-control" value="<?= decodeSN($_SESSION['ORCAMENTO']['CAB']['SEPARARPEDIDO']) ?>" disabled>
      <button class="btn btn-outline-info" type="button" data-bs-toggle="modal" data-bs-target="#modalSepararPedido">
        <i class="fa-solid fa-arrows-rotate"></i>
      </button>
    </div>
  </div>
  <div class="col">
    <label for="basic-url" class="form-label"><b>Exibir Cod. Peça (etiqueta)</b></label>
    <div class="input-group">
      <input type="text" class="form-control" value="<?= (($_SESSION['ORCAMENTO']['CAB']['EXIBIRCODPECAETIQUETA']=="N")?"NAO":"SIM") ?>" disabled>
      <button class="btn btn-outline-info" type="button" data-bs-toggle="modal" data-bs-target="#modalExibirCodpeca">
        <i class="fa-solid fa-arrows-rotate"></i>
      </button>
    </div>
  </div>
  <div class="col-md-2 d-grid gap-2">
    <button class="mt-4 btn btn-success" type="button" data-bs-toggle="modal" data-bs-target="#modalConfirmarFaturarOrcamento">
      Faturar Orçamento
    </button>
  </div>
</form>
