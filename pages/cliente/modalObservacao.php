<!-- Modal -->
<div class="modal fade" id="modalObservacao" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h5 class="modal-title" id="exampleModalLabel">Informe a Observação</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="op" value="151">
        <input type="hidden" name="IDORCAMENTO" value="<?=$_SESSION['ORC_CLIENTE']['CAB']['IDORCAMENTO']?>">

        <div class="modal-body">
          <div class="mb-3">
            <input type="text" class="form-control" name="MAQUINA" placeholder="Máquina ou Equipamento" autocomplete="off" maxlength="50" value="<?=(($_SESSION['ORC_CLIENTE']['CAB']['MAQUINA']<>'')?$_SESSION['ORC_CLIENTE']['CAB']['MAQUINA']:'')?>">
            <div class="text-muted">
              Informe a máquina ou o equipamento em que as peças serão aplicadas
            </div>
          </div>
          <div class="mb-3">
            <input type="text" class="form-control" name="NUMPEDCOMP" placeholder="Número do pedido de compra"  oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"  autocomplete="off" maxlength="10" value="<?=(($_SESSION['ORC_CLIENTE']['CAB']['NUMPEDCOMP']<>'')?$_SESSION['ORC_CLIENTE']['CAB']['NUMPEDCOMP']:'')?>" />
            <div class="text-muted">
              Informe a número de pedido/ordem de compra
            </div>
          </div>
          <div class="mb-3">
            <input type="text" class="form-control" name="OBSENTREGA1" placeholder="Observações" autocomplete="off" maxlength="75" value="<?=(($_SESSION['ORC_CLIENTE']['CAB']['OBSENTREGA1']<>'')?$_SESSION['ORC_CLIENTE']['CAB']['OBSENTREGA1']:'')?>">
            <div class="text-muted">
              Campo livre para observqções como nr da requisição, contato, etc...
            </div>
          </div>

        </div>

        <div class="modal-footer">
          <button type="submit" name="acao" value="defineObservacao" class="btn btn-primary">Salvar</button>
        </div>
      </form>

    </div>
  </div>
</div>


