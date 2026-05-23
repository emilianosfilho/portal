<div class="modal fade" id="modalconfirmacheckin_abrir" role="dialog" aria-labelledby="labelmodalAlteraSenha" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h4 class="modal-title text-dark" id="labelmodalAlteraSenha">
          <i class="fa-solid fa-circle-question"></i> Tem a certeza?
        </h4>
      </div>
      <div class="modal-body">
        <h5>Você deseja iniciar a conferência de Checkin da NF <?=$dados['NUMNOTA']?>?</h5>
      </div>
      <form action="index.php" method="GET">
        <div class="modal-footer">
          <input type="hidden" name="op" value="133">
          <input type="hidden" name="aba-entrada">
          <input type="hidden" name="NUMNOTA" value="<?=$dados['NUMNOTA']?>">
          <input type="hidden" name="NUMTRANSENT" value="<?=$dados['NUMTRANSENT']?>">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" name="acao" value="checkin_abrir" class="btn btn-info">Confirmar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  $( document ).ready(function() {
    $('#modalconfirmacheckin_abrir').modal('show');
  });
</script>