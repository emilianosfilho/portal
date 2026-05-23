<div class="modal fade" id="modalNovoVide" role="dialog" aria-labelledby="labelmodalAlteraSenha" aria-hidden="true">
  <div class="modal-dialog text-md" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h4 class="modal-title" id="labelmodalAlteraSenha">
          <i class="fa-solid fa-plus"></i> Novo Cadastro Vide
        </h4>
      </div>
      <form role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="acao" value="salvarNovoVide">
        <input type="hidden" name="op" value="<?=$dados['op']?>">

        <div class="modal-body">
          <div class="row">
            <div class="col-6">
              <label class="col-form-label">Cod Peça:</label>
              <input type="text" name="CODPECA" class="form-control" required>
            </div>
            <div class="col-6">
              <label class="col-form-label">Cod Vide:</label>
              <input type="text" name="VIDE" class="form-control">
            </div>
            <div class="col-6">
              <label class="col-form-label">Aplic / Marca:</label>
              <input type="text" name="APLICMARCA" class="form-control">
            </div>
            <div class="col-6">
              <label class="col-form-label">Descrição:</label>
              <input type="text" name="DESCRICAO" class="form-control" required>
            </div>
            <div class="col-6">
              <label class="col-form-label">Marca:</label>
              <input type="text" name="MARCA" class="form-control">
            </div>
            <div class="col-6">
              <label class="col-form-label">Preço NPR:</label>
              <input type="text" name="PRECO" class="form-control">
            </div>
            <div class="col-6">
              <label class="col-form-label">Nome Opção:</label>
              <input type="text" name="NOMEOPCAO" class="form-control">
            </div>
            <div class="col-6">
              <label class="col-form-label">Produto Importado:</label>
              <div class="form-check form-switch">
                <input type="checkbox" name="IMPORTADO" class="form-check-input" role="switch" id="flexSwitchCheckChecked" <?=(($vide['IMPORTADO']=='S')?'checked':'')?>>
              </div>
            </div>
          </div>

        </div>

        <div class="modal-footer d-flex justify-content-between">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fa fa-ban"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-success">
            <i class="fa fa-forward"></i> Avançar
          </button>
        </div>

      </form>

    </div>
  </div>
</div>