<div class="modal fade" id="checkout_confirmaResetarModal" role="dialog" aria-labelledby="labelmodalAlteraSenha" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger">
        <h4 class="modal-title" id="labelmodalAlteraSenha">
          <i class="fa-solid fa-circle-question"></i> Resetar Check-out
        </h4>
      </div>
      <div class="modal-body">
        <h5>Você tem certeza de que deseja resetar a conferência de Check-out do Pedido Nr <?=$dados['NUMPED']?>?</h5>
        <p><div class="text-danger"><b>ATENÇÃO:</b> Esta ação não poderá ser desfeita posteriormente!</div></p>
      </div>
      <form action="index.php" method="GET">
        <div class="modal-footer">
          <input type="hidden" name="op" value="132">
          <input type="hidden" name="aba-entrada">
          <input type="hidden" name="IDCHECKOUT" value="<?=$dados['IDCHECKOUT']?>">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" name="acao" value="checkout_resetar" class="btn btn-danger">Confirmar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  $( document ).ready(function() {
    $('#checkout_confirmaResetarModal').modal('show');
  });
</script>