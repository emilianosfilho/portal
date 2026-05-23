<div class="modal fade" id="modalEditarInventario" role="dialog" aria-labelledby="" aria-hidden="true">
  <div class="modal-dialog text-md" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h4 class="modal-title">
          <i class="fa-solid fa-edit"></i> Editar Conferência Inventário
        </h4>
      </div>

      <form role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="op" value="<?=$dados['op']?>">
        <input type="hidden" name="IDINVENTARIO" value="<?=$dados['IDINVENTARIO']?>">
        <input type="hidden" name="CODPROD" value="<?=$dados['CODPROD']?>">
        <input type="hidden" name="LOCACAO" value="<?=$dados['LOCACAO']?>">

        <div class="modal-body">
          <div class="my-1 row">
            <label class="col-sm-4 col-form-label">Codprod</label>
            <div class="col-sm-8">
              <input type="text" readonly disabled class="form-control-plaintext" value="<?='W'.$dados['CODPROD'].'-'.$dados['DV']?>">
            </div>
          </div>
          <div class="my-1 row">
            <label class="col-sm-4 col-form-label">Descrição</label>
            <div class="col-sm-8">
              <input type="text" readonly disabled class="form-control-plaintext" value="<?=$dados['DESCRICAO']?>">
            </div>
          </div>
          <div class="my-1 row">
            <label class="col-sm-4 col-form-label">Marca</label>
            <div class="col-sm-8">
              <input type="text" readonly disabled class="form-control-plaintext" value="<?=$dados['MARCA']?>">
            </div>
          </div>
          <div class="my-1 row">
            <label class="col-sm-4 col-form-label">Locação</label>
            <div class="col-sm-8">
              <input type="text" readonly disabled class="form-control-plaintext" value="<?=$dados['LOCACAO']?>">
            </div>
          </div>
          <div class="my-1 row">
            <label class="col-sm-4 col-form-label">Qt Conferida</label>
            <div class="col-sm-8">
              <input type="text" name="QTCONFERIDA" class="form-control" value="<?=$dados['QTCONFERIDA']?>" autocomplete="off">
            </div>
          </div>

          <div class="modal-footer d-flex justify-content-between">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              <i class="fa fa-ban"></i> Cancelar
            </button>
            <button type="submit" class="btn btn-success" name="acao" value="atualizaQtconferidaInventario">
              <i class="fa fa-forward"></i> Avançar
            </button>
          </div>

        </div>
      </form>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function () {
    $('#modalEditarInventario').modal('show');
  });
</script>