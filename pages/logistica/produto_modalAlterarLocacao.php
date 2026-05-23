<div class="modal fade" id="produto_modalAlterarLocacao" role="dialog" aria-labelledby="labelmodalAlteraSenha" aria-hidden="true">
  <div class="modal-dialog text-md" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h4 class="modal-title" id="labelmodalAlteraSenha">
          <i class="fa-solid fa-map-location-dot"></i> Alterar Locação do produto
        </h4>
      </div>

      <form role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="op" value="131">
        <input type="hidden" name="CODPROD" value="<?=$dados['CODPROD']?>">
        <input type="hidden" name="LOCACAO_OLD" value="<?=$dados['LOCACAO']?>">

        <?php  
        $produto = buscaDadosProduto($dados['CODPROD']);
        // varDump2($produto);
        ?>

        <div class="modal-body">
          <div class="my-1 row">
            <label class="col-sm-4 col-form-label">Codprod</label>
            <div class="col-sm-8">
              <input type="text" readonly disabled class="form-control-plaintext" value="<?='W'.$produto['CODPROD'].'-'.$produto['DV']?>">
            </div>
          </div>
          <div class="my-1 row">
            <label class="col-sm-4 col-form-label">Descrição</label>
            <div class="col-sm-8">
              <input type="text" readonly disabled class="form-control-plaintext" value="<?=$produto['DESCRICAO']?>">
            </div>
          </div>
          <div class="my-1 row">
            <label class="col-sm-4 col-form-label">Marca</label>
            <div class="col-sm-8">
              <input type="text" readonly disabled class="form-control-plaintext" value="<?=$produto['MARCA']?>">
            </div>
          </div>
          <div class="my-1 row">
            <label class="col-sm-4 col-form-label">Locação Atual</label>
            <div class="col-sm-8">
              <input type="text" readonly disabled class="form-control-plaintext" value="<?=$produto['LOCACAO']?>">
            </div>
          </div>
          <div class="my-1 row">
            <label class="col-sm-4 col-form-label">Locação Nova</label>
            <div class="col-sm-8">
              <input type="text" name="LOCACAO_NEW" id="LOCACAO_NEW" class="form-control" required autofocus autocomplete="off">
              </div>
            </div>
        </div>

        <div class="modal-footer d-flex justify-content-between">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fa fa-ban"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-success" name="acao" value="produto_defineLocacao">
            <i class="fa fa-forward"></i> Avançar
          </button>
        </div>

      </form>

    </div>
  </div>
</div>

<script type='text/javascript'>
  $(document).ready(function () { 
    $('#produto_modalAlterarLocacao').modal('show');
  });
  var myModal = document.getElementById('produto_modalAlterarLocacao')
  var myInput = document.getElementById('LOCACAO_NEW')
  myModal.addEventListener('shown.bs.modal', function () {
    myInput.focus()
  })
</script>