<div class="container-fluid mt-5 text-sm">
	<div class="row">
		<?php 
		require_once('pages/logistica/function.php'); 
		require_once('pages/logistica/controller.php'); 
		require_once('pages/logistica/sidebar.php'); 
		require_once('pages/logistica/modalAddCategoria.php'); 
		require_once('pages/logistica/modalAddSubcategoria.php'); 
		require_once('pages/logistica/modalAddGrupo.php'); 
		require_once('pages/logistica/modalAddSubgrupo.php'); 
		?>
		<main class="col ms-sm-auto px-3">
			<div class="row">
				<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
					<h1 class="h2">
						<i class="fa-regular fa-book-atlas"></i> Fichas Técnica
					</h1>
					<div class="btn-group float-end">
						<button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalAddCategoria" title="Adicionar Categoria"><i class="fa-solid fa-plus"></i> Categoria</button>
						<button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalAddSubcategoria" title="Adicionar Sub Categoria"><i class="fa-solid fa-plus"></i> Sub Categoria</button>
						<button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalAddGrupo" title="Adicionar Grupo"><i class="fa-solid fa-plus"></i> Grupo</button>
						<button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalAddSubgrupo" title="Adicionar Sub Grupo"><i class="fa-solid fa-plus"></i> Sub Grupo</button>
					</div>
				</div>
			</div>

			<div class="card">
				<div class="card-header">
					<form action="index.php" method="POST">
						<input type="hidden" name="op" value="<?=$dados['op']?>">
						<input type="hidden" name="aba" value="<?=$dados['aba']?>">

						<div class="row g-3">

							<div class="col">
								<label class="form-label">NUMORIGINAL</label>
								<input type="text" name="NUMORIGINAL" class="form-control" autofocus autocomplete="off" placeholder="Part Number">
							</div>

							<div class="col">
								<label class="form-label">Categoria</label>
								<select name="CATEGORIAID" id="CATEGORIAID" class="form-select select2">
									<option value="ALL">Selecione uma Categoria</option>
									<?php  
									if ($categorias = listarCategorias()) {
										foreach ($categorias as $key => $value) {
											echo '<option value="'.$value['CATEGORIAID'].'">'.$value['CATEGORIA'].'</option>';
										}
									}
									?>
								</select>
							</div>

							<div class="col">
								<label class="form-label">Sub Categoria</label>
								<select name="SUBCATEGORIAID" id="SUBCATEGORIAID" class="form-select select2">
									<option value="ALL">Selecione uma Sub Categoria</option>
								</select>
							</div>

							<div class="col">
								<label class="form-label">Grupo</label>
								<select name="GRUPOID" id="GRUPOID" class="form-select select2">
									<option value="ALL">Selecione um Grupo</option>
								</select>
							</div>

							<div class="col">
								<label class="form-label">Sub Grupo</label>
								<select name="SUBGRUPOID" id="SUBGRUPOID" class="form-select select2">
									<option value="ALL">Selecione um Sub Grupo</option>
								</select>
							</div>

							<div class="col-1">
								<div class="d-grid gap-2">
								<button class="btn btn-secondary mt-4" type="submit" name="acao" value="pesquisaProduto"><i class="fa-solid fa-search"></i></button>
								</div>
							</div> 

						</div>
					</form>
				</div>

				<div class="table-responsive mt-4 m-2">
					<table id="tb_default" class="table table-bordered table-striped table-hover" style="width: 100%">
						<thead>
							<tr>
								<th>NUMORIGINAL</th>
								<th>Categoria</th>
								<th>Sub Categoria</th>
								<th>Grupo</th>
								<th>Sub Grupo</th>
								<th width="10%">Ações</th>
							</tr>
						</thead>

						<tbody>

						<?php if ($_SESSION['produtos']): ?>
						<?php foreach ($_SESSION['produtos'] as $key => $value) : ?>

							<?php if ($value['CODPROD']<>""): ?>

								<?php 
								// varDump2($value);
								if ($value['QTETIQUETA'] == ""){
									$value['QTETIQUETA'] = 1;
								}
								?>
							 
								<tr>
								<td><?= ($key+1) ?></td>
								<td><?= $value['CODPROD'].'-'.$value['DV'] ?></td>
								<td><?= $value['NUMORIGINAL'] ?></td>
								<td><?= $value['DESCRICAO'] ?></td>
								<td><?= $value['MARCA'] ?></td>
								<td><?= $value['LOCACAO'] ?></td>
								<td><?= $value['VIDE'] ?></td>
								<td><?= $value['QTSALDO'] ?></td>
								<td><?= $value['QTRESERVADA'] ?></td>
								<td><?= $value['QTETIQUETA'] ?></td>
								<td>
									<div class="btn-group m-0">
										<a class="btn btn-outline-primary py-1 px-2" href="index.php?op=131&acao=modalQTETIQUETA&key=<?= $key ?>&CODPROD=<?= $value['CODPROD'] ?>&DV=<?= $value['DV'] ?>&QTETIQUETA=<?= $value['QTETIQUETA'] ?>" title="Editar"><i class="fa fa-edit"></i></a>
										<a class="btn btn-outline-secondary py-1 px-2" target="_blanck" href="pages/logistica/produto-extrato.php?CODPROD=<?= $value['CODPROD'] ?>" title="Extrato"><i class="fa fa-file"></i> </a>
										<a class="btn btn-outline-secondary py-1 px-2"href="index.php?op=131&acao=produto_modalAlterarLocacao&CODPROD=<?= $value['CODPROD'] ?>&LOCACAO=<?= $value['LOCACAO'] ?>" title="Alterar Locação"><i class="fa fa-retweet"></i> </a>
										<a class="btn btn-outline-secondary py-1 px-2" href="index.php?op=131&acao=historicoLocacao&CODPROD=<?= $value['CODPROD'] ?>" title="Histórico"><i class="fa fa-clock"></i> </a>
										<a class="btn btn-outline-danger py-1 px-2" href="index.php?op=131&acao=excluirItem&key=<?=$key?>" title="Remover desta lista"><i class="fa fa-ban"></i></a>
									</div>
								</td>
								</tr>
								
							<?php endif ?>
						
						<?php endforeach; ?>
						<?php endif ?>

						</tbody>

					</table>
				</div>

			</div>
		</main>
	</div><!-- row -->
</div><!-- container-fluid -->


<script type="text/javascript" src="pages/logistica/function.js"></script>
<script type="text/javascript">

// Assuming your select element has an id of 'mySelect'
const catetoriaID = document.getElementById('CATEGORIAID');

catetoriaID.addEventListener("change", function() {
	var categoriaIndex = catetoriaID.selectedIndex;
	var categoriaOption = catetoriaID.options[categoriaIndex];
	var categoria_id = categoriaOption.value;
	console.log('categoria_id:', categoria_id);

	if (categoria_id == "ALL") {
		const subcategoriaElement = document.getElementById('SUBCATEGORIAID');
		subcategoriaElement.innerHTML = ''; // Clear existing options (optional)

		// Add a default "Select an option" if desired
		defaultOption = document.createElement('option');
		defaultOption.value = '';
		defaultOption.textContent = 'Selecione uma Sub Categoria';
		subcategoriaElement.appendChild(defaultOption); 
	}

	// Instanciar o objeto XMLHttpRequest
	var xhr = new XMLHttpRequest();
	xhr.open('GET', 'pages/logistica/ajax_functions.php?acao=listaSubcategorias&categoria_id=' + encodeURIComponent(categoria_id), true);

	// Definir o que fazer quando a resposta do servidor for recebida
	xhr.onload = function() {
		if (xhr.status >= 200 && xhr.status < 400) {
			// Sucesso!
			var response = JSON.parse(xhr.responseText); // Converte a resposta JSON em objeto
			console.log(response);

			if (response.status == 'sucesso') {}
				const subcategoriaElement = document.getElementById('SUBCATEGORIAID');
				subcategoriaElement.innerHTML = ''; // Clear existing options (optional)

				// Add a default "Select an option" if desired
				defaultOption = document.createElement('option');
				defaultOption.value = '';
				defaultOption.textContent = 'Selecione uma Sub Categoria';
				subcategoriaElement.appendChild(defaultOption); 

				for (const key in response.data) {
					console.log(key, response.data[key]);
					option = document.createElement('option');
					option.value = response.data[key].SUBCATEGORIAID; // Assuming 'value' property in your data
					option.textContent = response.data[key].SUBCATEGORIA; // Assuming 'text' property in your data
					subcategoriaElement.appendChild(option);
				}

		} else {
				// Erro na requisição
				console.error('A requisição falhou.');
		}
	};

	// Enviar a requisição ao servidor
	xhr.send();

});


</script>