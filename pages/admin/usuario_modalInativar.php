<div class="modal fade modal-primary" id="usuario_modalInativar" tabindex="-1" role="dialog" aria-labelledby="labelusuario_modalNovo" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger">
        <h4 class="modal-title" id="labelusuario_modalNovo">
          <i class="fa-solid fa-ban"></i> Inativar Usuario
        </h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form class="row" action="index.php" method="POST">''
          <input type="hidden" name="op" value="10">
          <input type="hidden" name="IDUSUARIO" value="<?= $usuario['IDUSUARIO'] ?>">

          <div class="row col">
            <h3>Você confirma que deseja inativar o usuário?</h3>
            <ul>
              <li>O usuário ficará impossibilitado de ter acesso ao Portal.</li>
            </ul>
          </div>
   
          <div class="mt-3">
            <div class="d-flex justify-content-between">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fa-solid fa-times"></i> Cancelar</button>
              <button type="submit" name="acao" value="usuario_inativar" class="btn btn-danger"><i class="fa-solid fa-save"></i> Inativar</button>
            </div>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>