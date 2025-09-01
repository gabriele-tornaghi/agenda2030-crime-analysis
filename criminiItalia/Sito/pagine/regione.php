<!DOCTYPE HTML>
<html>
<head>
    <!-- Inclusione di Bootstrap per lo styling -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../Stile/style.css">
    <title>Dati regione</title>
</head>
<body>
    <?php
    // Controlla se è stato selezionato un luogo tramite GET
    if(isset($_GET["inviato"]))
    {
        // Inizializzazione degli array per dati della regione, provincia, tipi di reato
        $tipoReato  = $provincia = $totaliRegione = $totaliItalia = $temp =[];
        $regione = "";
        $anni = [2018, 2019, 2020, 2021, 2022];
        $nRiga = 0;

        // Funzione per verificare se un elemento è già presente in un array
        function Eprensente($elemento, $array)
        {
            for($i=0; $i<count($array); $i++)
            {
                if($array[$i] == $elemento)
                {
                    return true;
                }
            }
            return false;
        }

        // Apertura del file CSV contenente i dati sui reati
        $file = fopen("../../DataSet/Reati_in_Italia.csv", "r");

        // Lettura riga per riga
        while(!feof($file))
        {
            $riga = fgets($file);
            $arr_riga = str_getcsv($riga, ";");

            if(count($arr_riga) > 1) // Salta righe vuote
            {
                // Raccolta dei totali della regione selezionata
                if($arr_riga[0] == $_GET["luogo"] && $arr_riga[3] == "totale")
                {
                    array_push($totaliRegione, $arr_riga[5]);
                }

                // Recupera il nome della regione dal codice
                if(strlen($arr_riga[0]) == 4 && $regione == "")
                {
                    if($arr_riga[0] == $_GET["luogo"])
                    {
                        $regione = $arr_riga[1];
                    }
                }

                // Raccolta dei nomi delle province della regione
                if(strlen($arr_riga[0]) == 5 && str_contains($arr_riga[0], $_GET["luogo"]))
                {
                    if(count($provincia) == 0)
                    {
                        array_push($provincia, $arr_riga[1]);
                    }
                    elseif($provincia[count($provincia)-1] != $arr_riga[1])
                    {
                        if(!Eprensente($arr_riga[1], $provincia))
                        {
                            array_push($provincia, $arr_riga[1]);
                        }
                    }
                }

                // Raccolta dei tipi di reato presenti in Italia (per i filtri)
                if($arr_riga[0] == "IT")
                {
                    if(count($tipoReato) == 0)
                    {
                        array_push($tipoReato, $arr_riga[3]);
                    }
                    elseif($tipoReato[count($tipoReato)-1] != $arr_riga[3] && $arr_riga[3] != "totale")
                    {
                        if(!Eprensente($arr_riga[3], $tipoReato))
                            array_push($tipoReato, $arr_riga[3]);
                    }
                }
            }
        }

        fclose($file); // Chiude il file CSV
    ?>
    
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Seleziona regione</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Titolo con la regione selezionata -->
        <div class="row">
            <div class="col-lg-12 text-center">
                <h2>Regione selezionata: <?php echo $regione ?></h2>
            </div>
        </div>

        <!-- Grafico principale dei crimini della regione -->
        <div class="row">
            <div class="col-lg-2"></div>
            <div class="col-lg-8 p-2">
                <div id="graficoRegione">
                    <canvas id="canvaGraficoRegione" style="height: 370px; width: 100%;"></canvas>
                </div>
            </div>
            <div class="col-lg-2"></div>
        </div>

        <!-- Selezione anno, provincia e tipo di reato -->
        <div class="row pt-4">
            <div class="col-lg-4 p-2">
                <form>
                    <select id="selezionaAnnoReato" onchange="selezionaDati('impattoRegione', this.value, '<?php echo addslashes($regione) ?>')" class="form-select">
                        <option selected>Seleziona un anno</option>
                        <?php
                        foreach($anni as $anno){
                            echo "<option value='$anno'>$anno</option>";
                        }
                        ?>
                    </select>
                </form>
            </div>
            <div class="col-lg-4 p-2">
                <form>
                    <select id="selezionaProvincia" onchange="selezionaDati('provinciaRegione', this.value, '<?php echo addslashes($regione) ?>')" class="form-select">
                        <option selected>Seleziona una provincia</option>
                        <?php
                        foreach($provincia as $prov){
                            echo "<option value='$prov'>$prov</option>";
                        }
                        ?>
                    </select>
                </form>
            </div>
            <div class="col-lg-4 p-2">
                <form>
                    <select id="selezionaReato" onchange="selezionaDati('reatiRegione', this.value, '<?php echo addslashes($regione) ?>')" class="form-select">
                        <option selected>Seleziona un reato</option>
                        <?php
                        foreach($tipoReato as $reato){
                            echo "<option value='".addslashes($reato)."'>$reato</option>";
                        }
                        ?>
                    </select>
                </form>
            </div>
        </div>

        <!-- Grafici secondari: torta, provincia e reato -->
        <div class="row">
            <div class="col-lg-4 p-2">
                <canvas id="canvaGraficoRegioneTorta" style="height: 370px; width: 100%;"></canvas>
            </div>
            <div class="col-lg-4 p-2">
                <canvas id="graficoProvincia" style="height: 370px; width: 100%;"></canvas>
            </div>
            <div class="col-lg-4 p-2">
                <canvas id="graficoReato" style="height: 370px; width: 100%;"></canvas>
            </div>
        </div>
    </div>

    <!-- Inclusione librerie -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>

    <script>
        // Passaggio dati PHP a JS per grafico principale
        const etichette = <?php echo json_encode($anni); ?>;
        const dati = <?php echo json_encode($totaliRegione); ?>;

        console.log(etichette);

        // Creazione dei grafici tramite funzione esterna "creaGrafico"
        const graficoRegione = creaGrafico(etichette, dati, "Crimini", "bar", "canvaGraficoRegione");
        const graficoImpattoRegione = creaGrafico('', '', '', "doughnut", "canvaGraficoRegioneTorta");
        const graficoProvincia = creaGrafico('', '', '', 'bar', 'graficoProvincia');
        const graficoReato = creaGrafico('', '', '', 'bar', 'graficoReato');
    </script>

    <?php
    } else {
        // Messaggio di errore se non è stata selezionata una regione
        echo "<h1>Errore: nessuna regione selezionata</h1>";
    }
    ?>
</body>
</html>
