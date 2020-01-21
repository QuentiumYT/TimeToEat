<?php
include('fonction.php');
setlocale(LC_TIME, 'fr_FR', 'fra');
ini_set('memory_limit', '-1');

if (isset($_GET['count']) && isset($_GET['key']) && isset($_GET['debit'])) {
    $key = $_GET['key'];
    $count = $_GET['count'];
    $debit = $_GET['debit'];
    $count = decryptage($count, $key);
    $debit = decryptage($debit, $key);
    if (is_numeric($count) && is_numeric($debit) && is_numeric($key)) {
        $insertnewtime = $bdd->prepare("INSERT INTO time(nb_personne, debit) VALUES(?,?)");
        $insertnewtime->execute(array($count, $debit));
    }
}

if (isset($_GET['count_now']) && isset($_GET['key_now']) && isset($_GET['debit_now'])) {
    $key = $_GET['key_now'];
    $count = $_GET['count_now'];
    $debit = $_GET['debit_now'];
    $count = decryptage($count, $key);
    $debit = decryptage($debit, $key);
    if (is_numeric($count) && is_numeric($debit) && is_numeric($key)) {
        echo $key, "</br>", $count, "</br>", $debit;
        $insertnewtime = $bdd->prepare("INSERT INTO now_time(nb_personne, debit) VALUES(?,?)");
        $insertnewtime->execute(array($count, $debit));
    }
}

$cantine = $bdd->prepare('SELECT * FROM time ORDER BY id DESC LIMIT 1');
$cantine->execute(array());
$cantine = $cantine->fetch();

$date = $cantine['temps'];

$nb_personne = $cantine['nb_personne'];
$temps = $nb_personne * 9;
$temps_h = intval($temps / 3600);
$temps_m = intval(($temps % 3600) / 60);

$datejour = date("Y-m-d ");
$datejour1 = date('Y-m-d', strtotime("+1 day"));
$interval = 5;

$data1 = historique_temps($datejour, $datejour1, $interval);
$data = historique_personne($datejour, $datejour1, $interval);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>TimeToEat - Accueil</title>
    <meta name="description" content="Consultez la fréquentation, le temps d'attente en temps réel de la cantine du Heinrich-Nessel ou que vous soyez !">
    <meta name="language" content="fr">
    <meta name="keywords" content="timetoeat, time-to-eat, menu, cantine, temps, attente, heinrich nessel, st philomene">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="icon" type="image/png" href="img/TimeToEat.png">
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-117519485-3"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', 'UA-117519485-3');
    </script>
</head>

<body>
    <div class="header">
        <div class="home">
            <div class="titre">
                <h1 class="titre-header">TimeToEat</h1>
                <h1 class="slogan-header">Vous ne perdrez plus votre temps à attendre</h1>
            </div>
            <a class="js-scrollTo" href="#attente">
                <div class="btn-attente">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" width="50px" height="50px" viewBox="0 0 444.819 444.819" xml:space="preserve">
                        <g>
                            <path d="M434.252,114.203l-21.409-21.416c-7.419-7.04-16.084-10.561-25.975-10.561c-10.095,0-18.657,3.521-25.7,10.561 L222.41,231.549L83.653,92.791c-7.042-7.04-15.606-10.561-25.697-10.561c-9.896,0-18.559,3.521-25.979,10.561l-21.128,21.416   C3.615,121.436,0,130.099,0,140.188c0,10.277,3.619,18.842,10.848,25.693l185.864,185.865c6.855,7.23,15.416,10.848,25.697,10.848   c10.088,0,18.75-3.617,25.977-10.848l185.865-185.865c7.043-7.044,10.567-15.608,10.567-25.693   C444.819,130.287,441.295,121.629,434.252,114.203z">
                            </path>
                        </g>
                    </svg>
                </div>
            </a>
        </div>
        <div class="menu">
            <div class=" menu-burger">
                <div class="conteneur-burger">
                    <div class="burger">
                    </div>
                </div>
            </div>
            <ul id="menu">
                <li>
                    <a href="#">Accueil</a>
                </li>
                <li>
                    <a href="historique.php">Historique</a>
                </li>
                <li>
                    <a href="realtime.html">Temps réel</a>
                </li>
                <li>
                    <a href="more.html">Qui sommes-nous ?</a>
                </li>
            </ul>
        </div>
    </div>
    <section class="contenu">
        <div id="attente" class="temps-attente">
            <h1>Temps d'attente actuel</h1>
            <div class="conteur">
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
            </div>
        </div>
        <div class="graph">
            <h1>Historique du jour </h1>
            <canvas id="myChart" height="70px"></canvas>
            <a href="historique.php">Voir plus</a>
        </div>
        <div class="menu-cantine">
            <p>Menu de la semaine</p>
            <img src="img/menu.jpg?<?= filemtime("img/menu.jpg"); ?>" alt="Menu de la semaine">
        </div>
        <div class="menu-cantine">
            <p>Menu de la semaine Sainte-Philomène</p>
            <img src="img/menu1.jpg?1579413633" alt="menu de la semaine">
        </div>
    </section>
    <script src="js/jquery.min.js"></script>
    <script src="js/Chart.min.js"></script>
    <script src="js/index.js"></script>
    <script>
        window.onload = function() {
            var ctx = document.getElementById('myChart').getContext('2d');
            var chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['11h30', '11h35', '11h40', '11h45', '11h50', '11h55', '12h00', '12h05', '12h10', '12h15', '12h20', '12h25', '12h30', '12h35', '12h40', '12h45', '12h50', '12h55', '13h00', '13h05', '13h10', '13h15', '13h20', '13h25', '13h30'],
                    datasets: [{
                        label: "Temps d'attente",
                        backgroundColor: 'rgba(211, 84, 0, 0.50)',
                        borderColor: 'rgb(150, 60, 0)',
                        data: <?= $data1 ?>,
                        yAxisID: 'y-axis-2',
                    }, {
                        label: "Nombre de personnes",
                        backgroundColor: 'rgba(230, 126, 34,0.5)',
                        borderColor: 'rgba(230, 126, 34,1.0)',
                        data: <?= $data ?>,
                        yAxisID: 'y-axis-1'
                    }],
                },
                options: {
                    tooltips: {
                        mode: 'index',
                        intersect: false
                    },
                    responsive: true,
                    scales: {
                        yAxes: [{
                            type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
                            display: true,
                            position: 'left',
                            id: 'y-axis-1',
                        }, {
                            type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
                            display: true,
                            position: 'right',
                            id: 'y-axis-2',
                            gridLines: { // grid line settings
                                drawOnChartArea: false, // only want the grid lines for one axis to show up
                            },
                        }],
                    }
                }
            });
        };
    </script>
</body>

</html>