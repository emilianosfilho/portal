<div class="container-fluid mt-5 text-sm">
  <div class="row">
    <?php 
    require_once('pages/admin/function.php'); 
    require_once('pages/admin/controller.php'); 
    require_once('pages/admin/sidebar.php'); 
    ?>
    <main class="col ms-sm-auto px-3">

      <div class="row">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 border-bottom">
          <h1 class="h2">
            <i class="fa-solid fa-user"></i> Editar Usuário
          </h1>
        </div>
      </div>

      <div class="row mt-3">
        <?php 
          // varDump2($dados);
          if (isset($dados['IDUSUARIO']) && !empty($dados['IDUSUARIO'])) {
            $usuario = buscaUsuarioId($dados['IDUSUARIO']);
            // varDump2($usuario);
            include ('pages/admin/usuario_modalAvatar.php'); 
            include ('pages/admin/usuario_modalResetarSenha.php'); 
            include ('pages/admin/usuario_modalAlterarSenha.php'); 
            include ('pages/admin/usuario_modalDefinirSenha.php'); 
            include ('pages/admin/usuario_modalPermissoes.php'); 
            include ('pages/admin/usuario_modalInativar.php'); 
            include ('pages/admin/usuario_modalAtivar.php'); 
          } else {
            throw new Exception("IDUSUARIO inválido ou não informado!");
          }
        ?>

        <div class="col-2">
          <div style="text-align: center;">
            <img class="rounded-circle border border-dark" width="100px" src="<?=@DIR_IMG.''.$usuario['AVATAR']?>">
            <br><small class="text-muted">Cadastrado em: <?= formataDataOracleToBr($usuario['DTCADASTRO']) ?></small>
            <br><small class="text-muted">Último acesso: há <?= diferencaTempo($usuario['DTULTLOGIN']) ?></small>
            <div class="btn-group-vertical mt-2">
              <button type="button" data-bs-toggle="modal" data-bs-target="#usuario_modalAvatar" class="btn btn-xs btn-info">Mudar Imagem</button>
              <button type="button" data-bs-toggle="modal" data-bs-target="#usuario_modalResetarSenha" class="btn btn-xs btn-info">Resetar Senha</button>
              <button type="button" data-bs-toggle="modal" data-bs-target="#usuario_modalDefinirSenha" class="btn btn-xs btn-info">Definir Nova Senha</button>
              <button type="button" data-bs-toggle="modal" data-bs-target="#usuario_modalPermissoes" class="btn btn-xs btn-success">Permissões de acesso</button>
              <?php if (empty($usuario['DTEXCLUSAO'])): ?>
                <button type="button" data-bs-toggle="modal" data-bs-target="#usuario_modalInativar" class="btn btn-xs btn-danger">Inativar Usuário</button>
              <?php else: ?>
                <button type="button" data-bs-toggle="modal" data-bs-target="#usuario_modalAtivar" class="btn btn-xs btn-success">Ativar Usuário</button>
              <?php endif ?>
            </div>
          </div>
        </div>


        <div class="col-10">
          <form class="row" action="index.php" method="POST">
            <input type="hidden" name="op" value="11">
            <input type="hidden" name="nav" value="admin">
            <input type="hidden" name="tab" value="usuarios">
            <input type="hidden" name="IDUSUARIO" value="<?= $usuario['IDUSUARIO'] ?>">

            <div class="col-md-8">
              <label class="labels">Nome</label>
              <input name="NOME" value="<?= $usuario["NOME"] ?>" type="text" class="form-control" placeholder="NOME COMPLETO" required autocomplete="off">
            </div>
            <div class="col-md-4">
              <label class="labels">Login</label>
              <input name="LOGIN" value="<?= $usuario["LOGIN"] ?>" type="text" class="form-control" placeholder="LOGIN" required autocomplete="off">
            </div>
            <div class="col-md-8">
              <label class="labels">E-mail</label>
              <input name="EMAIL" value="<?= $usuario["EMAIL"] ?>" type="email" class="form-control" placeholder="EMAIL" required autocomplete="off">
            </div>
            <div class="col-md-4">
              <label class="labels">Perfil</label>
              <select name="PERFIL" class="form-select" require>
                <option value="CLIENTE" <?= (($usuario["PERFIL"]=="CLIENTE")?'selected':'') ?>>CLIENTE</option>
                <option value="VENDEDOR" <?= (($usuario["PERFIL"]=="VENDEDOR")?'selected':'') ?>>VENDEDOR</option>
                <option value="LOGISTICA" <?= (($usuario["PERFIL"]=="LOGISTICA")?'selected':'') ?>>LOGISTICA</option>
                <option value="ADMINISTRADOR" <?= (($usuario["PERFIL"]=="ADMINISTRADOR")?'selected':'') ?>>ADMINISTRADOR</option>
              </select>
            </div>
            <div class="col-md">
              <label class="labels">Matrícula Winthor</label>
              <input name="MATRICULA" value="<?= $usuario["MATRICULA"] ?>" type="text" class="form-control" placeholder="MATRICULA" autocomplete="off">
            </div>
            <div class="col-md">
              <label class="labels">Código RCA</label>
              <input name="CODUSUR" value="<?= $usuario["CODUSUR"] ?>" type="text" class="form-control" placeholder="CODUSUR" autocomplete="off">
            </div>
            <div class="col-md">
              <label class="labels">RCA (Loja Online)</label>
              <input name="CODUSUR2" value="<?= $usuario["CODUSUR2"] ?>" type="text" class="form-control" placeholder="Loja Online" autocomplete="off">
            </div>
            <div class="col-md">
              <label class="labels">Código Cliente</label>
              <input name="CODCLI" value="<?= $usuario["CODCLI"] ?>" type="text" class="form-control" placeholder="CODCLI" autocomplete="off">
            </div>
            <div class="mt-3">
              <div class="d-flex justify-content-between">
                <a href="index.php?op=11&acao=limparLista&aba=usuarios" type="button" class="btn btn-secondary"><i class="fa-solid fa-times"></i> Cancelar</a>
                <button type="submit" name="acao" value="usuario_atualizar" class="btn btn-success"><i class="fa-solid fa-save"></i> Salvar</button>
              </div>
            </div>
          </form>
          
        </div>


      </div>

    </main>
  </div><!-- row -->
</div><!-- container-fluid -->