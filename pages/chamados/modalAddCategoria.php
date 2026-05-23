<div class="modal fade" id="modalAddCategoria" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h5 class="modal-title" id="exampleModalLabel"><i class="fa-solid fa-plus"></i> Adicionar Categoria</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form name="form-contato" role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="op" value="<?=$dados['op']?>">

        <div class="modal-body">
          <div class="mb-3">
            <input type="text" name="CATEGORIA" class="form-control" autocomplete="off" required placeholder="Nova categoria" maxlength="50">
          </div>
          <div class="mb-3">
            <input type="text" name="DESCRICAO" class="form-control" autocomplete="off" required placeholder="Observação sobre a categoria" maxlength="200">
          </div>

          <div class="mb-3 float-md-end">
            <button type="submit" name="acao" value="addCategoria" class="btn btn-primary">Adicionar</button>
          </div>
        </div>

      </form>

    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready ( function() {
    $('#modalAddCategoria').modal('show');
  });
</script>