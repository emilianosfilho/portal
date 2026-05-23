<div class="modal fade" id="precificacao-modalZerarPreco" role="dialog" aria-labelledby="labelmodalAlteraSenha" aria-hidden="true">
  <div class="modal-dialog modal-lg text-md" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h4 class="modal-title" id="labelmodalAlteraSenha">
          <i class="fa-solid fa-trash"></i> Zerar Preço de venda
        </h4>
      </div>
      <?php  
      $produto = precificacao_buscaDados($dados["CODPROD"]);
      // varDump2($produto);
      ?>

      <form role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="op" value="114">
        <input type="hidden" name="nav" value="compras">
        <input type="hidden" name="aba" value="precificacao">
        <input type="hidden" name="CODPROD" value="<?=$produto["CODPROD"]?>">
        <input type="hidden" name="DV" value="<?=$produto["DV"]?>">
        <input type="hidden" name="NUMORIGINAL_OLD" value="<?=$produto["NUMORIGINAL"]?>">
        <input type="hidden" name="NUMORIGINAL_NEW" value="0X0001">
        <input type="hidden" name="PVENDA_OLD" value="<?= moeda($produto["PVENDA"],6) ?>">
        <input type="hidden" name="PVENDA_NEW" value="<?= 0.0001 ?>">

        <div class="modal-body">
          <div class="row g-3 mb-3">
            <div class="col-3">
              <label class="form-label">COD WINT</label>
              <input type="text" class="form-control" value="<?= $produto["CODPROD"].'-'.$produto["DV"]?>"  placeholder="Código Winthor" disabled>
            </div>
            <div class="col-6">
              <label class="form-label">PRODUTO</label>
              <input type="text" class="form-control" value="<?= $produto["DESCRICAO"]?>"  placeholder="Descrição Winthor" disabled>
            </div>
            <div class="col-3">
              <label class="form-label">NUM ORIGINAL</label>
              <input type="text" class="form-control" value="0X0001"  placeholder="Número Original" disabled>
            </div>
          </div>
          <div class="row g-3 mb-3">
            <div class="col-6">
              <label class="form-label">MARCA</label>
              <input type="text" class="form-control" value="<?= $produto["CODMARCA"].'-'.$produto["MARCA"]?>"  placeholder="Código Marca" disabled>
            </div>
            <div class="col-6">
              <label class="form-label">FORNECEDOR</label>
              <input type="text" class="form-control" value="<?= $produto["CODFORNEC"].'-'.$produto["FORNECEDOR"]?>"  placeholder="Código Fornecedor" disabled>
            </div>
          </div>
          <div class="row g-3 mb-3">
            <div class="col-3">
              <label class="form-label">Últ Entrada</label>
              <input type="text" class="form-control" value="<?= formataDataOracleToBr($produto["DTULTENTRADA"])?>"  placeholder="Código Marca" disabled>
            </div>
            <div class="col-3">
              <label class="form-label">Últ Saída</label>
              <input type="text" class="form-control" value="<?= formataDataOracleToBr($produto["DTULTSAIDA"])?>"  placeholder="Código Fornecedor" disabled>
            </div>
            <div class="col-3">
              <label class="form-label">Preço Atual</label>
              <input type="text" class="form-control" value="<?= moeda($produto["PVENDA"],6) ?>"  placeholder="Código Marca" disabled>
            </div>
            <div class="col-3">
              <label class="form-label">Preço Novo</label>
              <input type="text" class="form-control" value="0.0001"  placeholder="Código Marca" disabled>
            </div>
          </div>
          <div class="row g-3 mb-3">
            <div class="col-12">
              <label class="form-label">MOTIVO</label>
              <select class="form-select" name="MOTIVO" id="select2Motivo" required>
                <option value="preco_invalido" selected >Produto migrado para numoriginal 0X0001 com preço de venda 0.0001</option>
              </select>
            </div>
          </div>
          <div class="row g-3">
            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="CONFIRMA" value="" id="flexCheckDefault" required>
                <label class="form-check-label" for="flexCheckDefault">
                  Declaro que estou ciente e de acordo que a alteração de preço solicitada terá efeito imediato no sistema Winthor, sendo aplicada automaticamente a todas as regiões de preço.
                </label>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer d-flex justify-content-between">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fa fa-ban"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-primary" name="acao" value="precificacao_zerarPreco">
            <i class="fa fa-forward"></i> Avançar
          </button>
        </div>

      </form>

    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  $('#precificacao-modalZerarPreco').modal('show')
});
</script>
