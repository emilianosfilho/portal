<div class="modal fade" id="modalConfirmacao" role="dialog" aria-hidden="true" tabindex="-1">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">
          <i class="fa-solid fa-triangle-exclamation"></i> Confirmação
        </h4>
      </div>

      <form role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
        <?php  
        if ($dados && is_array($dados)) {
          foreach ($dados as $key => $value) {
            echo PHP_EOL.'<input type="hidden" name="'.$key.'" value="'.$value.'">';
          }
        }
        ?>

        <div class="modal-body text-center">
          <i class="fa-solid fa-triangle-exclamation fa-5x text-warning"></i>
          <h3 class="text-weight">Tem certeza que deseja:<br /><?=$dados['msgConfirmacao']?></h3>
        </div>

        <div class="modal-footer d-flex justify-content-between">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fa fa-ban"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-success" name="acao" value="<?=$dados['acaoConfirmacao']?>">
            <i class="fa-solid fa-edit"></i> Confirmar
          </button>
        </div>

      </form>

    </div>
  </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
  $('#modalConfirmacao').modal('show');
});
</script>