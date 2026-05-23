<!-- Modal -->
<div class="modal fade" id="modalAtendimento" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h5 class="modal-title" id="exampleModalLabel">Selecione a Forma de Atendimento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-sm-center">
        <form name="form-atentimento" role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="op" value="62">
          <input type="hidden" name="acao" value="defineAtendimento">
          <div class="btn-group" >
            <button type="submit" class="btn btn-primary <?=($_SESSION['ORCAMENTO']['CAB']["ATENDIMENTO"]=="WHATSAPP")?"active":""?>" name="ATENDIMENTO" value="WHATSAPP">WHATSAPP</button>
            <button type="submit" class="btn btn-primary <?=($_SESSION['ORCAMENTO']['CAB']["ATENDIMENTO"]=="BALCAO")?"active":""?>" name="ATENDIMENTO" value="BALCAO">BALCAO</button>
            <button type="submit" class="btn btn-primary <?=($_SESSION['ORCAMENTO']['CAB']["ATENDIMENTO"]=="EMAIL")?"active":""?>" name="ATENDIMENTO" value="EMAIL">E-MAIL</button>
            <button type="submit" class="btn btn-primary <?=($_SESSION['ORCAMENTO']['CAB']["ATENDIMENTO"]=="TELEFONE")?"active":""?>" name="ATENDIMENTO" value="TELEFONE">TELEFONE</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>