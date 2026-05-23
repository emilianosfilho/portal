<div class="modal fade" id="vide-modalConfirmaExclusao" role="dialog" aria-hidden="true">
  <div class="modal-dialog text-md" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger">
        <h4 class="modal-title">
          <i class="fa-solid fa-trash"></i> Confirma Exclsão de VIDE
        </h4>
      </div>

      <?php 
      $vide = buscaDadosORCVIDE($dados['IDVIDE']);
      ?>

      <form role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="op" value="40">
        <input type="hidden" name="nav" value="logistica">
        <input type="hidden" name="aba" value="vide">
        <input type="hidden" name="IDVIDE" value="<?= $vide['IDVIDE'] ?>">

        <div class="modal-body">
          <div class="row">
            <div class="col-6">
              <label class="col-form-label">Cod Peça:</label>
              <input type="text" value="<?= $vide['CODPECA'] ?>" class="form-control" autocomplete="off" disabled>
            </div>
            <div class="col-6">
              <label class="col-form-label">Cod Vide:</label>
              <input type="text" value="<?= $vide['VIDE'] ?>" class="form-control" autocomplete="off" disabled>
            </div>
            <div class="col-6">
              <label class="col-form-label">Aplic / Marca:</label>
              <input type="text" value="<?= $vide['APLICMARCA'] ?>" class="form-control" autocomplete="off" disabled>
            </div>
            <div class="col-6">
              <label class="col-form-label">Descrição:</label>
              <input type="text" value="<?= $vide['DESCRICAO'] ?>" class="form-control" autocomplete="off" disabled>
            </div>
            <div class="col-6">
              <label class="col-form-label">Opção:</label>
              <input type="text" value="<?= $vide['NOMEOPCAO'] ?>" class="form-control" autocomplete="off" disabled>
            </div>
            <div class="col-6">
              <label class="col-form-label">Produto Importado:</label>
              <input type="text" value="<?= (($vide['IMPORTADO']=='N')?'NÃO':'SIM') ?>" class="form-control" autocomplete="off" disabled>
            </div>
            <div class="col-6">
              <label class="col-form-label">Preço NPR:</label>
              <input type="text" value="<?= $vide['PRECO'] ?>" class="form-control" autocomplete="off" disabled>
            </div>
            <div class="col-6">
              <label class="col-form-label">Marca:</label>
              <input type="text" value="<?= $vide['MARCA'] ?>" class="form-control" autocomplete="off" disabled>
            </div>
          </div>

        </div>

        <div class="modal-footer d-flex justify-content-between">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fa fa-ban"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-danger" name="acao" value="vide_excluir">
            <i class="fa fa-forward"></i> Confirmar
          </button>
        </div>

      </form>

    </div>
  </div>
</div>

<script>
  $(document).ready(function () {
    $('#vide-modalConfirmaExclusao').modal('show');
  });
</script>