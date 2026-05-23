$(document).ready(function () {
    $("input[name='AjaxCodcli']").keyup(function(){
        $.ajax({
          type: 'GET',
          url:  'pages/orcamentoColaborador/ajaxDuplicarOrcamento.php',
          data: {
              codcli: $("#AjaxCodcli").val()
          },
          success: function(data) 
          {
            $('#AjaxNomeCliente').val(data);
          }
        });
    });
    $("input[name='AjaxCodusur']").keyup(function(){
        $.ajax({
          type: 'GET',
          url:  'pages/orcamentoColaborador/ajaxDuplicarOrcamento.php',
          data: {
              codusur: $("#AjaxCodusur").val()
          },
          success: function(data) 
          {
            $('#AjaxVendedorNome').val(data);
          }
        });
    });
});