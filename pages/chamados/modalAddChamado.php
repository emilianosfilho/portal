<div class="modal fade" id="modalAddChamado" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h5 class="modal-title" id="exampleModalLabel"><i class="fa-solid fa-plus"></i> Adicionar Chamado</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form name="form-contato" role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="op" value="<?=$dados['op']?>">

        <div class="modal-body row">

          <div class="col-4">
          <div class="mb-3">
            <label class="form-label">Tipo de Atendimento</label>
            <select class="form-select" name="ID_TIPO" id="ID_TIPO">
              <?php
              if($tipos = buscaTiposChamado()) {
                foreach ($tipos as $key => $value) {
                  echo '<option value="'.$value['ID_TIPO'].'" title="'.$value['DESCRICAO'].'">'.$value['TIPO'].'</option>';
                }
              }
              ?>
            </select>
          </div>
          </div>

          <div class="col-4">
          <div class="mb-3">
            <label class="form-label">Categoria</label>
            <select class="form-select" name="ID_CATEGORIA" id="ID_CATEGORIA">
              <?php
              if($categorias = buscaVmpCategorias()) {
                foreach ($categorias as $key => $value) {
                  echo '<option value="'.$value['ID_CATEGORIA'].'" title="'.$value['DESCRICAO'].'">'.$value['CATEGORIA'].'</option>';
                }
              }
              ?>
            </select>
          </div>
          </div>

          <div class="col-4">
          <div class="mb-3">
            <label class="form-label">Usuário</label>
            <select class="form-select" name="ID_USUARIO" id="ID_USUARIO">
              <?php
              if($usuarios = buscaUsuariosChamado()) {
                foreach ($usuarios as $key => $value) {
                  echo '<option value="'.$value['IDUSUARIO'].'">'.$value['NOME'].'</option>';
                }
              }
              ?>
            </select>
          </div>
          </div>

          <div class="col-12">
          <div class="mb-3">
            <label class="form-label">Título</label>
            <input type="text" name="TITULO" class="form-control" autocomplete="off" required placeholder="descrição breve e objetiva do conteúdo principal do chamado." maxlength="200">
          </div>
          </div>
          
          <div class="mb-3">
            <button type="submit" name="acao" value="addChamado" class="btn btn-primary float-md-end">Adicionar</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready ( function() {
    $('#modalAddChamado').modal('show');
  });
</script>
    