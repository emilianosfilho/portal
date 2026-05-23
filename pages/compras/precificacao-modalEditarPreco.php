<div class="modal fade" id="precificacao-modalEditarPreco" role="dialog" aria-labelledby="labelmodalAlteraSenha" aria-hidden="true">
  <div class="modal-dialog modal-lg text-md" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h4 class="modal-title" id="labelmodalAlteraSenha">
          <i class="fa-solid fa-arrows-rotate"></i> Atualizar Preço de venda
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
              <input type="text" class="form-control" value="<?= $produto["NUMORIGINAL"]?>"  placeholder="Número Original" disabled>
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
              <input type="text" name="PVENDA_OLD" class="form-control" value="<?= moeda($produto["PVENDA"],6) ?>"  placeholder="Código Marca" readonly>
            </div>
            <div class="col-3">
              <label class="form-label">Novo Preço</label>
                <input
                  type="text"
                  class="form-control"
                  id="valor"
                  placeholder="R$ 0,00"
                  inputmode="numeric"
                  autocomplete="off"
                  required
                >
                <input type="hidden" id="valor_real" name="PVENDA_NEW">
            </div>
          </div>
          <div class="row g-3 mb-3">
            <div class="col-12">
              <label class="form-label">MOTIVO</label>
              <select class="form-select" name="MOTIVO" id="select2Motivo" required>
                <?php if ( $produto["PVENDA"]<=0.0001 ): ?>
                    <option value="preco_invalido" selected >Ajuste por preço de venda inválido (0,0001)</option>
                <?php else: ?>
                  <option value="">Selecione uma justificativa</option>
                  <option value="determinacao_diretoria">Determinação da diretoria</option>
                  <option value="preco_defasado">Correção de preço defasado</option>
                  <option value="correcao_preco">Correção de preço cadastrado incorretamente</option>
                  <option value="alteracao_tributaria">Alteração de impostos ou carga tributária</option>
                  <option value="revisao_margem">Revisão de margem de lucro</option>
                  <option value="fim_ciclo_vida">Produto em fim de ciclo de vida ou descontinuação</option>
                  <option value="sazonalidade">Ajuste por sazonalidade</option>
                <?php endif ?>
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
          <button type="submit" class="btn btn-primary" name="acao" value="precificacao_editarPreco">
            <i class="fa fa-forward"></i> Avançar
          </button>
        </div>

      </form>

    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  $('#precificacao-modalEditarPreco').modal('show')
});

const inputValor = document.getElementById('valor');
const inputReal  = document.getElementById('valor_real');

function formatarMoeda(valor) {
    valor = valor.replace(/\D/g, '');

    const numero = (parseFloat(valor) / 100).toFixed(2);

    return numero.toLocaleString('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    });
}

inputValor.addEventListener('input', function () {
    let valor = this.value.replace(/\D/g, '');

    if (valor === '') {
        this.value = '';
        inputReal.value = '';
        return;
    }

    const valorNumerico = (parseFloat(valor) / 100).toFixed(2);

    this.value = Number(valorNumerico).toLocaleString('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    });

    // valor real para backend (ex: 1234.56)
    inputReal.value = valorNumerico;
});
</script>
