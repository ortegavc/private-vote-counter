$(document).ready(function() {
  $.ajax({
    url: "index.php?page=ajaxreq&chart=col&tipo="+$("#tipo").val(),
    method: "GET",
    dataType: "json"
  }).done(function( msg ) {    
    var chart = new CanvasJS.Chart("chartContainer", {
            theme: "theme2",
            animationEnabled: true,
            title: {
                text: "Indicador de votos nominales"
            },
            data: [
            {
                type: "column",                
                dataPoints: msg
            }
            ]
        });
    chart.render();
  });
  $.ajax({
    url: "index.php?page=ajaxreq&chart=pie&tipo="+$("#tipo").val(),
    method: "GET",
    dataType: "json"
  }).done(function( msg ) {    
    var chart = new CanvasJS.Chart("chartContainer2", {
            title: {
                text: "Porcentajes de registro de actas"
            },
            animationEnabled: true,
            legend: {
                verticalAlign: "center",
                horizontalAlign: "left",
                fontSize: 20,
                fontFamily: "Helvetica"
            },
            theme: "light2",
            data: [
            {
                type: "pie",
                indexLabelFontFamily: "Garamond",
                indexLabelFontSize: 20,
                indexLabel: "{label} {y}%",
                startAngle: -20,
                //showInLegend: true,
                toolTipContent: "{legendText} {y}%",
                dataPoints: msg
            }
            ]
        });
    chart.render();
  });
  
  $.ajax({
    url: "index.php?page=ajaxreq&chart=pie2&tipo="+$("#tipo").val(),
    method: "GET",
    dataType: "json"
  }).done(function( msg ) {
    var chart = new CanvasJS.Chart("chartContainer3", {
      theme: "light2", // "light1", "light2", "dark1", "dark2"
      //exportEnabled: true,
      animationEnabled: true,
      title: {
        text: "Porcentajes de votos calculados en base a las actas registradas"
      },
      data: [{
        type: "pie",
        startAngle: 25,
        toolTipContent: "<b>{label}</b>: {y}%",
        //showInLegend: "true",
        //legendText: "{label}",
        indexLabelFontSize: 16,
        indexLabel: "{label} - {y}%",
        dataPoints: msg
      }]
    });
    chart.render();
  });
});
