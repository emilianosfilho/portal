<div class="modal fade modal-primary" id="usuario_modalNovo" tabindex="-1" role="dialog" aria-labelledby="labelusuario_modalNovo" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h4 class="modal-title" id="labelusuario_modalNovo">
          <i class="fa fa-plus"></i> Novo Usuário
        </h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form class="row" action="index.php" method="POST">
          <input type="hidden" name="op" value="11">

          <div class="col-md-8">
            <label class="labels">Nome</label>
            <input name="NOME" type="text" class="form-control" placeholder="NOME COMPLETO" required autocomplete="off">
          </div>
          <div class="col-md-4">
            <label class="labels">Login</label>
            <input name="LOGIN" type="text" class="form-control" placeholder="LOGIN" required autocomplete="off">
          </div>
          <div class="col-md-8">
            <label class="labels">E-mail</label>
            <input name="EMAIL" type="email" class="form-control" placeholder="EMAIL" required autocomplete="off">
          </div>
          <div class="col-md-4">
            <label class="labels">Perfil</label>
            <select name="PERFIL" class="form-select" require>
              <option value="CLIENTE">CLIENTE</option>
              <option value="VENDEDOR">VENDEDOR</option>
              <option value="LOGISTICA">LOGISTICA</option>
              <option value="ADMINISTRADOR">ADMINISTRADOR</option>
            </select>
          </div>
          <div class="col-md">
            <label class="labels">Matrícula Winthor</label>
            <input name="MATRICULA" type="text" class="form-control" placeholder="MATRICULA" autocomplete="off">
          </div>
          <div class="col-md">
            <label class="labels">Código RCA</label>
            <input name="CODUSUR" type="text" class="form-control" placeholder="CODUSUR" autocomplete="off">
          </div>
          <div class="col-md">
            <label class="labels">RCA (Loja Online)</label>
            <input name="CODUSUR2" type="text" class="form-control" placeholder="Loja Online" autocomplete="off">
          </div>
          <div class="col-md">
            <label class="labels">Código Cliente</label>
            <input name="CODCLI" type="text" class="form-control" placeholder="CODCLI" autocomplete="off">
          </div>
          <div class="mt-3">
            <div class="d-flex justify-content-between">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fa-solid fa-times"></i> Cancelar</button>
              <button type="submit" name="acao" value="usuario_cadastrar" class="btn btn-success"><i class="fa-solid fa-save"></i> Salvar</button>
            </div>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>