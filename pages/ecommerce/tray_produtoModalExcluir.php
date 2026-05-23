<div class="modal fade" id="tray_produtoModalExcluir" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger">
        <h4 class="modal-title">
          <i class="fas fa-trash"></i> Excluir produto na Tray
        </h4>
      </div>

      <form role="form" method="POST" action="index.php" enctype="multipart/form-data">
        <input type="hidden" name="op" value="171">
        <input type="hidden" name="aba_produtos">

        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group p-3 py-1">
                <label>product_id</label>
                <input type="text" name="product_id" value="<?=$dados['product_id']?>" class="form-control" name="NUMORIGINAL" autocomplete="off" readonly>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer d-flex justify-content-between">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-danger" name="acao" value="tray_excluirProduto">
            <i class="fas fa-trash"></i> Excluir
          </button>
        </div>

      </form>

    </div>
  </div>
</div>

<script type="text/javascript">
    $(window).on('load', function() {
        $('#tray_produtoModalExcluir').modal('show');
    });
</script>
