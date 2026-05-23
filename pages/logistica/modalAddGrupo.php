<div class="modal fade" id="modalAddGrupo" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h5 class="modal-title" id="exampleModalLabel">
          <i class="fa-regular fa-plus"></i> Adicionar Grupo
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="op" value="<?=$dados['op']?>">
          <input type="hidden" name="aba" value="<?=$dados['aba']?>">
          
          <div class="input-group mb-3">
            <select name="CATEGORIAID" class="form-select select2">
              <option value="" selected>Selecione a Categoria</option>
              <?php  
              if ($categorias = listarCategorias()) {
                foreach ($categorias as $key => $value) {
                  echo '<option value="'.$value['CATEGORIAID'].'">'.$value['CATEGORIA'].'</option>';
                }
              }
              ?>
            </select>
          </div>
          
          <div class="input-group mb-3">
            <select name="SUBCATEGORIAID" class="form-select select2">
              <option value="" disabled selected>Selecione a Categoria</option>
            </select>
          </div>
          
          <div class="input-group mb-3">
            <input type="text" name="GRUPO" id="GRUPO" class="form-control" placeholder="Novo registro" aria-label="Novo Grupo" aria-describedby="button-addon2" required autocomplete="off">
            <button type="submit" name="acao" value="inserirGrupo" class="btn btn-primary">Salvar</button>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>