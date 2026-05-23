<div class="modal fade" id="tray_addImagemProduto" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h4 class="modal-title">
          <i class="fas fa-plus"></i> Adicionar Imagem ao Produto
        </h4>
      </div>

      <form role="form" class="STYLE-NAME" action="index.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="op" value="172">
        <input type="hidden" name="product_id" value="<?=$dados["product_id"]?>">

        <div class="modal-body">
          <div class="row">
            <div class="mb-3">
              <label for="arquivos" class="form-label">Selecione a imagem que deseja enviar</label>
              <input class="form-control" type="file" id="arquivos" name="arquivos[]" accept=".jpg, .jpeg, .png" required multiple/>
            </div>
          </div>
          <div class="row">
            <div class="col">
              <p class="text-danger m-0 p-0"><strong>Regras de envio:</strong></p>
              <ul>
                <li>Quantidade máxima de imagens: Até 15 imagens;</li>
                <li>Formatos: JPG, JPEG e PNG;</li>
                <li>Dimensão: Limite máximo de 2000 x 2000 pixels;</li>
                <li>Tamanho: Até 350 Kb;</li>
                <li>A imagem não poderá possuir uma extensão renomeada;</li>
              </ul>
            </div>
          </div>
        </div>

        <div class="modal-footer d-flex justify-content-between">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            Cancelar
          </button>
          <button type="submit" class="btn btn-primary" name="acao" value="tray_adicionarImagemProduto">
            Enviar
          </button>
        </div>

      </form>

    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready ( function() {
    $('#tray_addImagemProduto').modal('show');
  });
</script>