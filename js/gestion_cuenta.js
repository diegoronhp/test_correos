function limpiar_formulario_nueva_cuenta(){
    $("#dir_email").val("");
    $("#alias_email").val("");
    $("#pswd_email").val("");
    $("#serv_smtp").val("");
    $("#port_smtp").val("");
    $("#serv_imap").val("");
    $("#port_imap").val("");
}

function consultar_cuentas(){
    var act = "consultar";
    $.ajax({
        url: "../controller/CorreoController.php",
        type: "POST",
        data: {consultar : act},
        success: function(data){
            var respuesta = JSON.parse(data);
            //console.log(respuesta);
            $("#tabla_cuentas").html(respuesta);
        },
        error: function(){
            var respuesta = "No hay cuentas para mostrar";
            alert("ERROR = "+respuesta);
        }
    })
}


function consultar_tiempo(){
    var act = "sltiempo";
    $.ajax({
        url: "../controller/CorreoController.php",
        type: "POST",
        data: {sltiempo : act},
        success: function(data){
            var respuesta = JSON.parse(data);
            console.log(respuesta);
            $("#tiempo_latencia").val(respuesta);
        },
        error: function(){
            var respuesta = "No hay tiempo para mostrar";
            alert("ERROR = "+respuesta);
        }
    })
}

function validar_email(email,cuadro){
    var cumple = true;
    emailRegex = /^[-\w.%+]{1,64}@(?:[A-Z0-9-]{1,63}\.){1,125}[A-Z]{2,63}$/i;
    if(!emailRegex.test(email)) {
        cumple = false;
        mensaje_error = "La cuenta de correo "+email+" no tiene el formato requerido, por favor digite una cuenta de correo valida";
        $mensaje_actual = $(cuadro).html();
        console.log("mensaje_actual = "+$mensaje_actual);
        $(cuadro).html($mensaje_actual+"<br>"+mensaje_error);
    }
    return cumple;
}

function validar_vacio(texto,campo,cuadro){
    var cumple = true;
    if(texto == ""){
        cumple = false;
        mensaje_error = "El campo "+campo+" no puede estar vacio, por favor digite un texto valido para este campo";
        $mensaje_actual = $(cuadro).html();
        console.log("mensaje_actual = "+$mensaje_actual);
        $(cuadro).html($mensaje_actual+"<br>"+mensaje_error);
    }
    return cumple;
}

function validar_numero(numero,campo,cuadro){
    var cumple = true;
    if(numero <= 0){
        cumple = false;
        mensaje_error = "El campo "+campo+" no puede ser menor o igual que cero, por favor digite un numero valido para este campo";
        $mensaje_actual = $(cuadro).html();
        console.log("mensaje_actual = "+$mensaje_actual);
        $(cuadro).html($mensaje_actual+"<br>"+mensaje_error);
    }
    return cumple;
}

function comprobar_campos(campos){
    var cumple_campos = true;
    for(var i=0;i<campos.length;i++){
        var cumple = campos[i];
        if(!cumple){
            cumple_campos = false;
            break
        }
    }
    return cumple_campos;
}


function comprobar_registro_agregado(){
    var cumple_campo = [];
    var cumple_registro = true;
    var alias_email = $('#alias_email').val();
    var dir_email = $('#dir_email').val();
    var pswd_email = $('#pswd_email').val();
    var serv_smtp = $('#serv_smtp').val();
    var port_smtp = $('#port_smtp').val();
    var serv_imap = $('#serv_imap').val();
    var port_imap = $('#port_imap').val();
    console.log("ENTRO AL METODO comprobar_registro_agregado");
    console.log("alias_email = "+alias_email);
    console.log("dir_email = "+dir_email);
    console.log("pswd_email = "+pswd_email);
    console.log("serv_smtp = "+serv_smtp);
    console.log("port_smtp = "+port_smtp);
    console.log("serv_imap = "+serv_imap);
    console.log("port_imap = "+port_imap);
    cumple_campo[0] = validar_vacio(alias_email,"alias","#errores_agregar");
    cumple_campo[1] = validar_email(dir_email,"#errores_agregar");
    cumple_campo[2] = validar_vacio(pswd_email,"password","#errores_agregar");
    cumple_campo[3] = validar_vacio(serv_smtp,"servidor smtp","#errores_agregar");
    cumple_campo[4] = validar_numero(port_smtp,"puerto smtp","#errores_agregarr");
    cumple_campo[5] = validar_vacio(serv_imap,"servidor imap","#errores_agregar");
    cumple_campo[6] = validar_numero(port_imap,"puerto imap","#errores_agregar");
    cumple_registro = comprobar_campos(cumple_campo);
    return cumple_registro;
}


function comprobar_registro_editado(reg){
    var cumple_campo = [];
    var cumple_registro = true;
    var alias = $('#als_'+reg).val();
    var email = $('#drc_'+reg).val();
    var pswd = $('#psw_'+reg).val();
    var s_smtp = $('#smt_'+reg).val();
    var p_smtp = $('#pts_'+reg).val();
    var s_imap = $('#imp_'+reg).val();
    var p_imap = $('#pti_'+reg).val();
    console.log("ENTRO AL METODO comprobar_registro_editado");
    console.log("alias = "+alias);
    console.log("email = "+email);
    console.log("pswd = "+pswd);
    console.log("s_smtp = "+s_smtp);
    console.log("p_smtp = "+p_smtp);
    console.log("s_imap = "+s_imap);
    console.log("p_imap = "+p_imap);
    cumple_campo[0] = validar_vacio(alias,"alias","#errores_editar");
    cumple_campo[1] = validar_email(email,"#errores_editar");
    cumple_campo[2] = validar_vacio(pswd,"password","#errores_editar");
    cumple_campo[3] = validar_vacio(s_smtp,"servidor smtp","#errores_editar");
    cumple_campo[4] = validar_numero(p_smtp,"puerto smtp","#errores_editar");
    cumple_campo[5] = validar_vacio(s_imap,"servidor imap","#errores_editar");
    cumple_campo[6] = validar_numero(p_imap,"puerto imap","#errores_editar");
    cumple_registro = comprobar_campos(cumple_campo);
    return cumple_registro;
}

$(function(){
    limpiar_formulario_nueva_cuenta();
    consultar_cuentas();
    consultar_tiempo();

    $(document).on("click",".act",function(event){
        var texto = $(this).val();
        var seleccionado = event.target.id;
        console.log("texto = "+texto);
        console.log("seleccionado = "+seleccionado);
        var cadena = seleccionado.split("_");
        var id = cadena[1];

        if(texto == "Editar"){
            $('#als_'+id).removeAttr('disabled');
            $('#drc_'+id).removeAttr('disabled');
            $('#psw_'+id).removeAttr('disabled');
            $('#smt_'+id).removeAttr('disabled');
            $('#pts_'+id).removeAttr('disabled');
            $('#imp_'+id).removeAttr('disabled');
            $('#pti_'+id).removeAttr('disabled');
            $('#reg_'+id).val('Guardar');
        }
        if(texto == "Guardar"){
            var validado = comprobar_registro_editado(id);

            if(validado){
                console.log("EL REGISTRO **SI** CUMPLE**");
                var act = "actualizar";
                var alias_email = $('#als_'+id).val();
                var dir_email = $('#drc_'+id).val();
                var pswd_email = $('#psw_'+id).val();
                var serv_smtp = $('#smt_'+id).val();
                var port_smtp = $('#pts_'+id).val();
                var serv_imap = $('#imp_'+id).val();
                var port_imap = $('#pti_'+id).val();
                console.log("dir_email = "+dir_email);
                console.log("alias_email = "+alias_email);
                console.log("pswd_email = "+pswd_email);
                console.log("serv_smtp = "+serv_smtp);
                console.log("port_smtp = "+port_smtp);
                console.log("serv_imap = "+serv_imap);
                console.log("port_imap = "+port_imap);

                var datos_form = new FormData;
                datos_form.append("id_reg",id);
                datos_form.append("dir_email",dir_email);
                datos_form.append("alias_email",alias_email);
                datos_form.append("pswd_email",pswd_email);
                datos_form.append("serv_smtp",serv_smtp);
                datos_form.append("port_smtp",port_smtp);
                datos_form.append("serv_imap",serv_imap);
                datos_form.append("port_imap",port_imap);
                datos_form.append("actualizar",act);

                $.ajax({
                    url: "../controller/CorreoController.php",
                    type: "POST",
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: datos_form,
                    beforeSend: function(){
                        console.log("ESPERANDO RESPUESTA DEL SERVIDOR");
                        $("#esperando").addClass('preloader');
                    },
                    success: function(data){
                        var respuesta = JSON.parse(data);
                        console.log("MENSAJE = "+respuesta.mensaje);
                        alert(respuesta.mensaje);
                        $("#esperando").removeClass('preloader');
                        $('#als_'+id).attr('disabled', 'disabled');
                        $('#drc_'+id).attr('disabled', 'disabled');
                        $('#psw_'+id).attr('disabled', 'disabled');
                        $('#smt_'+id).attr('disabled', 'disabled');
                        $('#pts_'+id).attr('disabled', 'disabled');
                        $('#imp_'+id).attr('disabled', 'disabled');
                        $('#pti_'+id).attr('disabled', 'disabled');
                        $('#reg_'+id).val('Editar');
                    },
                    error: function(){
                        var respuesta = "Error de conexion con el servidor de la aplicacion";
                        console.log("ERROR = "+respuesta);
                        $("#esperando").removeClass('preloader');
                        $('#reg_'+id).val('Editar');
                    }
                });
                $("#errores_editar").html("");
                $("#errores_editar").removeClass('errores');
            }else{
                console.log("EL REGISTRO **NO** CUMPLE**");
                $("#errores_editar").addClass('errores');
            }
        }
    });


    $(document).on("click",".del",function(event){
        var seleccionado = event.target.id;
        var cadena = seleccionado.split("_");
        var id = cadena[1];
        console.log("seleccionado = "+seleccionado);
        console.log("id_reg = "+id);
        var alias_email = $('#als_'+id).val();
        var dir_email = $('#drc_'+id).val();
        var decision = confirm("Esta seguro de eliminar la cuenta de correo ("+dir_email+")");
        if(decision){
            console.log("EL REGISTRO CON id = "+id+" VA A SER ELIMINADO");
            var act = "eliminar";
            var datos_form = new FormData;
            datos_form.append("id_reg",id);
            datos_form.append("alias_email",alias_email);
            datos_form.append("dir_email",dir_email);
            datos_form.append("eliminar",act);
            $.ajax({
                url: "../controller/CorreoController.php",
                type: "POST",
                cache: false,
                contentType: false,
                processData: false,
                data: datos_form,
                beforeSend: function(){
                    console.log("ESPERANDO RESPUESTA DEL SERVIDOR");
                    $("#esperando").addClass('preloader');
                },
                success: function(data){
                    var respuesta = JSON.parse(data);
                    console.log("MENSAJE = "+respuesta.mensaje);
                    alert(respuesta.mensaje);
                    $("#esperando").removeClass('preloader');
                    consultar_cuentas();
                },
                error: function(){
                    var respuesta = "Error de conexion con el servidor de la aplicacion";
                    console.log("ERROR = "+respuesta);
                    $("#esperando").removeClass('preloader');
                }
            });
        }else{
            event.preventDefault();
        }
    });


    $('#crear_cuenta').click(function(event){
        var act = "agregar";
        var dir_email = $("#dir_email").val();
        var alias_email = $("#alias_email").val();
        var pswd_email = $("#pswd_email").val();
        var serv_smtp = $("#serv_smtp").val();
        var port_smtp = $("#port_smtp").val();
        var serv_imap = $("#serv_imap").val();
        var port_imap = $("#port_imap").val();
        //alert("MOSTRANDO LAS VARIABLES CAPTURADAS EN EL FORMUALRIO");
        console.log("dir_email = "+dir_email);
        console.log("alias_email = "+alias_email);
        console.log("pswd_email = "+pswd_email);
        console.log("serv_smtp = "+serv_smtp);
        console.log("port_smtp = "+port_smtp);
        console.log("serv_imap = "+serv_imap);
        console.log("port_imap = "+port_imap);

        var validado = comprobar_registro_agregado();

        if(validado){
            console.log("EL REGISTRO **SI** CUMPLE**");
            var datos_form = new FormData;
            datos_form.append("dir_email",dir_email);
            datos_form.append("alias_email",alias_email);
            datos_form.append("pswd_email",pswd_email);
            datos_form.append("serv_smtp",serv_smtp);
            datos_form.append("port_smtp",port_smtp);
            datos_form.append("serv_imap",serv_imap);
            datos_form.append("port_imap",port_imap);
            datos_form.append("agregar",act);
            $.ajax({
                url: "../controller/CorreoController.php",
                type: "POST",
                cache: false,
                contentType: false,
                processData: false,
                data: datos_form,
                beforeSend: function(){
                    console.log("ESPERANDO RESPUESTA DEL SERVIDOR");
                    $("#esperando").addClass('preloader');
                },
                success: function(data){
                    var respuesta = JSON.parse(data);
                    console.log("MENSAJE = "+respuesta.mensaje);
                    alert(respuesta.mensaje);
                    $("#esperando").removeClass('preloader');
                    limpiar_formulario_nueva_cuenta();
                    consultar_cuentas();
                },
                error: function(){
                    var respuesta = "Error de conexion con el servidor de la aplicacion";
                    console.log("ERROR = "+respuesta);
                    $("#esperando").removeClass('preloader');
                    limpiar_formulario_nueva_cuenta();
                }
            });
            $("#errores_agregar").html("");
            $("#errores_agregar").removeClass('errores');
        }else{
            console.log("EL REGISTRO **NO** CUMPLE**");
            $("#errores_agregar").addClass('errores');
        }
    });

    $('#editar_tiempo').click(function(event){
        var texto = $(this).val();
        console.log("texto = "+texto);

        if(texto == "Editar"){
            $('#tiempo_latencia').removeAttr('disabled');
            $('#editar_tiempo').val('Guardar');
        }

        if(texto == "Guardar"){
            var act = "uptiempo";
            var latencia = $("#tiempo_latencia").val();
            console.log("tiempo_latencia = "+latencia);
            var cumple = validar_numero(latencia,"tiempo latencia","#errores_tiempo");

            if(!cumple){
                $("#errores_tiempo").addClass('errores');
            }else{
                var datos_form = new FormData;
                datos_form.append("latencia",latencia);
                datos_form.append("uptiempo",act);
                $.ajax({
                    url: "../controller/CorreoController.php",
                    type: "POST",
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: datos_form,
                    beforeSend: function(){
                        console.log("ESPERANDO RESPUESTA DEL SERVIDOR");
                        $("#esperando").addClass('preloader');
                    },
                    success: function(data){
                        var respuesta = JSON.parse(data);
                        console.log("MENSAJE = "+respuesta.mensaje);
                        alert(respuesta.mensaje);
                        $("#esperando").removeClass('preloader');
                    },
                    error: function(){
                        var respuesta = "Error de conexion con el servidor de la aplicacion";
                        console.log("ERROR = "+respuesta);
                        $("#esperando").removeClass('preloader');
                    }
                });
                $('#tiempo_latencia').attr('disabled', 'disabled');
                $('#editar_tiempo').val('Editar');
                $("#errores_tiempo").html("");
                $("#errores_tiempo").removeClass('errores');
            }
        }
    });

    $('#ejecutar_test').click(function(event){
        var act = "ejecutar";
        var datos_form = new FormData;
        datos_form.append("ejecutar",act);
        $.ajax({
            url: "../controller/CorreoController.php",
            type: "POST",
            cache: false,
            contentType: false,
            processData: false,
            data: datos_form,
            beforeSend: function(){
                console.log("ESPERANDO RESPUESTA DEL SERVIDOR");
                $("#esperando").addClass('preloader');
            },
            success: function(data){
                var respuesta = JSON.parse(data);
                console.log("MENSAJE = "+respuesta.mensaje);
                alert(respuesta.mensaje);
                $("#esperando").removeClass('preloader');
            },
            error: function(){
                var respuesta = "Error de conexion con el servidor de la aplicacion";
                console.log("ERROR = "+respuesta);
                $("#esperando").removeClass('preloader');
            }
        })
    })
});


