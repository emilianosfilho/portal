<form name="form-03" id="form-03"class="row g-3" action="index.php" method="POST">
  <input type="hidden" name="op" value="62">
  <input type="hidden" name="IDORCAMENTO" value="<?=$_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO']?>">
  <input type="hidden" name="titulo" value="ORCAMENTO_<?=$_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO']?>">
    
  <div class="col-lg-8">

    <div class="row">

      <div class="col-md-4">
        <div class="form-check form-switch">
           <input class="form-check-input flexSwitchCheckDefault" type="checkbox" name="incluirCodprod" role="switch" checked>
          <label class="form-check-label" for="flexSwitchCheckDefault">CODPROD (Cód Winthor)</label>
        </div>
        <div class="form-check form-switch">
           <input class="form-check-input flexSwitchCheckDefault" type="checkbox" name="incluirCodpeca" role="switch">
          <label class="form-check-label" for="flexSwitchCheckDefault">CODPECA (Núm. Original)</label>
        </div>
      </div>
      
      <div class="col-md-2">
        <div class="form-check form-switch">
           <input class="form-check-input flexSwitchCheckDefault" type="checkbox" name="incluirNCM" role="switch">
          <label class="form-check-label" for="flexSwitchCheckDefault">NCM</label>
        </div>
        <div class="form-check form-switch">
           <input class="form-check-input flexSwitchCheckDefault" type="checkbox" name="incluirCST" role="switch">
          <label class="form-check-label" for="flexSwitchCheckDefault">CST</label>
        </div>
      </div>
      
      <div class="col-md-2">
        <div class="form-check form-switch">
           <input class="form-check-input flexSwitchCheckDefault" type="checkbox" name="incluirLocacao" role="switch">
          <label class="form-check-label" for="flexSwitchCheckDefault">LOCAÇÃO</label>
        </div>
      </div>
      
      <div class="col-md-3">
        <div class="form-check form-switch">
           <input class="form-check-input flexSwitchCheckDefault" type="checkbox" name="somenteDisponiveis" role="switch">
          <label class="form-check-label text-lg" for="flexSwitchCheckDefault"><code>Somente Disponíveis</code></label>
        </div>
      </div>

    </div>
  
  </div>

  <div class="col-lg-4">
    <div class="mt-4 input-group btn-group">
      <button class="btn btn-outline-secondary" type="submit" name="acao" value="exportarExcel">
        Exportar Excel
      </button>
      <button class="btn btn-outline-secondary" type="submit" name="acao" value="exportarPDF">
        Exportar PDF
      </button>
      <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#modalDuplicarOrcamento">
        Duplicar
      </button>
    </div>
  </div>

</form>