<div class="modal fade" id="modalRelatorioAtividades" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h5 class="modal-title" id="exampleModalLabel"><i class="fa-solid fa-clock"></i> Adicionar Histórico</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form name="form-contato" role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="op" value="36">
        <div class="modal-body">
          <div class="row mb-3">
            <div class="col">
              <label class="form-label">DATA INÍCIO</label>
              <div class="span5" id="sandbox-container">
                <div class="input-group date">
                  <input name="DATAINI" type="text" class="form-control" value="<?= $dados['DATAINI'] ?? @date('d/m/Y') ?>" autocomplete="off">
                  <span class="input-group-addon btn btn-secondary"><i class="fa fa-th"></i></span>
                </div>         
              </div> 
            </div>
            <div class="col">
              <label class="form-label">DATA FIM</label>
              <div class="span5" id="sandbox-container">
                <div class="input-group date">
                  <input name="DATAFIM" type="text" class="form-control" value="<?= $dados['DATAFIM'] ?? @date('d/m/Y')?>" autocomplete="off">
                  <span class="input-group-addon btn btn-secondary"><i class="fa fa-th"></i></span>
                </div>         
              </div> 
            </div>
            <div class="col">
              <div class="mb-3">
                <label class="form-label">Agrupado por:</label>
                <select class="form-select" name="GRUPO" id="GRUPO">
                  <option value="DATAHORA" selected>Horário</option>
                  <option value="CHAMADO">Chamado</option>
                </select>
              </div>
            </div>

          </div>
          <div class="mb-3 float-md-end">
            <button type="submit" name="acao" value="PDF_relatorioAtividades" class="btn btn-primary">Gerar</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready ( function() {
    $('#modalRelatorioAtividades').modal('show');
  });
</script>