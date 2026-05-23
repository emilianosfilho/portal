<div class="modal fade" id="modalPrevisao" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h4 class="modal-title">
          <i class="fa-solid fa-clock"></i> Definir Tempo para Separação
        </h4>
      </div>
      <div class="modal-body">
        <form action="index.php" method="GET">
          <div class="modal-footer">
            <input type="hidden" name="op" value="137">
            <input type="hidden" name="aba-saida">
            <input type="hidden" name="IDCHECKOUT" value="<?=$_SESSION['CHECKOUT']['CAB']['IDCHECKOUT']?>">
            <input type="hidden" name="NUMPED" value="<?=$_SESSION['CHECKOUT']['CAB']['NUMPED']?>">
              
            <select name="PREVISAO" class="form-select">
              <option value="10" selected>10 min</option>
              <option value="15">15 min</option>
              <option value="20">20 min</option>
              <option value="30">30 min</option>
              <option value="60">60 min</option>
            </select>

            <button type="submit" name="acao" value="confirmarPrevisao" class="btn btn-primary">
              Confirmar
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function () {
  $('#modalPrevisao').modal('show');
})  
</script>