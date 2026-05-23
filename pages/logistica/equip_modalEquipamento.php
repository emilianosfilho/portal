<div class="modal fade" id="equip_modalEquipamento" aria-labelledby="exampleModalLabel" aria-hidden="true" tabindex="-1">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="labelmodalAlteraSenha">
          <i class="fas fa-edit"></i> Cadastro de Equipamento
        </h4>
      </div>

      <?php  
      // varDump2($dados);
      if (isset($dados["IDEQUIPAMENTO"]) && !empty($dados["IDEQUIPAMENTO"])) {
        $equipamento = buscaEquipamentoID($dados["IDEQUIPAMENTO"]);
      } else {
        $equipamento = [
          'IDEQUIPAMENTO' => null,
          'IDTIPOEQUIP'   => null,
          'IDMONTADORA'   => null,
          'EQUIPAMENTO'   => null
        ];
      }
      // varDump2($montadora);
      ?>

      <form role="form" class="STYLE-NAME" action="index.php" method="GET" enctype="multipart/form-data">
        <input type="hidden" name="op" value="149">
        <input type="hidden" name="nav" value="logistica">
        <input type="hidden" name="tab" value="equipamento">

        <?php if (!empty($equipamento["IDEQUIPAMENTO"])): ?>
          <input type="hidden" name="IDEQUIPAMENTO" value="<?=$equipamento["IDEQUIPAMENTO"]?>">
        <?php endif ?>

        <div class="modal-body">

          <div class="mb-3 row">
            <div class="col">
              <label class="form-label">Montadora</label>
              <select name="IDMONTADORA_NEW" id="IDMONTADORA_NEW" class="form-select select2">
                <?php  
                if ($equipamento === false) {
                  echo '<option value="">Selecione</option>';
                }
                if ($montadoras = listarMontadoras()) {
                  foreach ($montadoras as $key => $value) {
                    echo '<option value="'.$value['IDMONTADORA'].'"';
                    echo ($equipamento['IDMONTADORA'] == $value['IDMONTADORA'])
                      ? 'selected>'
                      : '>';
                    echo $value['MONTADORA'].'</option>';
                  }
                }
                ?>
              </select>
            </div>
          </div>

          <div class="mb-3 row">
            <div class="col">
              <label class="form-label">Tipo Equipamento</label>
              <select name="IDTIPOEQUIP_NEW" id="IDTIPOEQUIP_NEW" class="form-select select2">
                <?php  
                if ($equipamento === false) {
                  echo '<option value="">Selecione</option>';
                }
                if ($tipoequip = listarTipoequip()) {
                  foreach ($tipoequip as $key => $value) {
                    echo '<option value="'.$value['IDTIPOEQUIP'].'"';
                    echo ($equipamento['IDTIPOEQUIP'] == $value['IDTIPOEQUIP'])
                      ? 'selected>'
                      : '>';
                    echo $value['TIPOEQUIPAMENTO'].'</option>'.PHP_EOL;
                  }
                }
                ?>
              </select>
            </div>
          </div>
          
          <div class="mb-3 row">
            <div class="col">
              <label class="form-label">Equipamento</label>
              <input type="text" class="form-control" name="EQUIPAMENTO_NEW" value="<?= (isset($equipamento['EQUIPAMENTO'])?$equipamento['EQUIPAMENTO']:'')?>" required autocomplete="off">
            </div>
          </div>

        </div>

        <div class="modal-footer d-flex justify-content-between">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fa fa-ban"></i> Cancelar
          </button>
          
          <div class="btn-group">
            <?php if (!empty($equipamento["IDEQUIPAMENTO"])): ?>
              <button type="submit" class="btn btn-danger" name="acao" value="equip_excluir">
                <i class="fa fa-trash"></i> Excluir
              </button>
            <?php endif ?>
            <button type="submit" class="btn btn-primary" name="acao" value="equip_merge">
              <i class="fa fa-save"></i> Salvar
            </button>
          </div>
        </div>

      </form>

    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function() {
    $('#equip_modalEquipamento').modal('show');
  });
</script>