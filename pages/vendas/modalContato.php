<!-- Modal -->
<div class="modal fade" id="modalContato" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h5 class="modal-title" id="exampleModalLabel">Informe os dados do contato</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form name="form-contato" role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="op" value="62">
        <input type="hidden" name="acao" value="defineContato">
        <div class="modal-body">
          <div class="mb-3">
            <input type="text" name="CONTATO" class="form-control" autocomplete="off" required placeholder="Nome do Contato" value="<?=($_SESSION['ORCAMENTO']['CAB']['CONTATO']!="")?$_SESSION['ORCAMENTO']['CAB']['CONTATO']:""?>">
          </div>
          <div class="mb-3">
            <input type="text" name="TELCELULAR" id="TELCELULAR" class="form-control item" autocomplete="off" required placeholder="Telefone Celular" value="<?=($_SESSION['ORCAMENTO']['CAB']['TELCELULAR']!="")?$_SESSION['ORCAMENTO']['CAB']['TELCELULAR']:""?>">
          </div>
          <div class="mb-3">
            <input type="text" name="TELFIXO" id="TELFIXO" class="form-control" autocomplete="off" placeholder="Telefone Fixo" value="<?=($_SESSION['ORCAMENTO']['CAB']['TELFIXO']!="")?$_SESSION['ORCAMENTO']['CAB']['TELFIXO']:""?>">
          </div>
          <div class="mb-3 float-md-end">
            <button type="submit" class="btn btn-primary">Salvar</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>