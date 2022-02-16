$(document).ready(function(){
    $('#linkNuevo').on('click', function(){
        $('#campoOpcion').val("AGREGAR");
        $('.datosusuario').show();
        $('#txtNombre').val('');
        $('#txtUsuario').val('');
        $('#txtPass').val('');
        $('#btnAgregar').html('AGREGAR');

    });

    $('#linkCerrarNuevo').on('click', function(){
        $('.datosusuario').hide();
    })
});

function editarUsuario(valor){

    $('.overlay').show();
    $('.modal').show();

    var formData = {
        'idusuario': valor,
        'campoOpcion': 'RECUPERAR'
    };
    $.ajax({
        type: 'POST',
        url: 'usuariospost.php',
        data: formData,
        dataType: 'json',
        encode: true
    })
        .done(function(data){
            $('#campoOpcion').val("EDITAR");
            $('#campoID').val(data[0]['UsuarioID']);
            $('#txtNombre').val(data[0]['Nombre']);
            $('#txtUsuario').val(data[0]['Usuario']);
            $('#btnAgregar').html('GUARDAR');
            $('.datosusuario').show();
            $('.overlay').hide();
            $('.modal').hide();
        })
        .fail(function(data){

        })
}

function borrarUsuario(valor){
    $('#campoID').val(valor);


}