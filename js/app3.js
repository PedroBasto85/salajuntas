$(document).ready(function(){
    $('#linkNuevo').on('click', function(){
        $('#campoOpcion').val("AGREGAR");
        $('.datosusuario').show();
        $('#txtNombre').val('');
        $('#txtDireccion').val('');
        $('#txtTelefono').val('');
        $('#txtCorreo').val('');
        $('#txtWeb').val('');
        $('#txtLogo').val('');
        $('#btnAgregar').html('AGREGAR');

    });

    $('#linkCerrarNuevo').on('click', function(){
        $('.datosusuario').hide();
    });

    $('#txtLogo').on('click', function(e){
        $('#fileLogo').click();
        e.preventDefault();
    });

    $("input[name='fileLogo']").change(function(e) {
            $('#txtLogo').val(e.target.files[0].name);
    });

});

function editarEmpresa(valor){

    $('.overlay').show();
    $('.modal').show();

    var formData = {
        'idrazonsocial': valor,
        'campoOpcion': 'RECUPERAR'
    };
    $.ajax({
        type: 'POST',
        url: 'empresaspost.php',
        data: formData,
        dataType: 'json',
        encode: true
    })
        .done(function(data){
            $('#campoOpcion').val("EDITAR");
            $('#campoID').val(data[0]['RazonSocialID']);
            $('#txtNombre').val(data[0]['Nombre']);
            $('#txtDireccion').val(data[0]['Direccion']);
            $('#txtTelefono').val(data[0]['Telefono']);
            $('#txtCorreo').val(data[0]['Correo']);
            $('#txtWeb').val(data[0]['SitioWeb']);
            $('#txtLogo').val(data[0]['Logo'].split('/').pop());
            $('#btnAgregar').html('GUARDAR');
            $('.datosusuario').show();
            $('.overlay').hide();
            $('.modal').hide();
        })
        .fail(function(data){
            alert('error recuperando datos');
        })
}

function borrarEmpresa(valor){
    $('#campoID').val(valor);


}