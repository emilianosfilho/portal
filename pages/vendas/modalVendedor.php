<div class="modal fade" id="modalVendedor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header text-center bg-primary">
        <h4 class="modal-title w-100 font-weight-bold">
          Selecione o novo Vendedor
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </h4>
      </div>

      <form role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="op" value="62">
        <input type="hidden" name="acao" value="defineVendedor">

        <div class="modal-body mx-3">
          <div class="form-group">
            <label>VENDEDOR</label>
            <select class="form-control" style="width: 100%;" name="CODUSUR" id="select2Vendedor">
              <?php  
              $vendedores = listaVendedores();
              // varDump2($vendedores);
              if ($vendedores) {
                foreach ($vendedores as $key => $value) {
                  $selected = '';
                  if ($value['CODUSUR'] == $_SESSION['ORCAMENTO']['VENDEDOR']['CODUSUR']) {
                    $selected = ' selected="selected"';
                  }
                  echo '<option value="'.$value['CODUSUR'].'" '.$selected.' ">'.$value['CODUSUR'].'- '.$value['NOME'].'</option>';
                }
              }
              ?>
            </select>
          </div>
          <!-- /.form-group -->
        </div>
        <div class="modal-footer d-flex justify-content-center">
          <button type="button" class="pull-left btn btn-secondary" data-dismiss="modal" aria-label="Close">Cancelar</button>
          <button type="submit" class="pull-rigth btn btn-primary">Salvar</button>
        </div>
      </form>

    </div>
  </div>
</div>