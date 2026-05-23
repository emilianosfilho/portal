<div class="modal fade" id="vide-modalEnviarArquivo" role="dialog" aria-labelledby="labelmodalAlteraSenha" aria-hidden="true">
  <div class="modal-dialog text-md" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <h4 class="modal-title" id="labelmodalAlteraSenha">
          <i class="fa-solid fa-upload"></i> Enviar Arquivo VIDE
        </h4>
      </div>
      <form role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="op" value="41">
        <input type="hidden" name="nav" value="logistica">
        <input type="hidden" name="aba" value="vide">
        <input type="hidden" name="NOMEARQUIVO" value="VIDE">

        <div class="modal-body">
          <div class="row">
            <div class="col-12 mb-3">
              <label class="col-form-label">Selecione o arquivo:</label>
                <div class="input-group btn-group">
                  <div class="input-group">
                    <input type="file" name="arquivo" class="form-control" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                  </div>
                  <code>Somente arquivos xls ou xlsx serão aceitos.</code>
                </div>
            </div>
          </div>

          <a href="download/MODELO_VIDE.xlsx" class="btn btn-light" style="position: relative;">
            <i class="fa-solid fa-download"></i> Baixar arquivo modelo
          </a>

        </div>

        <div class="modal-footer d-flex justify-content-between">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fa fa-ban"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-primary" name="acao" value="enviarArquivo">
            <i class="fa fa-forward"></i> Avançar
          </button>
        </div>

      </form>

    </div>
  </div>
</div>