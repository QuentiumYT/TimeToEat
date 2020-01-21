<?php
include('../fonction.php');

$datejour = date("Y-m-d");
$datejour1 = date('Y-m-d', strtotime("+1 day"));
$datejour_m = date('Y-m-1');

$interval = 5;
$interval_m = 125;

$data1_m = historique_debit($datejour_m, $datejour, $interval_m);
$data1_m = json_decode($data1_m);
$date = historique_date($datejour_m, $datejour, $interval_m);
$date = json_decode($date);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Historique</title>
    <link rel="stylesheet" href="../css/style_app.css">
</head>

<body>
    <div class="menu">
        <ul id="menu">
            <li>
                <a href="accueil.php">Accueil</a>
            </li>
            <li>
                <a href="historique.php">Historique</a>
            </li>
            <li>
                <a href="menu.html">Menu</a>
            </li>
        </ul>
    </div>
    <h1>TimeToEat</h1>
    <div class="graphique">
        <table>
            <?php
            $long = count($data1_m);
            for ($i = 0; $i < $long; -$i++) {
            ?>
                <tr>
                    <th>
                        <?php
                        echo $date[$i];
                        ?>
                    </th>
                    <th>
                        <?php
                        echo $data1_m[$i];
                        ?>
                    </th>
                </tr>
            <?php
            }
            ?>
            <tr>
        </table>
    </div>
</body>

</html>