<div class="modal fade" id="modalConfirmarFaturarOrcamento" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="exampleModalLabel">
          <i class="fa fa-money"></i> Faturar Orçamento
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <h3>Deseja realmente enviar o Orçamento Nr <?= $_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO'] ?> para faturamento?</h3>
      </div>
      <div class="modal-footer">
        <form name="form-maquina" role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="op" value="62">
          <input type="hidden" name="acao" value="faturarOrcamento">
          <input type="hidden" name="IDORCAMENTO" value="<?= $_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO'] ?>">
          <div class="d-flex justify-content-md-between">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Cancelar</button>        
            <button type="submit" class="btn btn-success">Confirmar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>