<!DOCTYPE HTML>
<html>
<head>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="../Stile/style.css">
<title>Dati regione</title>
</head>
<body>
    <?php
    if(isset($_GET["inviato"]))
    {
            $tipoReato  = $provincia = $totaliRegione = $totaliItalia = $temp =[];
            $regione="";
            $anni = [2018, 2019, 2020, 2021, 2022];
            $nRiga=0;

            function Eprensente($elemento, $array)
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

                    if($arr_riga[0]==$_GET["luogo"] && $arr_riga[3]=="totale")
                    {
                        array_push($totaliRegione, $arr_riga[5]);
                    }

                    if(strlen($arr_riga[0])==4 && $regione=="")
                    {
                        if($arr_riga[0] == $_GET["luogo"])
                        {
                            $regione=$arr_riga[1]; // cerco il nome della regione usando il codice
                        }
                    }
                    
                    if(strlen($arr_riga[0])==5 && str_contains($arr_riga[0], $_GET["luogo"]))
                    {
                        if(count($provincia)==0)
                        {
                            array_push($provincia, $arr_riga[1]);
                        }
                        elseif($provincia[count($provincia)-1]!=$arr_riga[1])
                        {
                            if(!Eprensente($arr_riga[1], $provincia))
                            {
                                array_push($provincia, $arr_riga[1]);
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
                            if(!Eprensente($arr_riga[3], $tipoReato))
                                array_push($tipoReato, $arr_riga[3]);
                        }
                    
                    }
                }
            }

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
                   
                </ul>
                </div>
            </div>
        </nav>

        <div class="container" >
            <div class="row">
                <div class="col-lg-12 text-center"><h2>Regione selezionata: <?php echo $regione ?></h2></div>
            </div>
            <div class="row">
                <div class="col-lg-2"></div>
                <div class="col-lg-8 p-2">
                    <div  id="graficoRegione">
                        <canvas id="canvaGraficoRegione" style="height: 370px; width: 100%;">
                        
                        </canvas>
                    </div>
                </div>
                <div class="col-lg-2"></div>
            </div>
            
            <?php
                if(!isset($_GET["inviato"]))
                {
                    echo "<h1>errore</h1>";
                }
                else
                {
            ?>
                    <div class="row pt-4">
                        <div class="col-lg-4 p-2">
                            <form action="">
                                <select id="selezionaAnnoReato" onchange="selezionaDati('impattoRegione', this.value, '<?php echo addslashes($regione) ?>')" class="form-select" aria-label="Default select example">
                                    <option selected>Seleziona un anno</option>
                                    <?php
                                        for($i=0; $i<count($anni); $i++)
                                        {
                                            echo "<option value='". $anni[$i] ."'>". $anni[$i] ."</option>"; 
                                        }
                                    ?>
                                </select>
                            </form>
                        </div>
                        <div class="col-lg-4 p-2">
                            <form action="">
                                <select id="selezionaProvincia" onchange="selezionaDati('provinciaRegione', this.value, '<?php echo addslashes($regione) ?>')" class="form-select" aria-label="Default select example">
                                    <option selected>Seleziona una provincia</option>
                                    <?php
                                        for($i=0; $i<count($provincia); $i++)
                                        {
                                            echo "<option value='". $provincia[$i] ."'>". $provincia[$i] ."</option>"; 
                                        }
                                    ?>
                                </select>
                            </form>
                        </div>
                        <div class="col-lg-4 p-2">
                            <form action="">
                                <select id="selezionaProvincia" onchange="selezionaDati('reatiRegione', this.value, '<?php echo addslashes($regione) ?>')" class="form-select" aria-label="Default select example">
                                    <option selected>Seleziona un reato</option>
                                    <?php
                                        for($i=0; $i<count($tipoReato); $i++)
                                        {
                                            echo "<option value=\"". addslashes($tipoReato[$i]) ."\">". $tipoReato[$i] ."</option>"; 
                                        }
                                    ?>
                                </select>
                            </form>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 p-2">
                            <div id="graficoRegioneTorta">
                                <canvas id="canvaGraficoRegioneTorta" style="height: 370px; width: 100%;">
                                

                                </canvas>
                            </div>
                           
                        </div>
                        <div class="col-lg-4 p-2">
                            <div class="divGrafico">
                                <canvas id="graficoProvincia" style="height: 370px; width: 100%;"></canvas>
                            </div>
                        </div>

                        <div class="col-lg-4 p-2">
                            <div id="divSelezionaReato">
                                <div class="divGrafico">
                                    <canvas id="graficoReato" style="height: 370px; width: 100%;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

            <?php
                }
            ?>
        </div>

        
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="script.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>

    <script>
        etichette=(<?php echo json_encode($anni); ?>)
        dati=(<?php  echo json_encode($totaliRegione); ?>)
        console.log(etichette);
        graficoRegione= creaGrafico(etichette, dati, "Crimini", "bar", "canvaGraficoRegione");
                
        
        graficoImpattoRegione= creaGrafico('', '', '', "doughnut", "canvaGraficoRegioneTorta");
        graficoProvincia= creaGrafico('','', '', 'bar', 'graficoProvincia');
        graficoReato= creaGrafico('','', '', 'bar', 'graficoReato');
    <?php
    }
    else
    {
        echo "<h1>Errrrrrrrrroooooooooreeeeeeeee</h1>";
    }
    ?>

</script>
</body>
</html>       