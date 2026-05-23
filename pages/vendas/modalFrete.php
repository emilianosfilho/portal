<!-- Modal -->
<div class="modal fade" id="modalFrete" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h5 class="modal-title" id="exampleModalLabel">Informe o Frete / Obsercações de Entrega</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form name="form-maquina" role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="op" value="62">
        <div class="modal-body">
          <div class="row">
            <div class="col-8 mb-3">
              <label>Despacho:</label>
              <select class="form-select" name="FRETEDESPACHO" id="select2freteDespacho">
                <option value="G" <?=($_SESSION['ORCAMENTO']['CAB']['FRETEDESPACHO']=="G")?'selected':''?>>Retira na loja</option>
                <option value="C" <?=($_SESSION['ORCAMENTO']['CAB']['FRETEDESPACHO']=="C")?'selected':''?>>[CIF] Frete VEMAP</option>
                <option value="F" <?=($_SESSION['ORCAMENTO']['CAB']['FRETEDESPACHO']=="F")?'selected':''?>>[FOB] Frete Cliente</option>
                <option value="T" <?=($_SESSION['ORCAMENTO']['CAB']['FRETEDESPACHO']=="T")?'selected':''?>>Frete Terceiros</option>
                <option value="R" <?=($_SESSION['ORCAMENTO']['CAB']['FRETEDESPACHO']=="R")?'selected':''?>>Transporte Próprio VEMAP</option>
                <option value="D" <?=($_SESSION['ORCAMENTO']['CAB']['FRETEDESPACHO']=="D")?'selected':''?>>Transporte Próprio Cliente</option>
              </select>
            </div>
            <div class="col-4 mb-3">
              <label>Valor do frete:</label>
              <input type="text" name="VALORFRETE" id="VALORFRETE" class="form-control" value="<?=$_SESSION['ORCAMENTO']['CAB']['VALORFRETE']?>" placeholder="0,00">
            </div>  
          </div>
            
          <div class="col-12 mb-3">
            <label>Observação de Entrega:</label>
            <textarea name="OBSENTREGA3" id="OBSENTREGA3" class="form-control" rows="2" maxlength="75" autocomplete="off"><?=$_SESSION['ORCAMENTO']['CAB']['OBSENTREGA3']?></textarea>
            <textarea name="OBSENTREGA4" id="OBSENTREGA4" class="form-control" rows="2" maxlength="75" autocomplete="off"><?=$_SESSION['ORCAMENTO']['CAB']['OBSENTREGA4']?></textarea>
          </div>  
          <div class="mb-3 float-md-end">
            <button type="submit" name="acao" value="defineFrete" class="btn btn-primary">Salvar</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  $(document).ready(function () {
    valorFrete = document.getElementById('VALORFRETE'); // or in jQuery use: select = this;
    select = document.getElementById('select2freteDespacho'); // or in jQuery use: select = this;
    
    if (select.value == 'G') {
      valorFrete.value = 0;
      valorFrete.setAttribute('readonly', true);
    }

    select.onchange = function(){
      console.log(select.value);
      if (select.value == 'G') {
        valorFrete.value = 0;
        valorFrete.setAttribute('readonly', true);
        console.log(valorFrete.value);
      } else {
        valorFrete.removeAttribute('readonly');
        console.log(valorFrete.value);
      }
    };

  });
</script>