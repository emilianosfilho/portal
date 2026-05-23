<div class="modal fade modal-primary" id="usuario_modalDefinirSenha" tabindex="-1" role="dialog" aria-labelledby="labelusuario_modalNovo" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h4 class="modal-title" id="labelusuario_modalNovo">
          <i class="fa-solid fa-retweet"></i> Definir Senha
        </h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form class="row" action="index.php" method="POST">''
          <input type="hidden" name="op" value="10">
          <input type="hidden" name="IDUSUARIO" value="<?= $usuario['IDUSUARIO'] ?>">
          <input type="hidden" name="LOGIN" value="<?= $usuario['LOGIN'] ?>">

          <div class="col-md-12">
            <label class="labels">Nome</label>
            <input type="text" class="form-control" value="<?= $usuario['NOME'] ?>" placeholder="NOME COMPLETO" disabled>
          </div>
          <div class="col-md-12">
            <label class="labels">E-mail</label>
            <input type="email" class="form-control" value="<?= $usuario['EMAIL'] ?>" placeholder="EMAIL" disabled>
          </div>
          <div class="col-md-12">
            <label class="labels">Login</label>
            <input type="text" class="form-control" value="<?= $usuario['LOGIN'] ?>" placeholder="LOGIN" disabled>
          </div>
          <div class="col-md-12">
            <label class="labels">Nova Senha</label>
            <input name="SENHA_NEW" type="password" class="form-control" placeholder="Nova senha" required autocomplete="off">
          </div>
          <div class="col-md-12">
            <label class="labels">Repita a nova senha</label>
            <input name="CONFIRMACAO" type="password" class="form-control" placeholder="Confirmação" required autocomplete="off">
          </div>
   
          <div class="mt-3">
            <div class="d-flex justify-content-between">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fa-solid fa-times"></i> Cancelar</button>
              <button type="submit" name="acao" value="usuario_definirSenha" class="btn btn-success"><i class="fa-solid fa-save"></i> Salvar</button>
            </div>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>