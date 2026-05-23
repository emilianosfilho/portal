<div class="container-fluid mt-5 text-sm">
	<div class="row">
	<?php 
	require_once('pages/logistica/function.php'); 
	require_once('pages/logistica/controller.php'); 
	require_once('pages/logistica/sidebar.php'); 
	require_once('pages/logistica/modalPesquisaCliente.php'); 
	?>
	<main class="col-11 ms-sm-auto px-3">
		<div class="row">
		<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
			<h1 class="h2">
			<i class="fa-solid fa-right-from-bracket"></i> Consulta Saídas
			</h1>
		</div>
		</div>

				
		<form class="row" action="index.php" method="POST">
			<input type="hidden" name="op" value="132">
			<input type="hidden" name="aba-saida">

			<div class="col-2">
				<label class="form-label">DATA INÍCIO</label>
				<div class="span5" id="sandbox-container">
				<div class="input-group date">
					<input name="DATAINI" type="text" class="form-control" value="<?=(isset($dados['DATAINI']))?$dados['DATAINI']:@date('d/m/Y')?>">
					<span class="input-group-addon btn btn-secondary"><i class="fa fa-th"></i></span>
				</div>         
				</div> 
			</div>

			<div class="col-2">
				<label class="form-label">DATA FIM</label>
				<div class="span5" id="sandbox-container">
				<div class="input-group date">
					<input name="DATAFIM" type="text" class="form-control" value="<?=(isset($dados['DATAFIM']))?$dados['DATAFIM']:@date('d/m/Y')?>">
					<span class="input-group-addon btn btn-secondary"><i class="fa fa-th"></i></span>
				</div>         
				</div> 
			</div>

			<div class="col">
				<label class="form-label">CHECK-OUT</label>
				<select name="STATUSCHECKOUT" class="form-select select2">
					<option value="ALL" <?=(($dados['STATUSCHECKOUT']=='ALL')?'selected':'')?> >Todos</option>
					<option value="NAOFINALIZADO" <?=(($dados['STATUSCHECKOUT']=='FINALIZADO')?'':'selected')?> >Não Finalizado</option>
					<option value="FINALIZADO" <?=(($dados['STATUSCHECKOUT']=='FINALIZADO')?'selected':'')?> >Finalizado</option>
				</select>
			</div>

			<div class="col">
				<label class="form-label">FATURAMENTO</label>
				<select name="POSICAO" class="form-select select2">
					<option value="ALL" <?=(($dados['POSICAO']=='ALL')?'selected':'')?> >Todos</option>
					<option value="NAOFATURADO" <?=(($dados['POSICAO']=='NAOFATURADO')?'':'selected')?> >Não Faturado</option>
					<option value="FATURADO" <?=(($dados['POSICAO']=='FATURADO')?'selected':'')?> >Faturado</option>
				</select>
			</div>

			<div class="col">
				<label class="form-label">CÓD. CLIENTE</label>
				<input type="text" name="CODCLI" class="form-control" value="<?=(isset($dados['CODCLI']))?$dados['CODCLI']:''?>" autocomplete="off" placeholder="CODCLI">
			</div>

			<div class="col">
				<label class="form-label">NÚM. PEDIDO</label>
				<input type="text" name="NUMPED" class="form-control" value="<?=(isset($dados['NUMPED']))?$dados['NUMPED']:''?>" autofocus autocomplete="off" placeholder="NUMPED / IDORCAMENTO">
			</div>

			<div class="col-1">
				<div class="d-grid gap-2">
				<button class="btn btn-secondary mt-4" type="submit" name="acao" value="pesquisaPedVenda"><i class="fa-solid fa-search"></i></button>
				</div>
			</div>

		</form>
		



		<?php if ($_SESSION['PEDVENDA']): ?>
		<div class="row">
			<div class="col-12">
				<table id="tblEditavel" class="table table-bordered table-striped table-hover mt-4 text-lg" style="width: 100%">
					<thead>
						<tr>
							<th>#</th>
							<th>Núm Pedido</th>
							<th>Núm Orçamento</th>
							<th>Data</th>
							<th>Cliente</th>
							<th>Vendedor</th>
							<th>Posição</th>
							<th>Check-out</th>
							<th>Conferente</th>
							<th>Espera</th>
							<th>Balcão</th>
							<th width="10%">Ações</th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($_SESSION['PEDVENDA'] as $key => $value) : ?>
							<tr>
							<td><?= ($key+1) ?></td>
							<td><?= $value['NUMPED'] ?></td>
							<td><?= $value['IDORCAMENTO'] ?></td>
							<td><?= formataDataOracletoBr($value['DATA']) ?></td>
							<td><?= $value['CODCLI'].'- '.$value['CLIENTE'] ?></td>
							<td><?= reset(explode(' ', $value['VENDEDOR'])) ?></td>
							<td>
								<?php 
									switch ($value['POSICAO']) {
										case 'FATURADO': 	echo '<span class="badge bg-success">FATURADO</span>';	break;
										case 'LIBERADO': 	echo '<span class="badge bg-primary">LIBERADO</span>';	break;
										case 'PENDENTE': 	echo '<span class="badge bg-primary">PENDENTE</span>';	break;
										case 'MONTADO': 	echo '<span class="badge bg-primary">MONTADO</span>';	break;
										case 'CANCELADO': echo '<span class="badge bg-danger">CANCELADO</span>';	break;
										case 'BLOQUEADO': echo '<span class="badge bg-danger">BLOQUEADO</span>';	break;
										default: 					echo '<span class="badge bg-secondary">'.$value['POSICAO'].'</span>';	break;
									} 
								?>
							</td>
							<td>
								<?php 
									if ($value['STATUSCHECKOUT'] == 'PENDENTE') {
										echo '<span class="badge bg-secondary">PENDENTE</span>';
									} else {
										if ($value['STATUSCHECKOUT'] == 'INCOMPLETO') {
											echo '<span class="badge bg-primary">INCOMPLETO</span>';
										} else {
											if ($value['STATUSCHECKOUT'] == 'FINALIZADO') {
												echo '<span class="badge bg-success">FINALIZADO</span>';
											} else {
												echo '<span class="badge bg-warning text-black">EM TRANSITO</span>';
											}
										}
									}
								?>
							</td>
							<td><?= (($value['CONFERENTE']<>"")?reset(explode(' ', $value['CONFERENTE'])):'') ?></td>
							<td><?= $value['MIN_ESPERA'] ?></td>
							<td><?php 
								if ($value['CLIENTEBALCAO'] == 'SIM') {
									echo '<span class="badge bg-danger">SIM</span>';
								} else {
									echo '<span class="badge bg-secondary">NÃO</span>';
								} 
							?></td>
							<td>
								<form action="index.php" method="POST">
									<input type="hidden" name="op" value="132">
									<input type="hidden" name="aba-saida">
									<input type="hidden" name="key" value="<?=$key?>">
									<input type="hidden" name="NUMPED" value="<?=$value['NUMPED']?>">
									<input type="hidden" name="NUMPEDCLI" value="<?=$value['NUMPEDCLI']?>">
									<input type="hidden" name="NUMPEDRCA" value="<?=$value['NUMPEDRCA']?>">
									<input type="hidden" name="IDORCAMENTO" value="<?=$value['IDORCAMENTO']?>">
									<input type="hidden" name="IDUSURCONFERENTE" value="<?=$value['IDUSURCONFERENTE']?>">
									<div class="btn-group">
										<button type="submit" name="acao" value="abrirCheckOut" class="btn btn-sm btn-outline-secondary" title="Abrir Check-out">
											<i class="fa-solid fa-list-check"></i>
										</button>
									</div>
								</form>
							</td>
							</tr>
					<?php endforeach; ?>
					</tbody>
				</table>

			</div>
		</div>

	<?php endif ?>

	</main>
	</div>
</div>