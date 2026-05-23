<form name="form-05" id="form-05"class="row g-3" action="pages/vendas/enviarArquivoPecas.php" method="POST" enctype="multipart/form-data">
  <input type="hidden" name="op" value="62">
  
  <div class="col-2">
    <br>
    <div class="d-grid gap-2">
      <a href="download/MODELO_ORC_PECA.xlsx" target="_blanck" class="btn btn-block btn-info"><i class="fa fa-file-download"></i> Baixar Modelo</a>
    </div>
  </div>

  <div class="col-2">
    <label for="basic-url" class="form-label"><b>Opções</b></label>
    <div class="d-grid gap-2">
      <select name="somenteDisponiveis" class="form-select" aria-label="Default select example">
        <option value="S" selected>Somente Disponíveis</option>
        <option value="N">Todos</option>
      </select>      
    </div>
  </div>

  <div class="col">
    <label for="basic-url" class="form-label"><b></b></label>
    <div class="input-group btn-group">
      <div class="input-group mb-3">
        <input type="file" class="form-control" name="filePecas" id="filePecas" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
      </div>
      <code>Somente arquivos xls ou xlsx serão aceitos.</code>
    </div>
  </div>
  <div class="col-2">
    <br>
    <div class="d-grid gap-2">
      <button type="submit" name="acao" value="enviarArquivoPecas" class="btn btn-primary" ><i class="fa fa-file-upload"></i> Enviar arquivo</button>
    </div>
  </div>

</form>