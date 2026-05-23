<div class="modal fade" id="produto_modalEditarProd" role="dialog" aria-labelledby="labelmodalAlteraSenha" aria-hidden="true" tabindex="-1">
  <div class="modal-dialog modal-lg text-md" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h4 class="modal-title" id="labelmodalAlteraSenha">
          <i class="fa-solid fa-edit"></i> Editar Cadastro do produto <?=$dados['CODPROD'].'-'.$dados['DV']?>
        </h4>
      </div>

        <?php  
        $produto = buscaDadosProduto($dados['CODPROD']);
        // varDump2($produto);
        ?>

      <form role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="op" value="131">
        <input type="hidden" name="nav" value="logistica">
        <input type="hidden" name="tab" value="produto">
        <input type="hidden" name="CODPROD" value="<?=$produto['CODPROD']?>">

        <div class="modal-body row">

          <div class="col-4">
            <label for="inputEmail4" class="form-label">Núm Original</label>
            <input type="text" class="form-control" name="NUMORIGINAL" value="<?=$produto['NUMORIGINAL']?>">
          </div>
          <div class="col-8">
            <label class="form-label">Descrição</label>
            <input type="text" class="form-control" name="DESCRICAO" value="<?=$produto['DESCRICAO']?>">
          </div>
          <div class="col-4">
            <label for="inputCity" class="form-label">Marca</label>
            <select class="form-select" name="CODMARCA" id="select2codmarca">
              <?php
              if ($marcas = buscaMarcas()) {
                foreach ($marcas as $marca) {
                  echo '<option value="'.$marca['CODMARCA'].'" '.(($produto['MARCA']==$marca['MARCA'])?'selected':'').'>'.$marca['MARCA'].'</option>';
                  }
                }
              ?>
            </select>
          </div>
          
          <div class="col-4">
            <label for="inputAddress" class="form-label">Departamento</label>
            <select class="form-select" name="CODEPTO" id="select2depto">
              <?php
              if ($deptos = buscaDepartamentos()) {
                foreach ($deptos as $depto) {
                  echo '<option value="'.$depto['CODEPTO'].'" '.(($produto['DEPARTAMENTO']==$depto['DEPARTAMENTO'])?'selected':'').'>'.$depto['DEPARTAMENTO'].'</option>';
                }
              }
              ?>
            </select>
          </div>
          <div class="col-4">
            <label for="inputAddress2" class="form-label">Seção</label>
            <select class="form-select" name="CODSEC" id="select2secao">
              <?php
              if ($secoes = buscaSecoes()) {
                foreach ($secoes as $secao) {
                  echo '<option value="'.$secao['CODSEC'].'" '.(($produto['SECAO']==$secao['SECAO'])?'selected':'').'>'.$secao['SECAO'].'</option>';
                }
              }
              ?>
            </select>
          </div>
          <div class="col-3">
            <label for="inputState" class="form-label">Locação</label>
            <input type="text" class="form-control" name="LOCACAO" value="<?=$produto['LOCACAO']?>">
          </div>
          <div class="col-3">
            <label for="inputState" class="form-label">Cód Fábrica</label>
            <input type="text" class="form-control" name="CODFAB" value="<?=$produto['CODFAB']?>">
          </div>
          <div class="col-3">
            <label for="inputCity" class="form-label">NCM</label>
            <input type="text" class="form-control" name="NBM" value="<?=$produto['NBM']?>">
          </div>
          <div class="col-3">
            <label for="inputCity" class="form-label">NCM+Excessão</label>
            <input type="text" class="form-control" name="CODNCMEX" value="<?=$produto['CODNCMEX']?>">
          </div>

        </div>

        <div class="modal-footer d-flex justify-content-between">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fa fa-ban"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-success" name="acao" value="produto_atualizaDados">
            <i class="fa fa-save"></i> Atualizar
          </button>
        </div>

      </form>

    </div>
  </div>
</div>

<script type='text/javascript'>
  $(document).ready(function () { 
    $('#produto_modalEditarProd').modal('show');
  });
</script>