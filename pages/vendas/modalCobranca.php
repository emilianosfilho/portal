<!-- Modal -->
<div class="modal fade" id="modalCobrança" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h5 class="modal-title" id="exampleModalLabel">Informe a Cobrança</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form name="form-maquina" role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="op" value="62">
        <input type="hidden" name="acao" value="defineCobranca">
        <div class="modal-body">
          <div class="mb-3">
            <select class="form-select" name="CODCOB" id="select2Cobranca">
              <?php
              if($_SESSION['BASE']['LISTACOBRANCAS']) {
                foreach ($_SESSION['BASE']['LISTACOBRANCAS'] as $key => $value) {
                  $selected = '';
                  if ($value['CODCOB'] == $_SESSION['ORCAMENTO']['CAB']['CODCOB']) {
                    $selected = ' selected="selected"';
                  }
                  echo '<option value="'.$value['CODCOB'].'" '.$selected.' ">'.$value['COBRANCA'].'  [ '.$value['CODCOB'].' ]</option>';
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