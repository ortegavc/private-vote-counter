$(document).ready(function () {
    /*$("input[name^='v']").change(function () {
        var mateval = 0; // Valor del input que es pareja del actual
        //console.info($( this ).attr('name'));
        if ($(this).attr('name').indexOf("vn_") === 0) {
            //console.info('Cambia voto nominal');
            //console.info("vl_"+$( this ).attr('name').slice(3));
            //console.info($("input[name='vl_"+$( this ).attr('name').slice(3)+"']").val());
            if($("input[name='vl_" + $(this).attr('name').slice(3) + "']").val() === ""){
                $("input[name='vl_" + $(this).attr('name').slice(3) + "']").val(0);
            } else {
                mateval = parseInt($("input[name='vl_" + $(this).attr('name').slice(3) + "']").val());
            }
        } else if ($(this).attr('name').indexOf("vl_") === 0) {
            //console.info('Cambia voto lista');
            //console.info("vn_"+$( this ).attr('name').slice(3));    
            //console.info($("input[name='vn_"+$( this ).attr('name').slice(3)+"']").val());
            if($("input[name='vn_" + $(this).attr('name').slice(3) + "']").val() === ""){
                $("input[name='vn_" + $(this).attr('name').slice(3) + "']").val(0);
            } else {
                mateval = parseInt($("input[name='vn_" + $(this).attr('name').slice(3) + "']").val());
            }
        }
        $("#suma_" + $(this).attr('name').slice(3)).val(parseInt($(this).val()) + mateval);
    });*/
});

function activarEdicion() {
    $('input').removeAttr('readonly');
    $("input[name='numero']").attr('readonly', true);
    $("input[id^='suma_']").attr('readonly', true);
    //$('input').attr('required', true);
    document.getElementById("btnShowModal").style.display = 'none';
    document.getElementById("btnCancelar").style.visibility = "visible";
    document.getElementById("btnEditar").style.visibility = "visible";
    $('#myModal2').modal('hide');
}

function calcular(codigolista){
  console.info("Calcular: "+codigolista);
  var minimo = 0;
  var arraux = [];
  
  $.each($(".suma."+codigolista), function (index, value) {
    console.info($(this).val());
    arraux.push($(this).val());
  });
  minimo = Math.min(...arraux);
  console.log("Menor: "+minimo);
  $.each($(".vl."+codigolista), function (index, value) {
    $(this).val(minimo);
  });
  var i = 0;
  $.each($(".vn."+codigolista), function (index, value) {
    $(this).val(arraux[i]-minimo);
    i++;
  });
}
