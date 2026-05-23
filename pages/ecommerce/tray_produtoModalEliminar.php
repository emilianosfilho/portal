<div class="modal fade" id="tray_produtoModalEliminar" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger">
        <h4 class="modal-title">
          <i class="fas fa-trash"></i> Eliminar Produtos
        </h4>
      </div>

      <form role="form" method="POST" action="index.php" enctype="multipart/form-data">
        <input type="hidden" name="op" value="171">
        <input type="hidden" name="aba_produtos">

        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <h3>Confirma a eliminação total de todos os produtos cadastrados na Tray?</h3>
              <p><b>ATENÇÃO!</b> Esta operação não poderá ser desfeita posteriormente.</p>
            </div>
          </div>
        </div>

        <div class="modal-footer d-flex justify-content-between">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            Cancelar
          </button>
          <button type="submit" class="btn btn-danger" name="acao" value="tray_produtoModalEliminar">
            Confirmar
          </button>
        </div>

      </form>

    </div>
  </div>
</div>