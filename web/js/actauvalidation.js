function validateForm() {
    var total_h = parseInt($('#total').val());
    var total_b = 0;
    var total_n = 0;
    var total_x = 0;
    if ($('#blancos').val() === "") {
        $('#blancos').val(0);
    }
    if ($('#nulos').val() === "") {
        $('#nulos').val(0);
    }
    total_b = $('#blancos').val();
    total_n = $('#nulos').val();
//  console.info("Total firmas: "+total_h);
//  console.info("Total blancos: "+total_b);
//  console.info("Total nulos: "+total_n);
    var total_vn = 0;
    $.each($("input[name^='vn']"), function (index, value) {
        if ($(this).val() === "") {
            $(this).val(0);
        }
        total_vn += parseInt($(this).val());
    });
    console.info("Total votos nominales: " + total_vn);
    total_x = total_vn + total_b + total_n;
    if (total_x != total_h) {
        $('#msgvalidation').html('La suma de los votos (' + total_x + ') no coincide con el total de firmas y huellas dactilares (' + total_h + ').');
        $('#myModal').modal('show');
        return false;
    }
    return true;
}

function enviarForm() {
    document.getElementById("myForm").submit();
}

function activarEdicion() {
    $('input').removeAttr('readonly');
    $("input[name='numero']").attr('readonly', true);
    $('input').attr('required', true);
    document.getElementById("btnShowModal").style.display = 'none';
    document.getElementById("btnCancelar").style.visibility = "visible";
    document.getElementById("btnEditar").style.visibility = "visible";
    $('#myModal2').modal('hide');
}
