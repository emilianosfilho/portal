<div class="modal fade" id="tray_modalConfirmarCancelamento" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-warning text-black">
        <h4 class="modal-title">
          <i class="fas fa-ban"></i> Confirmar Sair sem salvar
        </h4>
      </div>

      <form role="form" method="POST" action="index.php" enctype="multipart/form-data">
        <input type="hidden" name="op" value="172">
        <input type="hidden" name="product_id" value="<?=$dados["product_id"]?>">
        <input type="hidden" name="aba_produtos">

        <div class="modal-body">
          <div class="row">
            <h4>Você deseja sair sem salvar as alterações do Produto?</h4>
          </div>
        </div>

        <div class="modal-footer d-flex justify-content-between">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            Não
          </button>
          <button type="submit" class="btn btn-warning" name="acao" value="tray_cancelarEdicao">
            Sim
          </button>
        </div>

      </form>

    </div>
  </div>
</div>
<script type="text/javascript">
  $(document).ready ( function() {
    $('#tray_modalConfirmarCancelamento').modal('show');
  });
</script>