<div class="modal fade" id="tray_modalResetarImagem" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h4 class="modal-title">
          <i class="fas fa-retweet"></i> Resetar de Imagens do Produto
        </h4>
      </div>

      <form role="form" class="STYLE-NAME" action="index.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="op" value="172">
        <input type="hidden" name="product_id" value="<?=$dados["product_id"]?>">

        <div class="modal-body">
          <div class="row">
            <div class="col">
              <h5 class="text-danger m-0 p-0"><strong>Tem certeza de que deseja resetar as imagens para o padrão Vemap?</strong></h5>
            </div>
          </div>
        </div>

        <div class="modal-footer d-flex justify-content-between">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            Não
          </button>
          <button type="submit" class="btn btn-danger" name="acao" value="tray_resetarImagensProduto">
            Sim
          </button>
        </div>

      </form>

    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready ( function() {
    $('#tray_modalResetarImagem').modal('show');
  });
</script>
