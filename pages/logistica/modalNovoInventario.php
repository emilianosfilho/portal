<div class="modal fade" id="modalNovoInventario" role="dialog" aria-labelledby="" aria-hidden="true">
  <div class="modal-dialog text-md" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h4 class="modal-title">
          <i class="fa-solid fa-circle-plus"></i> Abrir novo Inventário
        </h4>
      </div>

      <form role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="op" value="<?=$dados['op']?>">

        <div class="modal-body">

          <div class="my-1 row">
            <label class="col-sm-4 col-form-label">Locação</label>
            <div class="col-sm-8">
              <input name="LOCACAO" type="text" class="form-control" required autocomplete="off">
            </div>
          </div>

          <div class="modal-footer d-flex justify-content-between">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              <i class="fa fa-ban"></i> Cancelar
            </button>
            <button type="submit" class="btn btn-success" name="acao" value="abrirNovoInventario">
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
    $('#modalNovoInventario').modal('show');
  });
</script>