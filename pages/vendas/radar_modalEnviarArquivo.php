<div class="modal fade" id="radar_modalEnviarArquivo" role="dialog" aria-labelledby="labelmodalAlteraSenha" aria-hidden="true" tabindex="-1">
  <div class="modal-lg modal-dialog text-md" role="document">
    <div class="modal-content">
      <div class="modal-header bg-secondary">
        <h4 class="modal-title">
          <i class="fa-solid fa-upload"></i> Enviar Arquivo
        </h4>
      </div>

      <form role="form" action="radar.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="NOMEARQUIVO" value="RADAR">

        <div class="modal-body text-dark">
          <div class="mb-3">
            <label for="arquivo" class="col-form-label">Selecione o arquivo:</label>
            <input type="file" name="arquivo" id="arquivo" class="form-control" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
          </div>
        </div>

        <div class="modal-footer d-flex justify-content-between">
          <button type="button" class="btn btn-secondary text-white" data-bs-dismiss="modal">
            <i class="fa fa-ban"></i> Cancelar
          </button>
          <a target="_blank" href="download/MODELO_RADAR.xlsx" class="btn btn-light border">
            <i class="fa fa-download"></i> Modelo
          </a>
          <button type="submit" class="btn btn-primary text-white" name="acao" value="enviarArquivo">
            <i class="fa fa-forward"></i> Avançar
          </button>
        </div>

      </form>

    </div>
  </div>
</div>