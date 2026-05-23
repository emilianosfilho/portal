<div class="modal fade" id="modalFiltro" role="dialog" aria-hidden="true" tabindex="0">
  <div class="modal-dialog text-md" role="document">
    <div class="modal-content">
  
      <?php  
        if ($dados['acao'] == 'adicionarFiltro') {
          $var = [
            "bg" => "info",
            "textColor" => "dark",
            "title" => "Adicionar",
            "icon" => "plus",
            "disabled" => false,
          ];
        } else if ($dados['acao'] == 'editarFiltro') {
          $var = [
            "bg" => "primary",
            "textColor" => "light",
            "title" => "Editar",
            "icon" => "edit",
            "disabled" => false,
          ];
          $filtro = buscaDadosFiltroRelatorio($dados['IDRELATORIOFILTRO']);
        } else {
          $var = [
            "bg" => "danger",
            "textColor" => "light",
            "title" => "Excluir",
            "icon" => "trash",
            "disabled" => true,
          ];
          $filtro = buscaDadosFiltroRelatorio($dados['IDRELATORIOFILTRO']);
        }
        // varDump2($dados);
        // varDump2($var);
        // varDump2($filtro);
      ?>

      <div class="modal-header bg-<?= $var['bg'] ?>">
        <h4 class="modal-title">
          <i class="fa-solid fa-<?= $var['icon'] ?>"></i> <?= $var['title'] ?> Filtro
        </h4>
      </div>

      <form role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="op" value="93">
        <input type="hidden" name="nav" value="relatorios">
        <input type="hidden" name="IDRELATORIO" value="<?=(int) $dados['IDRELATORIO']?>">
        <input type="hidden" name="IDRELATORIOFILTRO" value="<?=(int) $dados['IDRELATORIOFILTRO']?>">

    
        <div class="modal-body">
          <div class="my-1 row">
            <label class="col-sm-4 col-form-label">Campo</label>
            <div class="col-sm-8">
              <input type="text" name="CAMPO" class="form-control" value="<?=$filtro['CAMPO']?>" <?= ($var['disabled']?'disabled':'') ?> >
            </div>
          </div>
          <div class="my-1 row">
            <label class="col-sm-4 col-form-label">Tipo</label>
            <div class="col-sm-8">
              <select name="TIPO" class="form-select" <?= ($var['disabled']?'disabled':'') ?> >
                <option value="DATE" <?=(($filtro['TIPO']=='DATE')?'selected':'')?> >DATE</option>
                <option value="TEXT" <?=(($filtro['TIPO']=='TEXT')?'selected':'')?> >TEXT</option>
                <option value="NUMBER" <?=(($filtro['TIPO']=='NUMBER')?'selected':'')?> >NUMBER</option>
              </select>
            </div>
          </div>
          <div class="my-1 row">
            <label class="col-sm-4 col-form-label">Função</label>
            <div class="col-sm-8">
              <input type="text" name="FUNCAO" class="form-control" value="<?=$filtro['FUNCAO']?>" <?= ($var['disabled']?'disabled':'') ?> >
            </div>
          </div>
        </div>

        <div class="modal-footer d-flex justify-content-between">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fa fa-ban"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-<?= $var['bg'] ?>" name="acao" value="aplicar<?= $dados['acao'] ?>">
            <i class="fa fa-save"></i> Confirmar
          </button>
        </div>

      </form>

    </div>
  </div>
</div>

<script>
  $(document).ready(function () {
    $('#modalFiltro').modal('show');
  });
</script>