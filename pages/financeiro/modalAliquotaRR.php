<div class="modal fade" id="modalAliquotaRR" role="dialog" aria-labelledby="labelmodalAlteraSenha" aria-hidden="true" tabindex="-1">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="labelmodalAlteraSenha">
          <i class="fa-solid fa-plus"></i> Adicionar Cliente
        </h4>
      </div>

      <form role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="op" value="144">

        <div class="modal-body">
          <div class="col">
            <input type="text" name="CODCLI" class="form-control" id="inputEmail4" placeholder="CODCLI" autocomplete="off" required>
          </div>
        </div>

        <div class="modal-footer d-flex justify-content-between">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fa fa-ban"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-success" name="acao" value="AdicionarClienteAliquotaRR">
            <i class="fa-solid fa-plus"></i> Adicionar
          </button>
        </div>

      </form>

    </div>
  </div>
</div>