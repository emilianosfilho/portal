<?php  
// $debug = true;

if($debug) varDump2($dados);

$validacao = validaProdutoEntrada($dados['CODPROD']);
if($debug) varDump2($validacao);

?>

<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h5 class="modal-title" id="staticBackdropLabel">Validação de Produto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
		<dl class="row">
		  <dt class="col-sm-3"><div class="float-end">CODPROD</div></dt>
		  <dd class="col-sm-9"><?= $validacao['CODPROD']?></dd>

		  <dt class="col-sm-3"><div class="float-end">PRODUTO</div></dt>
		  <dd class="col-sm-9"><?= $validacao['DESCRICAO']?></dd>
		
		  <dt class="col-sm-3"><div class="float-end">NUMORIGINAL</div></dt>
		  <dd class="col-sm-9"><?= $validacao['NUMORIGINAL']?></dd>
		
		  <dt class="col-sm-3"><div class="float-end">CODFAB</div></dt>
		  <dd class="col-sm-9"><?= $validacao['CODFAB']?></dd>
		
		  <dt class="col-sm-3"><div class="float-end">PCCODFABRICA</div></dt>
		  <dd class="col-sm-9"><?= $validacao['PCCODFABRICA']?></dd>
		
		  <dt class="col-sm-3"><div class="float-end">NCM</div></dt>
		  <dd class="col-sm-9"><?= $validacao['NCM'].'<br/>'.$validacao['NCM_DESCRICAO']?></dd>
		
		  <dt class="col-sm-3"><div class="float-end">CODFILIAL</div></dt>
		  <dd class="col-sm-9"><?= $validacao['CODFILIAL']?></dd>
		
		  <dt class="col-sm-3"><div class="float-end">TRIB_ENTRADA</div></dt>
		  <dd class="col-sm-9"><?= $validacao['TRIB_ENTRADA']?></dd>
		
		  <dt class="col-sm-3"><div class="float-end">FORNECEDOR</div></dt>
		  <dd class="col-sm-9">
		    <dl class="row">
		      <dt class="col-sm-4"><div class="float-end">CODFORNEC</div></dt>
		      <dd class="col-sm-8"><?= $validacao['CODFORNEC']?></dd>

		      <dt class="col-sm-4"><div class="float-end">FORNECEDOR</div></dt>
		      <dd class="col-sm-8"><?= $validacao['FORNECEDOR']?></dd>

		      <dt class="col-sm-4"><div class="float-end">TIPOFORNEC</div></dt>
		      <dd class="col-sm-8"><?= $validacao['TIPOFORNEC'].'- '.$validacao['DESCTIPOFORNEC']?></dd>
		    </dl>
		  </dd>
		</dl>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-info" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
	    $('#staticBackdrop').modal('show');
	});
</script>