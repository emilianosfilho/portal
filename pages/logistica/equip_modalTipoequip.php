<div class="modal fade" id="equip_modalTipoequip" role="dialog" aria-hidden="true" tabindex="-1">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">
          <i class="fas fa-edit"></i> Cadastro de Tipo Equipamento
        </h4>
      </div>

      <?php  
      if (isset($dados["IDTIPOEQUIP"]) && !empty($dados["IDTIPOEQUIP"])) {
        $tipoequip = buscaTipoequipID($dados["IDTIPOEQUIP"]);
      } else {
        $tipoequip = [
          'IDTIPOEQUIP'       => null,
          'TIPOEQUIPAMENTO'   => null
        ];
      }
      // varDump2($montadora);
      ?>

      <form role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="op" value="149">
        <input type="hidden" name="nav" value="logistica">
        <input type="hidden" name="tab" value="equipamento">
        <?php if (!empty($equipamento["IDEQUIPAMENTO"])): ?>
          <input type="hidden" name="IDEQUIPAMENTO" value="<?=$equipamento["IDEQUIPAMENTO"]?>">
        <?php endif ?>


        <div class="modal-body">

          <div class="mb-3 row">
            <div class="col">
              <label class="form-label">Tipo Equipamento</label>
              <input type="text" class="form-control" name="TIPOEQUIPAMENTO" value="<?= $tipoequip['TIPOEQUIPAMENTO']?>" required>
            </div>
          </div>

        </div>

        <div class="modal-footer d-flex justify-content-between">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fa fa-ban"></i> Cancelar
          </button>
          <?php if (empty($tipoequip['IDTIPOEQUIP'])): ?>
            <button type="submit" class="btn btn-success" name="acao" value="equip_tipoequipInsert">
              <i class="fa-solid fa-plus"></i> Cadastrar
            </button>
          <?php else: ?>
            <button type="submit" class="btn btn-danger" name="acao" value="equip_tipoequipDelete">
              <i class="fa-solid fa-trash"></i> Excluir
            </button>
            <button type="submit" class="btn btn-primary" name="acao" value="equip_tipoequipUpdate">
              <i class="fa-solid fa-edit"></i> Atualizar
            </button>
          <?php endif ?>
        </div>

      </form>

    </div>
  </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
  $('#equip_modalTipoequip').modal('show');
});
</script>