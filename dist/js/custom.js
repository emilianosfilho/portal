$(document).ready(function () {
    $('.datepicker').datepicker({
        format: "dd/mm/yyyy",
        language: "pt-BR",
        daysOfWeekDisabled: "0",
        autoclose: true,
        todayHighlight: true
    });
    $('#sandbox-container .input-group.date').datepicker({
        format: "dd/mm/yyyy",
        language: "pt-BR",
        daysOfWeekDisabled: "0",
        autoclose: true,
        todayHighlight: true
    });
    $('#tb_contatos').DataTable();
    $('#tb_default').DataTable();
    $('#tb_default1').DataTable({
        "lengthChange": false,
        "info": false,
    });
    $('#tb_default2').DataTable();
    $('#tb_ficha').DataTable({
         "order": [ 0, 'desc' ]
    });

    $('#tb_order_desc').DataTable({
        "order": [ 0, 'desc' ]
    });
    $('#tb_cliente').DataTable({
        "order": [ 2, 'asc' ]
    });
    $('#tb_clear').DataTable({
        scrollX: true,
        "info": true,
        "ordering": false,
        "searching": false,
        "paging": false,
        "order": [[1, 'desc'], [0, 'asc']],
    });
    $('#tb_produtos').DataTable({
        "info": false,
        "searching": false,
        "paging": false,
        "order": [0, 'asc'],
    });
    $('#tb_searching').DataTable({
        scrollX: true,
        "info": true,
        "ordering": false,
        "searching": true,
        "paging": false,
    });
    $('#tb_full').DataTable({
        scrollX: true,
        "info": true,
        "ordering": true,
        "paging": false,
        "order": [0, 'asc'],
    });
    $('#tb_orcamento').DataTable({
        scrollX: true,
        "info": false,
        "ordering": true,
        "paging": false,
        "order": [0, 'desc'],
    });
    $('#tb_itensOrcamento').DataTable({
        scrollX: true,
        "info": false,
        "searching": false,
        "ordering": true,
        "paging": false,
        "order": [0, 'asc'],
    });
    $('#modalNovaSenha').modal('show');
    $('#modalCotacao').modal('show');
    $('#modalNovoContato').modal('show');
    $('#modalVendas').modal('show');
    $('#modalCompras').modal('show');
    $('#modalInformacoes').modal('show');
    $('#modalAlterarLocacao').modal('show');
    $('#modalHistLocacao').modal('show');
    $('#modalEditarCheckin').modal('show');
    $('#modalEditarCheckout').modal('show');
    $('#modalQTETIQUETA').modal('show');
    $('#modalMarcaEditar').modal('show');
    

    $('#myModal0').modal('show');
    $('#myModal1').modal('show');
    $('#myModal2').modal('show');
    $('#myModal3').modal('show');
    $('#myModal4').modal('show');
    $('#myModal5').modal('show');
    $('#TELCELULAR').mask('(00)00000-0000');
    $('#TELFIXO').mask('(00)0000-0000');
    
    // var toastID = document.getElementById('toast0')
    // var toast = new bootstrap.Toast(toastID)
    // toast.show()
    
    // var toastID = document.getElementById('toast1')
    // var toast = new bootstrap.Toast(toastID)
    // toast.show()
    
    // var toastID = document.getElementById('toast2')
    // var toast = new bootstrap.Toast(toastID)
    // toast.show()


    
    $( '#single-select-field' ).select2( {
        theme: "bootstrap-5",
        width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
        selectionCssClass: "select2--small",
        dropdownCssClass: "select2--small",
    } );

    $( '.select2').select2( {
        theme: "bootstrap-5",
        width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
    } );

    $('#IDMONTADORA_NEW').select2({
        dropdownParent: "#equip_modalEquipamento",
        theme: 'bootstrap-5'
    });

    $('#IDTIPOEQUIP_NEW').select2({
        dropdownParent: "#equip_modalEquipamento",
        theme: 'bootstrap-5'
    });

    $('#select2Motivo').select2({
        dropdownParent: "#precificacao-modalEditarPreco",
        theme: 'bootstrap-5'
    });

    $('#select2MAQUINA').select2({
        dropdownParent: "#modalMaquina",
        theme: 'bootstrap-5'
    });

    $('#select2Cobranca').select2({
        dropdownParent: "#modalCobrança",
        theme: 'bootstrap-5'
    });
    
    $( '#select2PlPag' ).select2( {
        dropdownParent: "#modalPlPag",
        theme: "bootstrap-5",
        selectionCssClass: "select2--small",
        dropdownCssClass: "select2--small",
    });
    $('#select2FornecCheckin').select2( {
        dropdownParent: "#modalPesquisaFornecedor",
        theme: "bootstrap-5",
        selectionCssClass: "select2--small",
        dropdownCssClass: "select2--small",
    });
    $('#select2ClienteCheckout').select2( {
        dropdownParent: "#modalPesquisaCliente",
        theme: "bootstrap-5",
        selectionCssClass: "select2--small",
        dropdownCssClass: "select2--small",
    });
    $( '#IDEQUIPMONTADORA' ).select2( {
        dropdownParent: "#modalEquipamentoEditar",
        theme: "bootstrap-5",
    });
    $( '#IDEQUIPTIPO' ).select2( {
        dropdownParent: "#modalEquipamentoEditar",
        theme: "bootstrap-5",
    });
    $( '#IDEQUIPMONTADORA' ).select2( {
        dropdownParent: "#modalNovoEquipamento",
        theme: "bootstrap-5",
    });
    $( '#IDEQUIPTIPO' ).select2( {
        dropdownParent: "#modalNovoEquipamento",
        theme: "bootstrap-5",
    });
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    })


    $( '#ID_TIPO' ).select2( {
        dropdownParent: "#modalAddChamado",
        theme: "bootstrap-5",
    });
    $( '#ID_CATEGORIA' ).select2( {
        dropdownParent: "#modalAddChamado",
        theme: "bootstrap-5",
    });
    $( '#ID_USUARIO' ).select2( {
        dropdownParent: "#modalAddChamado",
        theme: "bootstrap-5",
    });

    $('#IDMONTADORA').select2({
        dropdownParent: "#modalMaquina",
        theme: 'bootstrap-5'
    });
    $('#EQUIPAMENTO').select2({
        dropdownParent: "#modalMaquina",
        theme: 'bootstrap-5',
    }); 

    $('#M_IDMONTADORA').select2({
        dropdownParent: "#equip_modalEquipamento",
        theme: 'bootstrap-5'
    });
    $('#M_IDTIPOEQUIP').select2({
        dropdownParent: "#equip_modalEquipamento",
        theme: 'bootstrap-5'
    });
    $('#select2codmarca').select2({
        dropdownParent: "#produto_modalEditarProd",
        theme: 'bootstrap-5'
    }); 
    $('#select2depto').select2({
        dropdownParent: "#produto_modalEditarProd",
        theme: 'bootstrap-5'
    }); 
    $('#select2secao').select2({
        dropdownParent: "#produto_modalEditarProd",
        theme: 'bootstrap-5'
    });  

    
});