<div class="modal fade modal-primary" id="usuario_modalPermissoes" tabindex="-1" role="dialog" aria-labelledby="labelusuario_modalNovo" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-success">
        <h4 class="modal-title" id="labelusuario_modalNovo">
          <i class="fa-solid fa-list-check"></i> Permissões de acesso
        </h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form class="row" action="index.php" method="POST">''
          <input type="hidden" name="op" value="11">
          <input type="hidden" name="IDUSUARIO" value="<?=$dados['IDUSUARIO']?>">

            <?php  
            
            if ($permissoes = buscaPermissoes()) {
              $permUsuario = buscaPermissoesUsuario($dados['IDUSUARIO']);
              foreach ($permissoes as $key => $value) {
            ?>
              <div class="col-md-12">
                <div class="form-group p-3 py-1">
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" name="permissao[<?=$value['IDPERMISSAO']?>]" <?=(in_array($value['PERMISSAO'], $permUsuario))?' checked':''?> >
                    <label class="form-check-label" for="<?=$value['IDPERMISSAO']?>"><?=$value['PERMISSAO']?></label>
                  </div>
                </div>
              </div>
            <?php  
              } //end foreach
            } // end if
            ?>
          <div class="mt-3">
            <div class="d-flex justify-content-between">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fa-solid fa-times"></i> Cancelar</button>
              <button type="submit" name="acao" value="usuario_alterarPermissoes" class="btn btn-success"><i class="fa-solid fa-save"></i> Salvar</button>
            </div>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>