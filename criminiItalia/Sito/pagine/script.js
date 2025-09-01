// cambiaDati(document.getElementById("selezionaReato").value);
let graficoProvincia=null; 
let graficoReato=null;
let=graficoImpattoRegione=null;



function creaGrafico(etichette, dati, label, tipo, id, borderColor)
{
    ctx = document.getElementById(id);

    grafico = new Chart(ctx, {
    type: tipo,
    data: {
      labels: etichette,
      datasets: [{
        label: label,
        data: dati,
        borderWidth: 1,
        borderColor: borderColor,
        backgroundColor: [
          'rgb(155, 99, 132)',
          'rgb(54, 162, 235)',
          'rgb(35, 205, 86)',
          'rgb(225, 105, 86)',
          'blue'
        ],
      }]
    },
    options: {
      maintainAspectRatio:false,
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });

  return grafico;
}
  


function selezionaDati(funzione, elemento, regione)
{
    console.log(elemento)
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() { 
        if (this.readyState == 4 && this.status == 200) { 
            console.log(this.responseText);

            var array= JSON.parse(this.responseText)
            etichette=[];
            dati=[];
            for(i=0; i<array.length; i++)
            {
              etichette.push(array[i]["etichetta"]);
              dati.push(array[i]["dato"]);
            }
            if(funzione=="provinciaRegione")
              cambiaDatiProvincia(etichette, dati);
            else if(funzione=="reatiRegione")
              cambiaDatiReato(etichette, dati);
            else if(funzione=="impattoRegione")
              cambiaDatiImpattoRegione(etichette, dati);

        }
    }

    xmlhttp.open("GET", "backend.php?funzione="+funzione+"&elemento="+elemento+"&regione="+regione, true); 
    xmlhttp.send(); 
} 


function cambiaDatiRegione(funzione, elemento)
{
  selezionaDati(funzione, elemento);
}




function cambiaDatiProvincia(etichetta, dati)
{
  if(graficoProvincia!=null)
  {
    graficoProvincia.destroy()
  }
  graficoProvincia= creaGrafico(etichette, dati, "Crimini", "bar", "graficoProvincia");
}





function cambiaDatiReato(etichette, dati)
{
  if(graficoReato!=null)
  {
    graficoReato.destroy()
  }
  graficoReato= creaGrafico(etichette, dati, "Crimini", "bar", "graficoReato")
}

function cambiaDatiImpattoRegione(etichette, dati)
{
  if(graficoImpattoRegione!=null)
  {
    graficoImpattoRegione.destroy()
  }
  graficoImpattoRegione= creaGrafico(etichette, dati, "Crimini", "doughnut", "canvaGraficoRegioneTorta")
}


// console.log("caiooooo ")