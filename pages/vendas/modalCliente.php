<div class="modal fade" id="modalCliente" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header text-center bg-primary">
        <h4 class="modal-title w-100 font-weight-bold">
          Selecione o novo Cliente
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </h4>
      </div>

      <form role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="op" value="62">
        <input type="hidden" name="defineCliente">
        <div class="modal-body mx-3">
          <div class="form-group">
            <label>SELECIONE O CLIENTE</label>
            <select class="form-control" style="width: 100%;" name="CODCLI" id="select2Cliente">
              <?php  
              $clientes = listaClientes();
              // varDump2($vendedores);
              if ($clientes) {
                foreach ($clientes as $key => $value) {
                  $selected = '';
                  if ($value['CODCLI'] == $_SESSION['ORCAMENTO']['CLIENTE']['CODCLI']) {
                    $selected = ' selected="selected"';
                  }
                  echo '<option value="'.$value['CODCLI'].'" '.$selected.' ">'.$value['CODCLI'].'- '.$value['FANTASIA'].'</option>';
                }
              }
              ?>
            </select>
          </div>
          <!-- /.form-group -->
        </div>
        <div class="modal-footer d-flex justify-content-center">
          <button type="submit" class="btn btn-primary">Salvar</button>
        </div>
      </form>

    </div>
  </div>
</div>