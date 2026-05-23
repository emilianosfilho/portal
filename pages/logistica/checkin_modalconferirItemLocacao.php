<div class="modal fade" id="checkin_modalconferirItemLocacao" role="dialog" aria-labelledby="labelmodalAlteraSenha" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header bg-secondary text-white">
				<h4 class="modal-title" >
					<i class="fas fa-tasks"></i> Conferir Item Locação - CHECKIN
				</h4>
			</div>

			<?php  
				// $debug = true;
				if($debug) varDump2("insereModalconferirItemLocacaoCHECKIN");
				if($debug) varDump2($dados);
				
				$IDCHECKIN = $dados['IDCHECKIN'];
				if (strpos($dados['CODPROD'], '*')) {
					$QTCONFERIDA  = reset(explode('*', $dados['CODPROD']));
					$CODPROD      = end(explode('*', $dados['CODPROD']));
				} else {
					$QTCONFERIDA  = 1;
					$CODPROD      = intval($dados['CODPROD']);
				}
				if($debug) varDump2("IDCHECKIN: ".$IDCHECKIN);
				if($debug) varDump2("CODPROD: ".$CODPROD);
				if($debug) varDump2("QTCONFERIDA: ".$QTCONFERIDA);

				$produto = buscaDadosProduto($CODPROD);
				if($debug) varDump2($produto);
				// die();
			?>

			<form role="form" class="STYLE-NAME" action="index.php" method="POST" enctype="multipart/form-data">
				<input type="hidden" name="op" value="135">
				<input type="hidden" name="aba" value="entrada">
				<input type="hidden" name="IDCHECKIN" value="<?=$IDCHECKIN?>">
				<input type="hidden" name="CODPROD" value="<?=$CODPROD?>">

				<div class="modal-body text-md">
					<div class="my-1 row">
						<label class="col-sm-4 col-form-label">Codprod</label>
						<div class="col-sm-8">
							<input type="text" disabled class="form-control-plaintext" value="<?='W'.$produto['CODPROD'].'-'.$produto['DV']?>">
						</div>
					</div>
					<div class="my-1 row">
						<label class="col-sm-4 col-form-label">Descrição</label>
						<div class="col-sm-8">
							<input type="text" disabled class="form-control-plaintext" value="<?=$produto['DESCRICAO']?>">
						</div>
					</div>
					<div class="my-1 row">
						<label class="col-sm-4 col-form-label">Marca</label>
						<div class="col-sm-8">
							<input type="text" disabled class="form-control-plaintext" value="<?=$produto['MARCA']?>">
						</div>
					</div>
					<div class="my-1 row">
						<label class="col-sm-4 col-form-label">Qtd Conferida</label>
						<div class="col-sm-8">
							<input type="number" name="QTCONFERIDA" id="QTCONFERIDA" value="<?=$QTCONFERIDA?>" class="form-control" required autocomplete="off">
						</div>										
					</div>
					<div class="my-1 row">
						<label class="col-sm-4 col-form-label">Locação</label>
						<div class="col-sm-8">
							<input type="text" name="LOCACAO" id="LOCACAO" class="form-control" required autofocus autocomplete="off">
							</div>
						</div>
				</div>

				<div class="modal-footer d-flex justify-content-between">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
						<i class="fa fa-ban"></i> Cancelar
					</button>
					<button type="submit" class="btn btn-success" name="acao" value="checkin_conferirItem">
						<i class="fa fa-forward"></i> Avançar
					</button>
				</div>

			</form>	

		</div>
	</div>
</div>

<script type='text/javascript'>
	$(document).ready(function () { 
		$('#checkin_modalconferirItemLocacao').modal('show');
	});
	var myModal = document.getElementById('checkin_modalconferirItemLocacao')
	var myInput = document.getElementById('LOCACAO')
	myModal.addEventListener('shown.bs.modal', function () {
		myInput.focus()
	})
</script>