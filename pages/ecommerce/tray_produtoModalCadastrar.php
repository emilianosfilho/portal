<div class="modal fade" id="tray_produtoModalCadastrar" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <h4 class="modal-title">
          <i class="fas fa-plus"></i> Cadastrar produto na Tray
        </h4>
      </div>

      <form role="form" method="POST" action="index.php" enctype="multipart/form-data">
        <input type="hidden" name="op" value="171">
        <input type="hidden" name="aba_produtos">

        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group p-3 py-1">
                <label>Referência (Numoriginal):</label>
                <input type="text" class="form-control" name="NUMORIGINAL" autocomplete="off">
              </div>
            </div>
            <div class="col-md-12">
              <div class="mb-3">
                <label for="formFile" class="form-label">Selecione o Arquivo:</label>
                <input class="form-control" type="file" id="arquivo" name="arquivo" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer d-flex justify-content-between">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            Cancelar
          </button>
          <button type="submit" class="btn btn-primary" name="acao" value="tray_produtoCadastrar">
            Cadastrar
          </button>
        </div>

      </form>

    </div>
  </div>
</div>