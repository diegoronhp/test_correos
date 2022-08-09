function limpiar_formulario_nueva_cuenta(){
    $("#dir_email").val("");
    $("#alias_email").val("");
    $("#pswd_email").val("");
    $("#serv_smtp").val("");
    $("#port_smtp").val("");
    $("#serv_imap").val("");
    $("#port_imap").val("");
}

$(function(){
    $('#crear_cuenta').click(function(event){
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

        var datos_form = new FormData;
        datos_form.append("dir_email",dir_email);
        datos_form.append("alias_email",alias_email);
        datos_form.append("pswd_email",pswd_email);
        datos_form.append("serv_smtp",serv_smtp);
        datos_form.append("port_smtp",port_smtp);
        datos_form.append("serv_imap",serv_imap);
        datos_form.append("port_imap",port_imap);
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
            },
            error: function(){
                var respuesta = "Error de conexion con el servidor de la aplicacion";
                console.log("ERROR = "+respuesta);
                $("#esperando").removeClass('preloader');
                limpiar_formulario_nueva_cuenta();
            }
        })
    })
})


