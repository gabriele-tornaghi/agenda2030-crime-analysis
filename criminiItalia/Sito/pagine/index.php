<!DOCTYPE HTML>
<html>
<head>
    <!-- Inclusione di Bootstrap per lo styling rapido -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../Stile/style.css">
    <title>Dati Italia</title>
</head>
<body>
    <?php
        // Inizializzazione degli array principali
        $regione = $tipoReato  = $totaliItalia = $codiciRegioni= [];
        $anni = [2018, 2019, 2020, 2021, 2022];
        $nRiga=0;

        // Funzione per controllare se un elemento è presente in un array
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

        // Apertura del file CSV contenente i dati sui reati in Italia
        $file=fopen("../../DataSet/Reati_in_Italia.csv", "r");

        // Lettura riga per riga del CSV
        while(!feof($file))
        {
            $riga =fgets($file); // Legge la riga corrente
            $arr_riga=str_getcsv($riga, ";"); // Converte la riga in array separato da ";"
            
            if(count($arr_riga)>1) // Salta eventuali righe vuote
            {
                // Se la riga riguarda l'Italia e il totale dei reati
                if($arr_riga[0]=="IT" && $arr_riga[3]=="totale")
                {
                    array_push($totaliItalia, $arr_riga[5]); // Salva il totale dei crimini
                }
                
                // Se la riga riguarda una regione italiana (codice a 4 caratteri)
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
                
                // Raccolta dei diversi tipi di reato (escludendo "totale")
                if($arr_riga[0]=="IT")
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

        fclose($file); // Chiusura del file CSV
    ?>

    <!-- Navbar Bootstrap -->
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

    <div class="container">
        <div class="row">
            <h1>Italia</h1>

            <!-- Grafico lineare dei reati in Italia -->
            <div class="col-lg-6 p-2">
                <div id="graficoItalia">
                    <canvas id="canvaGraficoItalia" style="height: 370px; width: 100%;"></canvas>
                </div>
            </div>

            <!-- Grafico a torta dei reati in Italia -->
            <div class="col-lg-6 p-2">
                <div id="graficoItaliaTorta">
                    <canvas id="canvaGraficoItaliaTorta" style="height: 370px; width: 100%;"></canvas>
                </div>
            </div>
        </div>

        <!-- Selezione della regione -->
        <div class="row">
            <div class="col-lg-4"></div>
            <div class="col-lg-4 p-2 text-center">
                <h1>Seleziona la tua regione</h1>
                <form action="regione.php" method="">
                    <select id="selezionaLuogo" class="form-select" name="luogo" aria-label="Default select example">
                        <?php
                            // Popola il select con le regioni italiane
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

    <!-- Inclusione Chart.js per i grafici -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="script.js"></script>

    <!-- Inclusione Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>

    <script>
        // Passaggio dei dati PHP a JavaScript
        etichette = (<?php echo json_encode($anni); ?>) // Anni
        dati = (<?php echo json_encode($totaliItalia); ?>) // Totale reati Italia
        console.log(etichette);

        // Creazione dei grafici tramite funzione esterna "creaGrafico"
        graficoItalia = creaGrafico(etichette, dati, "Crimini", "line", "canvaGraficoItalia", "black");
        graficoTortaItalia = creaGrafico(etichette, dati, "Crimini", "polarArea", "canvaGraficoItaliaTorta");
    </script>
</body>
</html>
