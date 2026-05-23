<div class="modal fade" id="vide-modalAtualizar" role="dialog" aria-hidden="true">
  <div class="modal-dialog text-md" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <h4 class="modal-title">
          <i class="fa-solid fa-edit"></i> Atualizar VIDE
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
              <input type="text" name="CODPECA" value="<?= $vide['CODPECA'] ?>" class="form-control" autocomplete="off" required>
            </div>
            <div class="col-6">
              <label class="col-form-label">Cod Vide:</label>
              <input type="text" name="VIDE" value="<?= $vide['VIDE'] ?>" class="form-control">
            </div>
            <div class="col-6">
              <label class="col-form-label">Aplic / Marca:</label>
              <input type="text" name="APLICMARCA" value="<?= $vide['APLICMARCA'] ?>" class="form-control" autocomplete="off">
            </div>
            <div class="col-6">
              <label class="col-form-label">Descrição:</label>
              <input type="text" name="DESCRICAO" value="<?= $vide['DESCRICAO'] ?>" class="form-control" autocomplete="off" required>
            </div>
            <div class="col-6">
              <label class="col-form-label">Opção:</label>
              <select class="form-select" name="NOMEOPCAO" aria-label="Default select example">
                <option value="VIDE" <?= (($vide['NOMEOPCAO']=='VIDE')?'selected':'') ?> >VIDE</option>
                <option value="INFO" <?= (($vide['NOMEOPCAO']=='INFO')?'selected':'') ?> >INFO</option>
                <option value="OPCAO" <?= (($vide['NOMEOPCAO']=='OPCAO')?'selected':'') ?> >OPCAO</option>
                <option value="NPR" <?= (($vide['NOMEOPCAO']=='NPR')?'selected':'') ?> >NPR</option>
              </select>
            </div>
            <div class="col-6">
              <label class="col-form-label">Produto Importado:</label>
              <select class="form-select" name="IMPORTADO" aria-label="Default select example">
                <option value="N" <?= (($vide['IMPORTADO']=='N')?'selected':'') ?> >NÃO</option>
                <option value="S" <?= (($vide['IMPORTADO']=='S')?'selected':'') ?> >SIM</option>
              </select>
            </div>
            <div class="col-6">
              <label class="col-form-label">Preço NPR:</label>
              <input type="text" name="PRECO" value="<?= $vide['PRECO'] ?>" class="form-control" autocomplete="off">
            </div>
            <div class="col-6">
              <label class="col-form-label">Marca:</label>
              <input type="text" name="MARCA" value="<?= $vide['MARCA'] ?>" class="form-control" autocomplete="off">
            </div>
          </div>

        </div>

        <div class="modal-footer d-flex justify-content-between">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fa fa-ban"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-danger" name="acao" value="vide-modalConfirmaExclusao">
            <i class="fa fa-trash"></i> Excluir
          </button>
          <button type="submit" class="btn btn-primary" name="acao" value="vide_atualizar">
            <i class="fa fa-forward"></i> Confirmar
          </button>
        </div>

      </form>

    </div>
  </div>
</div>

<script>
  $(document).ready(function () {
    $('#vide-modalAtualizar').modal('show');
  });
</script>