<?php 
$funzione = $_REQUEST["funzione"];
$risposta = [];
$temp=[];


if($funzione=="provinciaRegione")
{
   $provincia= $_REQUEST["elemento"] ;
   
   $file = fopen("../../DataSet/Reati_in_Italia.csv","r");
   
   while(!feof($file))
    {
        $riga =fgets($file);
        $arr_riga=str_getcsv($riga, ";");
        if(count($arr_riga)>1)
        {
            if($arr_riga[1]==$provincia && $arr_riga[3]=="totale")
            {
                $temp = ["etichetta"=>$arr_riga[4], "dato"=>$arr_riga[5]];
                array_push($risposta, $temp);
            }
        }
    }
    fclose($file);
    echo json_encode($risposta);


}
if($funzione=="reatiRegione")
{
    $reato = $_REQUEST["elemento"];
    $reato= stripcslashes($reato);
    $regione = $_REQUEST["regione"];
    $regione= stripcslashes($regione);
    $file = fopen("../../DataSet/Reati_in_Italia.csv","r");

    while(!feof($file))
    {
        $riga =fgets($file);
        $arr_riga=str_getcsv($riga, ";");
        if(count($arr_riga)>1)
        {
            if($arr_riga[3]==$reato && $arr_riga[1]==$regione)
            {
                $temp = ["etichetta"=>$arr_riga[4], "dato"=>$arr_riga[5]];
                array_push($risposta, $temp);
            }
        }
    }

    fclose($file);


    echo json_encode($risposta);
}
if($funzione=="impattoRegione")
{
    $anno = $_REQUEST["elemento"];
    $regione = $_REQUEST["regione"];
    $regione= stripcslashes($regione);

    $file = fopen("../../DataSet/Reati_in_Italia.csv","r");

    while(!feof($file))
    {
        $riga =fgets($file);
        $arr_riga=str_getcsv($riga, ";");
        if(count($arr_riga)>1)
        {
            if($arr_riga[4]==$anno && $arr_riga[1]==$regione && $arr_riga[2]=="TOT")
            {
                $totRegione = ["etichetta"=>"Crimini in ".$regione , "dato"=>$arr_riga[5]];
                array_push($risposta, $totRegione);
            }
            if($arr_riga[4]==$anno && $arr_riga[0]=="IT" && $arr_riga[2]=="TOT")
            {
                $totItalia = $arr_riga[5];
            }
        }
    }

    fclose($file);

    $criminiRestanti=$totItalia-$totRegione["dato"];
    array_push($risposta, ["etichetta"=>"Restanti crimini in italia" , "dato"=>$criminiRestanti]);

    echo json_encode($risposta);

}



?>