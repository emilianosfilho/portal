<!-- Modal -->
<div class="modal fade" id="modalExibirCodpeca" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h5 class="modal-title" id="exampleModalLabel">Exibir Cod. Peça na etiqueta de venda?</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form name="form-maquina" role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="op" value="62">
        <input type="hidden" name="aba" value="faturamento">
        <input type="hidden" name="IDORCAMENTO" value="<?=$_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO']?>">
        <div class="modal-body text-lg">
          <div class="row mb-2">
            <div class="col">
              <h5 class="text-danger">
                <b>ATENÇÃO:</b><br>Ao marcar a opção SIM, a etiqueta de venda exibirá o Código da peça digitado no orçamento.<br>Use este recurso com responsabilidade.
              </h5>
            </div>
          </div>
          <div class="row">
            <div class="col-6 d-grid gap-2">
              <input type="radio" class="btn-check" name="EXIBIRCODPECAETIQUETA" value="N" id="option1" autocomplete="off" <?=(($_SESSION['ORCAMENTO']['CAB']['EXIBIRCODPECAETIQUETA']=="N")?"checked":"")?> >
              <label class="btn btn-lg btn-outline-primary" for="option1">NÃO</label>
            </div>
            <div class="col-6 d-grid gap-2">
              <input type="radio" class="btn-check" name="EXIBIRCODPECAETIQUETA" value="S" id="option2" autocomplete="off" <?=(($_SESSION['ORCAMENTO']['CAB']['EXIBIRCODPECAETIQUETA']=="S")?"checked":"")?> >
              <label class="btn btn-lg btn-outline-danger" for="option2">SIM</label>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <div class="mb-3 float-md-end">
            <button type="submit" name="acao" value="defineEXIBIRCODPECAETIQUETA" class="btn btn-primary">Salvar</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>