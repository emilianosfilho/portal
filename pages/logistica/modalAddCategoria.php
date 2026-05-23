<div class="modal fade" id="modalAddCategoria" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h5 class="modal-title" id="exampleModalLabel">
          <i class="fa-regular fa-plus"></i> Adicionar Categoria
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="op" value="<?=$dados['op']?>">
          <input type="hidden" name="aba" value="<?=$dados['aba']?>">
          
          <div class="input-group mb-3">
            <input type="text" name="CATEGORIA" class="form-control" placeholder="Novo registro" aria-label="Novo registro" aria-describedby="button-addon2" required autocomplete="off">
            <button type="submit" name="acao" value="inserirCategoria" class="btn btn-primary">Salvar</button>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>