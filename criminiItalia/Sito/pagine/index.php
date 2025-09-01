<!DOCTYPE HTML>
<html>
<head>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="../Stile/style.css">
<title>Dati italia</title>
</head>
<body>
    <?php
        $regione = $tipoReato  = $totaliItalia = $codiciRegioni= [];
        $anni = [2018, 2019, 2020, 2021, 2022];
        $nRiga=0;

        function Epresente($elemento, $array)
        {
            for($i=0; $i<count($array); $i++)
            {
                if($array[$i]==$elemento)
                {
                    return true;
                }
            }
            return false;
        }

        $file=fopen("../../DataSet/Reati_in_Italia.csv", "r");


        while(!feof($file))
        {
            $riga =fgets($file);
            $arr_riga=str_getcsv($riga, ";");
            if(count($arr_riga)>1)
            {
                if($arr_riga[0]=="IT" && $arr_riga[3]=="totale")
                {
                    array_push($totaliItalia, $arr_riga[5]);
                }
                
                if(strlen($arr_riga[0])==4)
                {
                    if(count($regione)==0)
                    {
                        array_push($codiciRegioni, $arr_riga[0]);
                        array_push($regione, $arr_riga[1]);
                    }
                    elseif($regione[count($regione)-1]!=$arr_riga[1])
                    {
                        if(!Epresente($arr_riga[1], $regione))
                        {
                            array_push($codiciRegioni, $arr_riga[0]);
                            array_push($regione, $arr_riga[1]);
                        }
                    }
                }
                
                
                if($arr_riga[0]=="IT") //se sto riempiendo ancora l'italia allora entra 
                {
                    if(count($tipoReato)==0)
                    {
                        array_push($tipoReato, $arr_riga[3]);
                    }
                    elseif($tipoReato[count($tipoReato)-1]!=$arr_riga[3] && $arr_riga[3]!="totale")
                    {
                        if(!Epresente($arr_riga[3], $tipoReato))
                            array_push($tipoReato, $arr_riga[3]);
                    }
                   
                }
            }
        }

        // print_r($totaliItalia);
   
        fclose($file);

    ?>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                <a class="nav-link"  href="#">Home</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="index.php">Seleziona regione</a>
                </li>
                <li class="nav-item">
          
            </ul>
            </div>
        </div>
    </nav>

    <div class="container" >
        <div class="row">
        <h1>Italia</h1>

            <div class="col-lg-6 p-2">

                <div  id="graficoItalia">
                    <canvas id="canvaGraficoItalia" style="height: 370px; width: 100%;">
                    

                    </canvas>
                </div>
            </div>
            <div class="col-lg-6 p-2">
                <div id="graficoItaliaTorta">
                    <canvas id="canvaGraficoItaliaTorta" style="height: 370px; width: 100%;">
                    

                    </canvas>
                </div>
            </div>
        </div>
            <div class="row">
                <div class="col-lg-4"></div>
                <div class="col-lg-4 p-2 text-center">
                    <h1>Seleziona la tua regione</h1>
                    <form action="regione.php" method="">
                        <select id="selezionaLuogo" class="form-select" name="luogo" aria-label="Default select example">
                            <?php
                                for($i=0; $i<count($regione); $i++)
                                {
                                    echo "<option value='". $codiciRegioni[$i] ."'>". $regione[$i] ."</option>";
                                }
                            ?>
                        </select>
                        <br>
                        <input type="submit" name="inviato" class="btn btn-primary mb-3">
                    </form>
                </div>
                <div class="col-lg-4"></div>
            </div>
    </div>

    
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="script.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
<script>
    etichette=(<?php echo json_encode($anni); ?>)
    dati=(<?php  echo json_encode($totaliItalia); ?>)
    console.log(etichette);
    graficoItalia= creaGrafico(etichette, dati, "Crimini", "line", "canvaGraficoItalia", "black");
    graficoTortaItalia= creaGrafico(etichette, dati, "Crimini", "polarArea", "canvaGraficoItaliaTorta");

</script>
</body>
</html>       