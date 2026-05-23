<form name="form-02" id="form-02"class="row g-3" action="index.php" method="POST">
  <input type="hidden" name="op" value="62">

  <div class="row">

    <div class="col-3">
      <div class="row mt-4">
        <div class="col-4">
          <label for="basic-url" class="form-label"><b>QTD</b></label>
          <div class="input-group">
            <input type="number" name="QTPEDIDA" class="form-control" placeholder="Qtd" required>
          </div>
        </div>
        <div class="col-8">
          <label for="basic-url" class="form-label"><b>CODPECA</b></label>
          <div class="input-group">
            <input type="text" name="CODPECA" class="form-control active" value="" placeholder="Código da Peça" required autofocus autocomplete="off">
            <button class="btn btn-outline-secondary " type="submit" name="acao" value="consultaPeca">
              <i class="fa-solid fa-search"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
      
      
    <div class="col-9">
      <div class="row mt-4">
        <div class="col">
          <label for="basic-url" class="form-label"><b>ATENDIMENTO</b></label>
          <div class="input-group">
            <input type="text" class="form-control" value="<?=$_SESSION['ORCAMENTO']['CAB']["ATENDIMENTO"]?>" disabled>
            <button class="btn btn-outline-info" type="button" data-bs-toggle="modal" data-bs-target="#modalAtendimento">
              <i class="fa-solid fa-arrows-rotate"></i>
            </button>
          </div>
        </div>
        <div class="col">
          <label for="basic-url" class="form-label"><b>CONTATO</b></label>
          <div class="input-group">
            <input type="text" class="form-control" value="<?= $_SESSION['ORCAMENTO']['CAB']['CONTATO'].' '.$_SESSION['ORCAMENTO']['CAB']['TELCELULAR'].' '.$_SESSION['ORCAMENTO']['CAB']['TELFIXO'] ?>" disabled>
            <button class="btn btn-outline-info" type="button" data-bs-toggle="modal" data-bs-target="#modalContato">
              <i class="fa-solid fa-arrows-rotate"></i>
            </button>
          </div>
        </div>
        <div class="col">
          <label for="basic-url" class="form-label"><b>MÁQUINA</b></label>
          <div class="input-group">
            <input type="text" class="form-control" value="<?= buscaEquipamento($_SESSION['ORCAMENTO']['CAB']['MAQUINA']) ?>" disabled>
            <button class="btn btn-outline-info" type="button" data-bs-toggle="modal" data-bs-target="#modalMaquina">
              <i class="fa-solid fa-arrows-rotate"></i>
            </button>
          </div>
        </div>
        <div class="col">
          <label for="basic-url" class="form-label"><b>OBS GERAIS</b></label>
          <div class="input-group">
            <input type="text" class="form-control" value="<?= $_SESSION['ORCAMENTO']['CAB']['OBSGERAIS'] ?>" disabled>
            <button class="btn btn-outline-info" type="button" data-bs-toggle="modal" data-bs-target="#modalObsGerais">
              <i class="fa-solid fa-arrows-rotate"></i>
            </button>
          </div>
        </div>
        <div class="col">
          <label for="basic-url" class="form-label"><b>ORDEM DE COMPRA</b></label>
          <div class="input-group">
            <input type="text" class="form-control" value="<?= $_SESSION['ORCAMENTO']['CAB']["ORDEMDECOMPRA"] ?>" disabled>
            <button class="btn btn-outline-info" type="button" data-bs-toggle="modal" data-bs-target="#modalOrdemCompra">
              <i class="fa-solid fa-arrows-rotate"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

</form>