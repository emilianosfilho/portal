

<div class="modal fade" id="modalEquipamento" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <?php
      switch ($dados['acao']) {
        case 'equipamentoCadastrar':
          $tipo = "Insert";
          $background = "info";
          $title = '<i class="fa-regular fa-plus"></i> Adicionar Equipamento';
          $montadora = false;
          break;
        case 'equipamentoAtualizar':
          $tipo = "Update";
          $background = "primary";
          $title = '<i class="fa-regular fa-edit"></i> Editar Equipamento';
          $equipamento = buscaEquipamentoID($dados['IDEQUIPAMENTO']);
          break;
        case 'equipamentoExcluir':
          $tipo = "Delete";
          $background = "danger";
          $title = '<i class="fa-regular fa-trash"></i> Excluir Equipamento';
          $equipamento = buscaEquipamentoID($dados['IDEQUIPAMENTO']);
          break;
        default:
          varDump2($dados);  
          break;
      }
      // varDump2($dados);  
      // varDump2($equipamento);  
      ?>
      <div class="modal-header bg-<?=$background?>">
        <h5 class="modal-title" id="exampleModalLabel">
          <?=$title?>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="op" value="149">
          <input type="hidden" name="aba" value="equipamento">

          <div class="input-group mb-3">
            <select name="IDMONTADORA" id="select2IDMONTADORA" class="form-select select2" onchange="validaCampos()" required>
              <option value="">Selecione a Montadora</option>
              <?php  
              if ($montadoras = listarMontadoras()) {
                foreach ($montadoras as $key => $value) {
                  echo '<option value="'.$value['IDMONTADORA'].'" '.((isset($equipamento['IDMONTADORA']) && $equipamento['IDMONTADORA'] == $value['IDMONTADORA'])?'selected':'').'>'.$value['MONTADORA'].'</option>';
                }
              }
              ?>
            </select>
          </div>

          <div class="input-group mb-3">
            <select name="IDTIPOEQUIP" id="select2IDTIPOEQUIP" class="form-select select2" onchange="validaCampos()" required>
              <option value="">Selecione o Tipo Equipamento</option>
              <?php  
              if ($tipoequipamento = listarTipoequip()) {
                foreach ($tipoequipamento as $key => $value) {
                  echo '<option value="'.$value['IDTIPOEQUIP'].'" '.((isset($equipamento['IDTIPOEQUIP']) && $equipamento['IDTIPOEQUIP'] == $value['IDTIPOEQUIP'])?'selected':'').'>'.$value['TIPOEQUIPAMENTO'].'</option>';
                }
              }
              ?>
            </select>
          </div>

          <?php if ($tipo == "Insert"): ?>
            <div class="input-group mb-3">
              <input type="text" name="EQUIPAMENTO" id="inputEQUIPAMENTO1" class="form-control" placeholder="Selecione a Montadora e o Tipo Equipamento" aria-label="Novo registro" aria-describedby="button-addon2" autocomplete="off" required readonly>
              <button type="submit" name="acao" value="equipamento<?=$tipo?>" class="btn btn-info">Salvar Novo</button>
            </div>
          <?php else: ?>
            <?php if ($tipo == "Update"): ?>
              <input type="hidden" name="IDEQUIPAMENTO" id="inputEQUIPAMENTO2" value="<?=$equipamento['IDEQUIPAMENTO']?>">
              <div class="input-group mb-3">
                <input type="text" name="EQUIPAMENTO" value="<?=$equipamento['EQUIPAMENTO']?>" class="form-control" placeholder="Atualizar registro" aria-label="Novo registro" aria-describedby="button-addon2" autocomplete="off" required>
                <button type="submit" name="acao" value="equipamento<?=$tipo?>" class="btn btn-primary">Salvar Alterações</button>
              </div>
            <?php else: ?>
              <input type="hidden" name="IDEQUIPAMENTO" value="<?=$equipamento['IDEQUIPAMENTO']?>">
              <div class="input-group mb-3">
                <input type="text" name="EQUIPAMENTO" value="<?=$equipamento['EQUIPAMENTO']?>" class="form-control" aria-describedby="button-addon2" readonly>
                <button type="submit" name="acao" value="equipamento<?=$tipo?>" class="btn btn-danger">Confirmar Exclusão</button>
              </div>
            <?php endif ?>
          <?php endif ?>

        </form>
      </div>
    </div>
  </div>
</div>


<script>

$(function() {
  $('#modalEquipamento').modal('show');
});


function validaCampos() {
  const modalEquipamento = document.getElementById("modalEquipamento");
  const select2IDMONTADORA = modalEquipamento.querySelector("#select2IDMONTADORA");
  const select2IDTIPOEQUIP = modalEquipamento.querySelector("#select2IDTIPOEQUIP");
  const inputEQUIPAMENTO1 = document.getElementById("inputEQUIPAMENTO1");
  const inputEQUIPAMENTO2 = document.getElementById("inputEQUIPAMENTO2");

  console.log("select2IDMONTADORA: "+select2IDMONTADORA.value);
  console.log("select2IDTIPOEQUIP: "+select2IDTIPOEQUIP.value);

  if (select2IDMONTADORA.value != "" && select2IDTIPOEQUIP.value != "") {
    console.log("habilita o input");
    inputEQUIPAMENTO1.removeAttribute("readonly");
    inputEQUIPAMENTO1.placeholder = 'Nome do Equipamento';
  } else {
    inputEQUIPAMENTO1.setAttribute('readonly', true);
  }

};

</script>