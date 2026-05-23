<div class="modal fade" id="modalConfirmaTransito" role="dialog" aria-labelledby="labelmodalAlteraSenha" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h4 class="modal-title" id="labelmodalAlteraSenha">
          <i class="fa-solid fa-truck-arrow-right"></i> Confirmar pedido Em Trânsito
        </h4>
      </div>
      <div class="modal-body">
        <h3><strong>Confima que o pedido <?=$_SESSION['CHECKOUT']['CAB']['NUMPED']?> possui itens em trânsito?</strong></h3>
      </div>
      <form action="index.php" method="GET">
        <div class="modal-footer">
          <input type="hidden" name="op" value="137">
          <input type="hidden" name="aba-saida">
          <input type="hidden" name="IDCHECKOUT" value="<?=$_SESSION['CHECKOUT']['CAB']['IDCHECKOUT']?>">
          <input type="hidden" name="NUMPED" value="<?=$_SESSION['CHECKOUT']['CAB']['NUMPED']?>">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" name="acao" value="confirmarTransito" class="btn btn-primary">
            Confirmar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>