<div class="modal fade" id="checkin_modalConfirmaFinalizar" role="dialog" aria-labelledby="labelmodalAlteraSenha" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-success">
        <h4 class="modal-title" id="labelmodalAlteraSenha">
          <i class="fa-solid fa-circle-check"></i> Confirmação Finalizar
        </h4>
      </div>
      <div class="modal-body">
        <h5>Você tem certeza de que deseja finalizar a conferência de entrada da NF <?=$dados['NUMNOTA']?>?</h5>
      </div>
      <form action="index.php" method="GET">
        <div class="modal-footer">
          <input type="hidden" name="op" value="133">
          <input type="hidden" name="aba" value="entrada">
          <input type="hidden" name="IDCHECKIN" value="<?=$dados['IDCHECKIN']?>">
          <input type="hidden" name="NUMNOTA" value="<?=$dados['NUMNOTA']?>">
          <input type="hidden" name="NUMTRANSENT" value="<?=$dados['NUMTRANSENT']?>">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" name="acao" value="checkin_defineFinalizar" class="btn btn-success"><i class="fa-solid fa-check"></i> Confirmar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  $( document ).ready(function() {
    $('#checkin_modalConfirmaFinalizar').modal('show');
  });
</script>