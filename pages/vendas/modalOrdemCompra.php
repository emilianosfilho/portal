<!-- Modal -->
<div class="modal fade" id="modalOrdemCompra" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h5 class="modal-title" id="exampleModalLabel">Informe a ordem de compra / Nr Pedido cliente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form name="form-ordem" role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="op" value="62">
        <input type="hidden" name="IDORCAMENTO" value="<?= $_SESSION['ORCAMENTO']['CAB']['IDORCAMENTO'] ?>">

        <div class="modal-body">
          <div class="mb-3">
            <input type="text" name="ORDEMDECOMPRA" class="form-control" autocomplete="off" maxlength="40" placeholder="Ordem de compra" value="<?=($_SESSION['ORCAMENTO']['CAB']['ORDEMDECOMPRA']!="")?$_SESSION['ORCAMENTO']['CAB']['ORDEMDECOMPRA']:""?>">
            <small class="mt-1">
              <ul class="list-unstyled ">
                <li><code>Máximo de 40 caracteres.</code></li>
                <li>Preencher o campo Ordem de compra quando:
                  <ul>
                    <li>O orçamento se tratar de uma pesquisa feita a partir de um pedido de compra do cliente.</li>
                  </ul>
                </li>
                <li>Atenção: Este campo será enviado para o Winthor.</li>
              </ul>
            </small>
          </div>
          <div class="mb-3 float-md-end">
            <button type="submit" name="acao" value="defineOrdemdecompra" class="btn btn-primary">Salvar</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>