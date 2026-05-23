<div class="modal fade" id="vide-modalCadastrar" role="dialog" aria-hidden="true">
  <div class="modal-dialog text-md" role="document">
    <div class="modal-content">
      <div class="modal-header bg-success">
        <h4 class="modal-title">
          <i class="fa-solid fa-plus"></i> Cadastrar novo VIDE
        </h4>
      </div>

      <form role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="op" value="40">
        <input type="hidden" name="nav" value="logistica">
        <input type="hidden" name="aba" value="vide">

        <div class="modal-body">
          <div class="row">
            <div class="col-6">
              <label class="col-form-label">Cod Peça:</label>
              <input type="text" name="CODPECA" class="form-control" autocomplete="off" required>
            </div>
            <div class="col-6">
              <label class="col-form-label">Cod Vide:</label>
              <input type="text" name="VIDE" class="form-control">
            </div>
            <div class="col-6">
              <label class="col-form-label">Aplic / Marca:</label>
              <input type="text" name="APLICMARCA" class="form-control" autocomplete="off">
            </div>
            <div class="col-6">
              <label class="col-form-label">Descrição:</label>
              <input type="text" name="DESCRICAO" class="form-control" autocomplete="off" required>
            </div>
            <div class="col-6">
              <label class="col-form-label">Opção:</label>
              <select class="form-select" name="NOMEOPCAO" aria-label="Default select example">
                <option value="VIDE">VIDE</option>
                <option value="NPR">NPR</option>
                <option value="INFO">INFO</option>
                <option value="OPCAO">OPCAO</option>
              </select>
            </div>
            <div class="col-6">
              <label class="col-form-label">Produto Importado:</label>
              <select class="form-select" name="IMPORTADO" aria-label="Default select example">
                <option value="N">NÃO</option>
                <option value="S">SIM</option>
              </select>
            </div>
            <div class="col-6">
              <label class="col-form-label">Preço NPR:</label>
              <input type="text" name="PRECO" class="form-control" autocomplete="off">
            </div>
            <div class="col-6">
              <label class="col-form-label">Marca:</label>
              <input type="text" name="MARCA" class="form-control" autocomplete="off">
            </div>
          </div>

        </div>

        <div class="modal-footer d-flex justify-content-between">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fa fa-ban"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-success" name="acao" value="vide_cadastrar">
            <i class="fa fa-forward"></i> Avançar
          </button>
        </div>

      </form>

    </div>
  </div>
</div>