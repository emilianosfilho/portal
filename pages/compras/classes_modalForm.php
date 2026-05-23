<div class="modal fade" id="classes_modalForm" role="dialog" aria-labelledby="labelmodalAlteraSenha" aria-hidden="true">
  <div class="modal-dialog text-md" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h4 class="modal-title" id="labelmodalAlteraSenha">
          <?php 
          if ($dados["acao"] == "classeDelete"){
            echo '<i class="fa-solid fa-trash"></i> Excluir Registro';
            $classe = buscaClasseID($dados["CODLINHA"]);
          } else if ($dados["acao"] == "classeEdit") {
            echo '<i class="fa-solid fa-edit"></i> Editar Registro';
          } else {
            echo '<i class="fa-solid fa-plus"></i> Adicionar Registro';
          }
          ?>
        </h4>
      </div>
      <form role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="op" value="126">
        <input type="hidden" name="aba" value="classes">
        <input type="hidden" name="CODPROD" value="<?=$produto['CODPROD']?>">
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
          <button type="submit" class="btn btn-success" name="acao" value="salvarAlteracaoLocacao">
            <i class="fa fa-forward"></i> Avançar
          </button>
        </div>

      </form>

    </div>
  </div>
</div>