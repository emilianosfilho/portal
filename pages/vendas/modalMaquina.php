<div class="modal fade" id="modalMaquina" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h4 class="modal-title" id="labelmodalAlteraSenha">
          <i class="fa-solid fa-tractor"></i> Define Máquina
        </h4>
      </div>

      <form name="form" role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="op" value="<?=$dados['op']?>">
        <input type="hidden" name="IDORCAMENTO" value="<?=$_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO']?>">

        <div class="modal-body">

          <div class="mb-3 row">
            <div class="col">
              <label class="form-label">Máquina</label>
              <select name="MAQUINA" id="select2MAQUINA" class="form-select select2" required>
                <option value="">Selecione uma Máquina</option>
                <option value="OUTRO">OUTRO</option>
                <?php
                if ($maquinas = buscaEquipamentoMontadora()) {
                  $maquinaValida = 0;
                  foreach ($maquinas as $key => $value) {
                    echo '<option value="'.$value['MAQUINA'].'"';
                    if($_SESSION['ORCAMENTO']['CAB']['MAQUINA'] == $value['MAQUINA']){
                      echo ' selected>';
                      $maquinaValida = 1;
                    } else {
                      echo ' >';
                    }
                    echo $value['EQUIPAMENTO'].'</option>'.PHP_EOL;
                  }
                }
                ?>                
              </select>
            </div>
          </div>

          <div class="mb-3 ">
            <label for="OUTRAMAQUINA" class="form-label">Outra Máquina</label>
            <input type="text" class="form-control" name="OUTRAMAQUINA" id="OUTRAMAQUINA" aria-describedby="emailHelp" disabled placeholder="Informe o nome da Máquina e Montadora">
            <div id="emailHelp" class="form-text">
              Somente deverá ser usada quando a máquina não estiver contida na lista a cima e nestes casos deverá ser selecionada a opção OUTRO.
            </div>
          </div>  

        </div>

        <div class="modal-footer d-flex justify-content-between">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fa fa-ban"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-success" name="acao" value="defineMaquina">
            <i class="fa fa-forward"></i> Avançar
          </button>
        </div>



      </form>

    </div>
  </div>
</div>

<script>
$(function () {

    const MAQUINA       = '<?= $_SESSION['ORCAMENTO']['CAB']['MAQUINA'] ?>';
    const $selectMaquina = $("#select2MAQUINA");
    const $outraMaquina  = $("#OUTRAMAQUINA");

    function existeNoSelect(valor) {
        return $selectMaquina.find(`option[value="${valor}"]`).length > 0;
    }

    function controlarCampoOutraMaquina(valor) {

        if (valor === "OUTRO") {
            $outraMaquina
                .prop("disabled", false)
                .prop("required", true);
        } else {
            $outraMaquina
                .prop("disabled", true)
                .prop("required", false)
                .val("");
        }
    }

    // 🔹 Validação inicial da MAQUINA vinda do backend
    if (MAQUINA !== "") {

        if (existeNoSelect(MAQUINA)) {
            $selectMaquina.val(MAQUINA);
        } else {
            $selectMaquina.val("OUTRO");
            $outraMaquina.val(MAQUINA);
        }
    }

    controlarCampoOutraMaquina($selectMaquina.val());

    // 🔹 Evento onchange
    $selectMaquina.on("change", function () {
        controlarCampoOutraMaquina(this.value);
    });

});
</script>


