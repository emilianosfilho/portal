<!-- Modal -->
<div class="modal fade" id="modalClienteBalcao" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h5 class="modal-title" id="exampleModalLabel">Cliente no balcão?</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form name="form-maquina" role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="op" value="62">
        <input type="hidden" name="IDORCAMENTO" value="<?=$_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO']?>">
        <div class="modal-body text-lg">
          <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input" type="radio" name="CLIENTEBALCAO" id="exampleRadios1" value="S" <?=(($_SESSION['ORCAMENTO']['CAB']['CLIENTEBALCAO']=="S")?"checked":"")?> >
              <label class="form-check-label" for="exampleRadios1">SIM</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="CLIENTEBALCAO" id="exampleRadios2" value="N" <?=(($_SESSION['ORCAMENTO']['CAB']['CLIENTEBALCAO']=="N")?"checked":"")?> >
              <label class="form-check-label" for="exampleRadios2">NÃO</label>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <div class="mb-3 float-md-end">
            <button type="submit" name="acao" value="defineCLIENTEBALCAO" class="btn btn-primary">Salvar</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>