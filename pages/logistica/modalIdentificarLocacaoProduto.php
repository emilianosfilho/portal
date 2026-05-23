<div class="modal fade" id="modalIdentificarLocacaoProduto" role="dialog" aria-labelledby="labelmodalAlteraSenha" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="labelmodalAlteraSenha">
          <i class="fas fa-tasks"></i> Definir Posição Primeira Etiqueta
        </h4>
      </div>

      <form role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="op" value="<?=$dados['op']?>">


        <div class="modal-body">
          <div class="mb-3 row">
            <label for="inputPassword" class="col-sm-4 col-form-label">Posição Inicial</label>
            <div class="col-sm-8">
              <input type="number" name="POSICAOINI" class="form-control" value="1" min="1" max="16">
            </div>
          </div>
        </div>

        <div class="modal-footer d-flex justify-content-between">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fa fa-ban"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-success" name="acao" value="<?=(($dados['op']=='131')?'gerarEtiquetasProduto':'gerarEtiquetasLocacao')?>">
            <i class="fa fa-forward"></i> Avançar
          </button>
        </div>

      </form>

    </div>
  </div>
</div>