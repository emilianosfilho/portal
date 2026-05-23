<div class="modal fade" id="modalEditarPrevisao" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h5 class="modal-title" id="exampleModalLabel"><i class="fa-solid fa-clock"></i> Editar Previsão</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form name="form-contato" role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="op" value="36">
        <input type="hidden" name="ID_CHAMADO" value="<?=$dados['ID_CHAMADO']?>">
        <div class="modal-body">
          <div class="col">
            <label class="form-label">Data Início</label>
            <div class="span5" id="sandbox-container">
              <div class="input-group date">
                <input name="DATA_PREVISAO" type="text" class="form-control" value="<?=@date('d/m/Y')?>">
                <span class="input-group-addon btn btn-primary"><i class="fa fa-th"></i></span>
              </div>         
            </div>
          </div>
          <br>
          <div class="mb-3 float-md-end">
            <button type="submit" name="acao" value="editarPrevisao" class="btn btn-primary">Adicionar</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready ( function() {
    $('#modalEditarPrevisao').modal('show');
  });
</script>