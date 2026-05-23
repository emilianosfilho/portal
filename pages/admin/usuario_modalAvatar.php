<div class="modal fade modal-primary" id="usuario_modalAvatar" tabindex="-1" role="dialog" aria-labelledby="labelusuario_modalNovo" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h4 class="modal-title" id="labelusuario_modalNovo">
          <i class="fa-solid fa-circle-user"></i> Mudar Imagem
        </h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form class="row" action="index.php" method="POST">''
          <input type="hidden" name="op" value="10">
          <input type="hidden" name="IDUSUARIO" value="<?= $usuario['IDUSUARIO'] ?>">

          <div class="row">
              <?php
                // varDump2($dados);
                $dir = "dist/img/";
                $tmp = scandir($dir);
                $o=0;
                foreach ($tmp as $key => $value) {
                  $result = strpos($value, 'avatar');
                  // varDump2($value);
                  if ($result !== false) {
                
                    echo '<div class="col-md-1">';
                    echo '    <div class="custom-control custom-radio image-checkbox">';
                    echo '        <input type="radio" class="custom-control-input" id="AVATAR'.$key.'" name="AVATAR" value="'.$value.'" '.(($value == $usuario['AVATAR'])?'checked':'').'>';
                    echo '        <label class="custom-control-label" for="AVATAR'.$key.'">';
                    echo '            <img class="rounded-circle border border-dark" width="90px" src="'.@DIR_IMG.''.$value.'">';
                    echo '        </label>';
                    echo '    </div>';
                    echo '</div>';

                  }
                }
              ?>

          </div>
   
          <div class="mt-3">
            <div class="d-flex justify-content-between">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fa-solid fa-times"></i> Cancelar</button>
              <button type="submit" name="acao" value="usuario_defineAvatar" class="btn btn-success"><i class="fa-solid fa-save"></i> Salvar</button>
            </div>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>