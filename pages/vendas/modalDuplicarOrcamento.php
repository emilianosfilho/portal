

<!-- Modal -->
<div class="modal fade" id="modalDuplicarOrcamento" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h5 class="modal-title" id="exampleModalLabel">
          <i class="fa fa-copy"></i> Duplicar Orçamento <?= $_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO'] ?>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form name="form-ordem" role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="op" value="62">
        <input type="hidden" name="acao" value="duplicarOrcamento">
        <div class="modal-body">
          <div class="row mb-3">
            <label for="exampleInputEmail1" class="form-label">Cliente</label>
            <div class="col-3">
              <input type="text" aria-label="AjaxCodcli" class="form-control" name="AjaxCodcli" id="AjaxCodcli" value="<?= $_SESSION['ORCAMENTO']['CLIENTE']['CODCLI'] ?>">
            </div>
            <div class="col-9">
              <input type="text" aria-label="AjaxNomeCliente" class="form-control" name="AjaxNomeCliente" id="AjaxNomeCliente" value="<?= $_SESSION['ORCAMENTO']['CLIENTE']['CLIENTE'] ?>">
            </div>
          </div>
          <div class="row mb-3">
            <label for="exampleInputEmail1" class="form-label">Vendedor</label>
            <div class="col-3">
              <input type="text" aria-label="AjaxCodusur" class="form-control" name="AjaxCodusur" id="AjaxCodusur" value="<?= $_SESSION['ORCAMENTO']['VENDEDOR']['CODUSUR'] ?>">
            </div>
            <div class="col-9">
              <input type="text" aria-label="AjaxVendedorNome" class="form-control" name="AjaxVendedorNome" id="AjaxVendedorNome" value="<?= $_SESSION['ORCAMENTO']['VENDEDOR']['NOME'] ?>">
            </div>
          </div>
          <div class="mb-3 float-md-end">
            <button type="submit" class="btn btn-primary">Duplicar agora</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

