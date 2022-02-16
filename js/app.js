var mouseX;
var mouseY;
$(document).mousemove( function(e) {
    mouseX = e.pageX;
    mouseY = e.pageY;
});

$(document).ready(function(){
    $('#dp').datepicker({
        firstDay: 1,
        dateFormat: 'dd/mm/yy',
        dayNames : ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'],
        dayNamesMin : ['Do','Lu','Ma','Mi','Ju','Vi','Sa'],
        monthNames :['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
        onSelect: function (dateText, inst) {
            $('#fechaActual').html(dateText);
            var day1 = $("#dp").datepicker('getDate').getDate();
            var month1 = $("#dp").datepicker('getDate').getMonth() + 1;
            var year1 = $("#dp").datepicker('getDate').getFullYear();
            var fullDate =  $.datepicker.formatDate('yy-mm-dd',$(this).datepicker('getDate'));
            recuperarCitas(fullDate);
        }
    });
    $('#txtFecha').datepicker({
        firstDay: 1,
        dateFormat: 'dd/mm/yy',
        minDate : 0,
        dayNames : ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'],
        dayNamesMin : ['Do','Lu','Ma','Mi','Ju','Vi','Sa'],
        monthNames :['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre']
    });

    $('#fechaSelect').click(function(){
        $('#dp').datepicker("show");
   });

    $('#linkReservar').on('click', function(){
        $('.ocultar').hide();
        $('.down, .up').hide();
        $('.error').hide();
        $('#txtTitulo').val('');
        $('#txtFecha').val(fechaActual());
        $('#txtPersonas').val('');
        $('#txtHoraInicio').val('00:00');
        $('#txtHoraTermino').val('00:00');
        $('#txtComentarios').val('');
        $('.asistentes').hide();
        $('.agenda').slideDown();
        $('#listaGrupo li').first().addClass('activo');
        $('hiddenGrupo').val(1);
        $('#campoTemp').val('0');
    });


    $('#btnCerrar, #asalir').on('click', function(){
        $('.ocultar').show();
        $('.down, .up').show();
        $('.agenda').fadeOut();
    });

    $('#formAgendar').submit(function(event){
        event.preventDefault();
        return false;
    });

    $('.mensaje').tooltipster({
        theme: 'tooltipster-shadow'
    });

    $('#txtPersonas').focus(function(){
        if ($(".asistentes").is(":hidden")){
            recuperarGrupo(1);
        }
        $('.asistentes').show();
    });

    $('#txtTitulo, #txtFecha, #txtHoraInicio, #txtHoraTermino, #txtComentarios').focus(function(){
        $('.asistentes').hide();
    });


    $('.tablas tr').mouseover(function(){
        if ($(this).find('div').hasClass("cancelado")==false) {
            $(this).find('.borrar').show();
        }
    });
    $('.tablas tr').mouseout(function(){
        $(this).find('.borrar').hide();
    });

    /*$('.tablas tr').mouseleave(function(){
        $('.borrar').hide();
    });*/

    $('#linkAgregar').on('click',function(){
        actualizarGrupos($('#hiddenEmpresa').val());
        $('.lista').hide();
        $('.nuevo').show();
    });

    $('#linkMasGrupos').on('click',function(){
        $('.lista').hide();
        $('.nuevogrupo').show();
    });

    $('#linkCerrarAgregar').on('click',function(){
        $('.nuevo').hide();
        $('.lista').show();
    });

    $('#linkCerrarGrupo').on('click',function(){
        $('.nuevogrupo').hide();
        $('.lista').show();
    });

    $('#listaGrupo li label').on('click',function(){
        recuperarGrupo($(this).attr('id'));
        $("#listaGrupo li").removeClass('activo');
        $(this).parent().addClass('activo');
    });

    $('#listaGrupo li').mouseover(function(){
        if ($('#hiddenRol').val() == 1){
            $(this).find('.borrarGrupo').show();
        }
    });

    $('#listaGrupo li').mouseout(function(){
        $(this).find('.borrarGrupo').hide();
    });

});

function recuperarCitas(fecha){
    $('.overlay').show();
    $('.modal').show();
    var formData = {
        'fecha': fecha,
        'operacion': 'ACTUALIZAR'
    };
    $.ajax({
        type: 'POST',
        url: 'agendapost.php',
        data: formData,
        dataType: 'json',
        encode: true
    })
        .done(function(data){
            $('.overlay').hide();
            $('.modal').hide();
            //console.log(data);
            $('#tblAgendaDiaria > tbody').empty();
            if (data[0]['ReservacionID']==0){
                nuevafila = "<tr style='text-align: center'><td colspan='6'>Sin reservaciones programadas</td>";
                $('#tblAgendaDiaria').append(nuevafila);
            }else {
                $.each(data, function (i, item) {
                    reservacionid = data[i].ReservacionID;
                    usuario = data[i].Nombre + ' ';
                    nombreestado = data[i].NombreEstado + ' ';
                    fechaum = data[i].UM;
                    if (data[i].EstadoID == 1) {
                        clase = 'agendado';
                        claseletra = 'agendadoletra';
                    } else if (data[i].EstadoID == 2) {
                        clase = 'cancelado';
                        claseletra = 'canceladoletra';
                    } else if (data[i].EstadoID == 3) {
                        clase = 'encurso';
                        claseletra = 'encursoletra';
                    } else {
                        clase = 'finalizado';
                        claseletra = 'finalizadoletra';
                    }
                    titulo = "<div class='small-6 columns " + claseletra + "'><a href='listas.php?id=" + reservacionid + "' target='_blank' class='listasa'>" + data[i].Titulo + "</a></div>";
                    HoraInicio = "<div class='small-2 columns center letragrid letraDe'>" + data[i].HoraInicio + "</div>";
                    HoraTermino = "<div class='small-2 columns center letragrid color3'>" + data[i].HoraTermino + "</div>";
                    personas = "<div class='small-1 columns center letragrid'>" + data[i].Personas + "</div>";
                    borrar = "<div class='small-1 columns center'><a class='borrar' onclick='return borrarPrevio(" + reservacionid + ")'><img src='img/cerrar24.png'></a></div>";
                    nuevafila = "<tr class='mensaje' title='"+usuario+nombreestado+fechaum+"'>"+"<td colspan='5'><div class='"+ clase + " filaestado'><div class='row'>" + titulo + HoraInicio + HoraTermino + personas + borrar + "</div></div></td>"+"</tr>";
                    $('#tblAgendaDiaria').append(nuevafila);
                });
                $('.mensaje').tooltipster({
                    theme: 'tooltipster-shadow'
                });

                $('.tablas tr').mouseover(function(){
                    if ($(this).find('div').hasClass("cancelado")==false) {
                        $(this).find('.borrar').show();
                    }
                });
                $('.tablas tr').mouseout(function(){
                    $(this).find('.borrar').hide();
                });
            }
        })
        .fail(function(data){

        })

}

function agendarCita(){
    if (($('#txtEmpresa').val()==0) ||  ($('#txtTitulo').val()=='') || ($('#txtFecha').val()=='') || ($('#txtPersonas').val()=='') || ($('#txtHoraInicio').val()==0) || ($('#txtHoraTermino').val()==0)){
        $('.error').html('Faltan Datos');
        $('.error').show();
        return false;
    }else{
        $('.error').hide();
    }

    $('.overlay').show();
    $('.modal').show();

    var formData = {
        'titulo':$('#txtTitulo').val(),
        'fecha': $('#txtFecha').val(),
        'personas': $('#txtPersonas').val(),
        'horainicio':$('#txtHoraInicio').val(),
        'horatermino':$('#txtHoraTermino').val(),
        'usuarioid' : $('#campoUsuarioID').val(),
        'comentario': $('#txtComentarios').val(),
        'empresaid': $('#txtEmpresa').val(),
        'empresanombre': $('#txtEmpresa option:selected').text(),
        'idtemp': $('#campoTemp').val(),
        'operacion': 'AGENDAR'
    };
    $.ajax({
        type: 'POST',
        url: 'agendapost.php',
        data: formData,
        dataType: 'json',
        encode: true
    })
        .done(function(data){
            if (data['Error'] == ''){
                $('.error').html('Agendado');
                $('.error').show();
                fechahoy = fechaActual();

                if ($('#txtFecha').val() == fechahoy){
                    numeros = fechahoy.split('/');
                    fechaformateada = numeros[2] + '-' + numeros[1] + '-' + numeros[0];
                    recuperarCitas(fechaformateada);
                }
                $('.ocultar').show();
                $('#flechas').show();
                $('.agenda').slideUp(800);

            }else{
                $('.error').html(data['Error']);
                $('.error').show();
            }
            $('.overlay').hide();
            $('.modal').hide();
            actualizarnumero();

        })
        .fail(function(data){
            $('.overlay').hide();
            $('.modal').hide();
            alert('error en agenda');
        })
}

function borrarPrevio(valor){
    $('#campoReservacionID').val(valor);
    $('#divCancelar').css({'top':mouseY,'left':mouseX}).fadeIn('slow');
}

function borrarCita(){
    $('.overlay').show();
    $('.modal').show();
    var valor = $('#campoReservacionID').val();
    var comentario = $('#txtCancelacion').val();

    var formData = {
        'reservacionid': valor,
        'comentario': comentario,
        'operacion': 'BORRAR'
    };
    $.ajax({
        type: 'POST',
        url: 'agendapost.php',
        data: formData,
        dataType: 'json',
        encode: true
    })
        .done(function(data){
            fechahoy = $('#fechaActual').html();
            numeros = fechahoy.split('/');
            fechaformateada = numeros[2] + '-' + numeros[1] + '-' + numeros[0];
            recuperarCitas(fechaformateada);
            actualizarnumero();
            $('#divCancelar').hide();
        })
        .fail(function(){
            alert('Fallo al borrar la cita');
        });
}

function invitar(nombre,valor){
    //if ($("#checkContacto[value='" + valor + "']").is(":checked")){
    if($(nombre).is(":checked")){
        estado = 1;
    }else{
        estado = 0;
    }

    $('.overlay').show();
    $('.modal').show();
    var formData = {
        'contacto': valor,
        'idtemp': $('#campoTemp').val(),
        'estado':estado,
        'operacion': 'INVITAR'
    };
    $.ajax({
        type: 'POST',
        url: 'agendapost.php',
        data: formData,
        dataType: 'json',
        encode: true
    }).done(function(data){
        $('#campoTemp').val(data['idtemp']);
        $('#txtPersonas').val(data['cuantos']);
        $('.overlay').hide();
        $('.modal').hide();

    }).fail(function(data){

    })

}

function agregarGrupo(){
    $('.overlay').show();
    $('.modal').show();
    var formData = {
        'nombre':$('#txtNombreGrupo').val(),
        'empresa': $('#hiddenEmpresa').val(),
        'idusuario': $('#campoUsuarioID').val(),
        'operacion': 'GRUPOINSERTAR'
    };
    $.ajax({
        type: 'POST',
        url: 'agendapost.php',
        data: formData,
        dataType: 'json',
        encode: true
    }).done(function(data){
        $('#listaGrupo').append("<li value='"+data['id']+"'>"+"<label id='"+data['id']+"'>"+$('#txtNombreGrupo').val()+"</label>"+"<a class='borrarGrupo' style='float: right' onclick='return borrarGrupo("+data['id']+")'><img src='img/cerrar24.png' width='16px'></a>"+"</li>");
        $('#comboGrupo').append($('<option>', {
         value: data['id'],
         text: $('#txtNombreGrupo').val()
        }));

        $('#txtNombreGrupo').val('');
        $('.overlay').hide();
        $('.modal').hide();

        $('#listaGrupo li').mouseover(function(){
            if ($('#hiddenRol').val() == 1){
                $(this).find('.borrarGrupo').show();
            }
        });

        $('#listaGrupo li').mouseout(function(){
            $(this).find('.borrarGrupo').hide();
        });

        $('#listaGrupo li label').on('click',function(){
            recuperarGrupo($(this).attr('id'));
            $("#listaGrupo li").removeClass('activo');
            $(this).parent().addClass('activo');
        });
        redimensionar();

    }).fail(function(data){

    })
}

function borrarGrupo(valor){

    $('.overlay').show();
    $('.modal').show();
    var formData = {
        'grupo':valor,
        'idusuario': $('#campoUsuarioID').val(),
        'operacion': 'GRUPOBORRAR'
    };
    $.ajax({
        type: 'POST',
        url: 'agendapost.php',
        data: formData,
        dataType: 'json',
        encode: true
    }).done(function(data){
        $('.overlay').hide();
        $('.modal').hide();
        $("#listaGrupo>li[value="+valor+"]").remove();
        $('#listaGrupo li').first().addClass('activo');
        $('hiddenGrupo').val(1);
        recuperarGrupo(1);
    }).fail(function(data){
        alert('error al borrar grupo')
    });
}

function recuperarGrupo(valor){
    $('.overlay').show();
    $('.modal').show();
    $('#hiddenGrupo').val(valor);
    var formData = {
        'grupo': $('#hiddenGrupo').val(),
        'idusuario': $('#campoUsuarioID').val(),
        'idtemp': $('#campoTemp').val(),
        'operacion': 'RECUPERARCONTACTO'
    };
    $.ajax({
        type: 'POST',
        url: 'agendapost.php',
        data: formData,
        dataType: 'json',
        encode: true
    }).done(function(data){
        $('#listaContactoDet').empty();
        $('.error').hide();
        $.each(data, function (i, item) {
            id = data[i].ContactoID;
            nombre = data[i].Nombre;
            correo = data[i].Correo;
            if(data[i].Registro > 0){
                checado = 'checked'
            }else{
                checado = '';
            }
            namecheck='checkContacto'+id;
            linombre = "<li style='width: 40%; text-align: left'><label>" + nombre + "</label></li>";
            licorreo = "<li style='width: 40%; text-align: center'><label>" + correo + "</label></li>";
            licheck = "<li style='width: 18%; text-align: right'>"+
                "<div class='ajuste'><div class='roundedOne'>" +
                "<input type='checkbox' id='"+ namecheck +"' name='checkContacto' onclick='invitar(" + namecheck + ","  + id + ")' value='" + id + "' " + checado + " />" +
                "<label for='"+ namecheck +"'></label>" +
                "</div></div>" +
                "</li>";
                //<input type='checkbox' name='checkContacto' id='checkContacto' onclick='invitar(" + id + ")' value='" + id + "' style='margin-right: -8px'" + checado + "></li>";

            $('#listaContactoDet').append(linombre);
            $('#listaContactoDet').append(licorreo);
            $('#listaContactoDet').append(licheck);
        });

        redimensionar();
        $('.overlay').hide();
        $('.modal').hide();

    }).fail(function(data){
        alert('error en la recuperación de contactos');
    })
}

function redimensionar(){
    var ancho = 0;
    var filas = ($('#listaContacto li ul li').length) / 3;
    if (filas > $('#listaGrupo li').length ){
        ancho = (filas * 50) + 50;
        $('.rowListas').height(ancho);
        //alert(ancho);
    }else{
        ancho = ($('#listaGrupo li').length * 50) + 50;
        $('.rowListas').height(ancho);
        //alert(ancho);
    }


}

function agregarContacto(){
    $('.overlay').show();
    $('.modal').show();
    var formData = {
        'nombre':$('#txtNombre').val(),
        'correo': $('#txtCorreo').val(),
        'grupo': $('#comboGrupo').val(),
        'empresacontacto' : $('#txtEmpresaContacto').val(),
        'idusuario': $('#campoUsuarioID').val(),
        'operacion': 'CONTACTO'
    };
    $.ajax({
        type: 'POST',
        url: 'agendapost.php',
        data: formData,
        dataType: 'json',
        encode: true
    }).done(function(data){
        $('.overlay').hide();
        $('.modal').hide();

        idgrupo = $('#comboGrupo').val();
        if (data['id'] > 0){
            recuperarGrupo(idgrupo);
            $("#listaGrupo li").removeClass('activo');
            $("#listaGrupo >li[value="+idgrupo+"]").addClass('activo');

            $('#txtNombre').val('');
            $('#txtCorreo').val('');
            $('#comboGrupo').val('-1');
            $('#txtEmpresaContacto').val('');
        }else{
            $('.error').html('Correo repetido o faltan datos');
            $('.error').show();
        }


    }).fail(function(data){

    })
}

function fechaActual(){
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth()+1; //January is 0!
    var yyyy = today.getFullYear();

    if(dd<10) {
        dd='0'+dd
    }

    if(mm<10) {
        mm='0'+mm
    }

    hoy = dd+'/'+mm+'/'+yyyy;
    return hoy;
}

function cerrarCancelar(){
    $('#divCancelar').hide();
    return false;
}

function actualizarnumero(){
    var valor = $('#campoUsuarioID').val();
    var formData = {
        'usuarioid': valor,
        'operacion': 'NUMERO'
    };
    $.ajax({
        type: 'POST',
        url: 'agendapost.php',
        data: formData,
        dataType: 'json',
        encode: true
    })
        .done(function(data){
            $('#h1numero').html(data);
        })
        .fail(function(){
            alert('Falló al actualizar citas');
        });
}

function actualizarGrupos(valor){
    $('.overlay').show();
    $('.modal').show();
    var formData = {
        'empresaid':valor,
        'operacion': 'LISTAGRUPOS'
    };
    $.ajax({
        type: 'POST',
        url: 'agendapost.php',
        data: formData,
        dataType: 'json',
        encode: true
    }).done(function(data){
        $('.overlay').hide();
        $('.modal').hide();

        $('#comboGrupo').empty();
        if (data[0].GrupoID > 0){
            $.each(data, function(i, item) {
                grupoid = data[i].GrupoID;
                nombre = data[i].Nombre;
                opcion = "<option value='" + grupoid + "'>" + nombre + "</option>"
                $('#comboGrupo').append(opcion);
            });
        }else{
            opcion = "<option value='"+0+"'>"+ 'Sin Grupos' +"</option>"
            $('#comboGrupo').append(opcion);
        }


    }).fail(function(data){

    })

}
