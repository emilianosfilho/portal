<div class="modal fade" id="modalQTETIQUETA" role="dialog" aria-labelledby="labelmodalAlteraSenha" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="labelmodalAlteraSenha">
          <i class="fas fa-tasks"></i> Alterar Qtd etiquetas do produto W<?=$dados['CODPROD'].'-'.$dados['DV']?>
        </h4>
      </div>

      <form role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="op" value="<?=$dados['op']?>">
        <input type="hidden" name="CODPROD" value="<?=$dados['CODPROD']?>">
        <input type="hidden" name="key" value="<?=$dados['key']?>">

        <div class="modal-body">
          <div class="mb-3 row">
            <label for="inputPassword" class="col-sm-4 col-form-label">Qtd Etiquetas</label>
            <div class="col-sm-8">
              <input type="number" name="QTETIQUETA" class="form-control" value="<?=$dados['QTETIQUETA']?>" required>
            </div>
          </div>
        </div>

        <div class="modal-footer d-flex justify-content-between">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fa fa-ban"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-success" name="acao" value="salvarQTETIQUETAProduto">
            <i class="fa fa-forward"></i> Avançar
          </button>
        </div>

      </form>

    </div>
  </div>
</div>