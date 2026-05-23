<div class="modal fade" id="modalPesquisaFornecedor" role="dialog" aria-labelledby="labelmodalAlteraSenha" aria-hidden="true" tabindex="-1">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="labelmodalAlteraSenha">
          <i class="fas fa-search"></i> Pesquisa Fornecedor
        </h4>
      </div>

      <form role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="op" value="<?=$dados['op']?>">


        <div class="modal-body">
          <div class="mb-3 row">
            <div class="col-sm-12">
              <select class="form-select" name="CODFORNEC" id="select2FornecCheckin">
                <?php
                if ($fornecedores = buscaFornecedores()) {
                  foreach ($fornecedores as $key => $value) {
                    echo '<option value="'.$value['CODFORNEC'].'">'.$value['CODFORNEC'].'- '.$value['FORNECEDOR'].'</option>';
                  }
                }
                ?>
              </select>
            </div>
          </div>
        </div>

        <div class="modal-footer d-flex justify-content-between">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fa fa-ban"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-success" name="acao" value="selecionarFornecedorCheckin">
            <i class="fa fa-forward"></i> Avançar
          </button>
        </div>

      </form>

    </div>
  </div>
</div>