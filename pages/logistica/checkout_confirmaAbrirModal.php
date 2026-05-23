<div class="modal fade" id="checkout_confirmaAbrirModal" role="dialog" aria-labelledby="labelmodalAlteraSenha" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <h4 class="modal-title" id="labelmodalAlteraSenha">
          <i class="fa-solid fa-circle-question"></i> Iniciar Conferência de Check-out
        </h4>
      </div>
      <div class="modal-body">
        <h5>Você tem certeza de que deseja iniciar a conferência de Check-out do Pedido Nr <?=$dados['NUMPED']?>?</h5>
      </div>
      <form action="index.php" method="GET">
        <div class="modal-footer">
          <input type="hidden" name="op" value="132">
          <input type="hidden" name="aba-entrada">
          <input type="hidden" name="NUMPED" value="<?=$dados['NUMPED']?>">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" name="acao" value="checkout_iniciar" class="btn btn-primary">Confirmar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  $( document ).ready(function() {
    $('#checkout_confirmaAbrirModal').modal('show');
  });
</script>