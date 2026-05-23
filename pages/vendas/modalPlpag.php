<!-- Modal -->
<div class="modal fade" id="modalPlPag" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h5 class="modal-title" id="exampleModalLabel">Informe o Plano de Pagamento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form name="form-maquina" role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="op" value="62">
        <input type="hidden" name="acao" value="definePlpag">
        <div class="modal-body">
          <div class="mb-3">
            <select class="form-select" name="CODPLPAG" id="select2PlPag">
              <?php
              if($_SESSION['BASE']['LISTAPLANOS']) {
                foreach ($_SESSION['BASE']['LISTAPLANOS'] as $key => $value) {
                  $selected = '';
                  if ($value['CODPLPAG'] == $_SESSION['ORCAMENTO']['CAB']['CODPLPAG']) {
                    $selected = ' selected="selected"';
                  }
                  echo '<option value="'.$value['CODPLPAG'].'" '.$selected.' ">'.$value['DESCRICAO']." [VM ".moeda($value['VLMINPEDIDO'])."]".'</option>';
                }
              }
              ?>
            </select>
          </div>
          <div class="mb-3 float-md-end">
            <button type="submit" class="btn btn-primary">Salvar</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>