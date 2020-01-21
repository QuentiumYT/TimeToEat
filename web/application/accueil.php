<?php
include('../fonction.php');

$cantine = $bdd->prepare('SELECT * FROM time ORDER BY id DESC LIMIT 1');
$cantine->execute(array());
$cantine = $cantine->fetch();
$date = $cantine['temps'];
$nb_personne = $cantine['nb_personne'];
$temps = $nb_personne * 9;
$temps_h = intval($temps / 3600);
$temps_m = intval(($temps % 3600) / 60);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Accueil</title>
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
    <h2>Temps d'attente :</h2>
    <p>
        <?php
        if ($temps_h != 0) {
            echo $temps_h;
            echo " h ";
        }
        echo $temps_m;
        echo " min";
        ?>
    </p>
    <h3>Nombre de personne(s)</h3>
    <p><?= $nb_personne ?></p>
</body>

</html>