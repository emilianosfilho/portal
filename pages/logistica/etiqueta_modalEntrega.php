<div class="modal fade" id="etiqueta_modalEntrega" role="dialog" aria-labelledby="labelmodalAlteraSenha" aria-hidden="true">
  <div class="modal-dialog modal-lg text-md" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h4 class="modal-title" id="labelmodalAlteraSenha">
          <i class="fa-solid fa-file"></i> Gerar Etiquetas de Entrega
        </h4>
      </div>

      <form role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="op" value="43">
        <input type="hidden" name="nav" value="logistica">
        <input type="hidden" name="aba" value="etiquetas">
        <input type="hidden" name="tab" value="entrega">
        <input type="hidden" name="NUMNOTA" value="<?=$dados['NUMNOTA']?>">

        <?php  
        // varDump2($dados);
        $infoNota = buscaDadosEtiquetaEntrega($dados['NUMNOTA']);
        // varDump2($infoNota);
        ?>

        <div class="modal-body row">

          <div class="col-8">
            <label for="inputEmail4" class="form-label">CLIENTE</label>
            <input type="text" class="form-control" value="<?= $infoNota['CLI_CODCLI'].' - '.$infoNota['CLI_CLIENTE'] ?>" readonly>
          </div>
          <div class="col-4">
            <label for="inputPassword4" class="form-label">NUMNOTA</label>
            <input type="text" class="form-control" value="<?= $infoNota['NUMNOTA'] ?>" readonly>
          </div>
          <div class="col-8">
            <label for="inputCity" class="form-label">FILIAL</label>
            <input type="text" class="form-control" value="<?= $infoNota['FIL_CODFILIAL'].' - '.$infoNota['FIL_RAZAO'] ?>" readonly>
          </div>
          <div class="col-4">
            <label class="form-label">QTD VOLUMES</label>
            <input type="number" class="form-control" name="VOLUMES" value="1">
          </div>
          <div class="col-12">
            <label for="inputPassword4" class="form-label">OBSERVAÇÃO</label>
            <input type="text" name="OBS" class="form-control">
          </div>

        </div>

        <div class="modal-footer d-flex justify-content-between">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fa fa-ban"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-success" name="acao" value="etiqueta_Entrega">
            <i class="fa fa-forward"></i> Avançar
          </button>
        </div>

      </form>

    </div>
  </div>
</div>

<script type='text/javascript'>
  $(document).ready(function () { 
    $('#etiqueta_modalEntrega').modal('show');
  });
</script>