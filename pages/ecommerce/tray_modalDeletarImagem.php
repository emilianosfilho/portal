<div class="modal fade" id="tray_modalDeletarImagem" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h4 class="modal-title">
          <i class="fas fa-trash"></i> Exclusão de Imagem do Produto
        </h4>
      </div>

      <form role="form" class="STYLE-NAME" action="index.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="op" value="172">
        <input type="hidden" name="product_id" value="<?=$dados["product_id"]?>">
        <input type="hidden" name="posicao" value="<?=$dados["posicao"]?>">

        <div class="modal-body">
          <div class="row">
            <div class="col">
              <h5 class="text-danger m-0 p-0"><strong>Tem certeza de que deseja excluir esta imagem?</strong></h5>
            </div>
          </div>
        </div>

        <div class="modal-footer d-flex justify-content-between">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            Não
          </button>
          <button type="submit" class="btn btn-danger" name="acao" value="tray_deletarImagem">
            Sim
          </button>
        </div>

      </form>

    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready ( function() {
    $('#tray_modalDeletarImagem').modal('show');
  });
</script>
