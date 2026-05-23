<!-- Modal -->
<div class="modal fade" id="produtoModalEditar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h5 class="modal-title" id="exampleModalLabel"><i class="fa-solid fa-edit"></i> Editar Produto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form name="form-ordem" role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="op" value="<?=$dados['op']?>">
        <input type="hidden" name="acao" value="produtoSalvarEdicao">
        <input type="hidden" name="CODPROD" value="<?=$dados['CODPROD']?>">
        <div class="modal-body">
          <?php  
          $produto = buscaDadosPCPRODUT($dados['CODPROD']);
          ?>
          <div class="row">
            <div class="col-6">
              <div class="mb-3">
                <label>CODPROD</label>
                <input type="text" class="form-control" autocomplete="off" value="<?=$produto['CODPROD'].'-'.$produto['DV']?>" disabled>
              </div>
            </div>
            <div class="col-6">
              <div class="mb-3">
                <label>NUMORIGINAL</label>
                <input type="text" class="form-control" autocomplete="off" value="<?=$produto['NUMORIGINAL']?>" disabled>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <div class="mb-3">
                <label>DESCRIÇÃO</label>
                <input type="text" class="form-control" autocomplete="off" value="<?=$produto['DESCRICAO']?>">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <div class="mb-3">
                <label>MARCA</label>
                <input name="DESCRICAO" type="text" class="form-control" autocomplete="off" value="<?=$produto['DESCRICAO']?>">
              </div>
            </div>
          </div>


        </div>
        <div class="modal-footer">
          <div class="float-md-end">
            <button type="submit" class="btn btn-primary">Salvar</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

