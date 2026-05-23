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
            <i class="fa-solid fa-user"></i> Meu Perfil
          </h1>
        </div>
      </div>

      <div class="row mt-3">
        <?php 
          // varDump2($dados);
          if (isset($_SESSION['login']['IDUSUARIO']) && !empty($_SESSION['login']['IDUSUARIO'])) {
            $usuario = buscaUsuarioId($_SESSION['login']['IDUSUARIO']);
            // varDump2($usuario);
            include ('pages/admin/usuario_modalAvatar.php'); 
            include ('pages/admin/usuario_modalAlterarSenha.php'); 
          } else {
            exibemensagem("IDUSUARIO inválido ou não informado!");
            redireciona("index.php?op=11&acao=limparLista&aba=usuarios");
          }
        ?>

        <div class="col-2">
          <div style="text-align: center;">
            <img class="rounded-circle border border-dark" width="100px" src="<?=@DIR_IMG.''.$usuario['AVATAR']?>">
            <br><small class="text-muted">Cadastrado em: <?= formataDataOracleToBr($usuario['DTCADASTRO']) ?></small>
            <br><small class="text-muted">Último acesso: há <?= diferencaTempo($usuario['DTULTLOGIN']) ?></small>
            <div class="btn-group-vertical mt-2">
              <button type="button" data-bs-toggle="modal" data-bs-target="#usuario_modalAvatar" class="btn btn-xs btn-info d-flex align-items-center">
                <i class="fa-solid fa-image mx-1"></i>
                Mudar Imagem
              </button>
            </div>
          </div>
        </div>


        <div class="col-10">
          <div class="row">
            <input type="hidden" name="op" value="11">

            <div class="col-md-8 p-2">
              <label class="labels">Nome</label>
              <input name="NOME" value="<?= $usuario["NOME"] ?>" type="text" class="form-control" placeholder="NOME COMPLETO" disabled autocomplete="off">
            </div>
            <div class="col-md-4 p-2">
              <label class="labels">Login</label>
              <input name="LOGIN" value="<?= $usuario["LOGIN"] ?>" type="text" class="form-control" placeholder="LOGIN" disabled autocomplete="off">
            </div>
            <div class="col-md-8 p-2">
              <label class="labels">E-mail</label>
              <input name="EMAIL" value="<?= $usuario["EMAIL"] ?>" type="email" class="form-control" placeholder="EMAIL" disabled autocomplete="off">
            </div>
            <div class="col-md-4 p-2">
              <label class="labels">Perfil</label>
              <select name="PERFIL" class="form-select" disabled>
                <option value="CLIENTE" <?= (($usuario["PERFIL"]=="CLIENTE")?'selected':'') ?>>CLIENTE</option>
                <option value="VENDEDOR" <?= (($usuario["PERFIL"]=="VENDEDOR")?'selected':'') ?>>VENDEDOR</option>
                <option value="LOGISTICA" <?= (($usuario["PERFIL"]=="LOGISTICA")?'selected':'') ?>>LOGISTICA</option>
                <option value="ADMINISTRADOR" <?= (($usuario["PERFIL"]=="ADMINISTRADOR")?'selected':'') ?>>ADMINISTRADOR</option>
              </select>
            </div>
            <div class="col-md p-2">
              <label class="labels">Matrícula Winthor</label>
              <input value="<?= $usuario["MATRICULA"] ?>" type="text" class="form-control" placeholder="MATRICULA" autocomplete="off" disabled>
            </div>
            <div class="col-md p-2">
              <label class="labels">Código RCA</label>
              <input value="<?= $usuario["CODUSUR"] ?>" type="text" class="form-control" placeholder="CODUSUR" autocomplete="off" disabled>
            </div>
            <div class="col-md p-2">
              <label class="labels">Código RCA (Loja Online)</label>
              <input value="<?= $usuario["CODUSUR2"] ?>" type="text" class="form-control" placeholder="CODUSUR Online" autocomplete="off" disabled>
            </div>
            <div class="col-md p-2">
              <label class="labels">Código Cliente</label>
              <input value="<?= $usuario["CODCLI"] ?>" type="text" class="form-control" placeholder="CODCLI" autocomplete="off" disabled>
            </div>
          
          </div>
        </div>


      </div>

    </main>
  </div><!-- row -->
</div><!-- container-fluid -->