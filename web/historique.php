<?php
include('fonction.php');
setlocale(LC_TIME, 'fr_FR', 'fra');
ini_set('memory_limit', '-1');

$datejour = date("Y-m-d");
$datejour1 = date('Y-m-d', strtotime("+1 day"));
$datejour_m = date('Y-m-1');
$interval = 5;
$interval_m = 125;
$data = historique_temps($datejour, $datejour1, $interval);
$data1 = historique_personne($datejour, $datejour1, $interval);
$data1_m = historique_debit($datejour_m, $datejour, $interval_m);
$date = historique_date($datejour_m, $datejour);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>TimeToEat - Historique</title>
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
    <div class="menu">
        <div class=" menu-burger">
            <div class="conteneur-burger">
                <div class="burger">
                </div>
            </div>
        </div>
        <ul id="menu">
            <li>
                <a href="index.php">Accueil</a>
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
    <div class="graphique-conteneur">
        <h1>Historique du Jour</h1>
        <div class="graphique">
            <input id="date" type="date" name="date" value="<?= $datejour ?>">
            <a class="valider-date" href="fonction.php?date=">Actualiser</a>
            <canvas id="myChart" height="70"></canvas>
        </div>
    </div>
    <div class="graphique-conteneur">
        <h1>Historique du Mois</h1>
        <div class="graphique">
            <input id="date-m" type="month" name="date-m" value="<?= date("Y-m") ?>">
            <a class="valider-date-m" href="fonction.php?date_m=">Actualiser</a>
            <canvas id="myChart_m" height="70"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4"></script>
    <script src="js/historique.js"></script>
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
                            data: [],
                            yAxisID: 'y-axis-2',
                        },
                        {
                            label: "Nombre de personnes",
                            backgroundColor: 'rgba(230, 126, 34,0.5)',
                            borderColor: 'rgba(230, 126, 34,1.0)',
                            data: [],
                            yAxisID: 'y-axis-1'
                        }
                    ],
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
                            id: 'y-axis-2', // grid line settings
                            gridLines: {
                                drawOnChartArea: false, // only want the grid lines for one axis to show up
                            },
                        }],
                    }
                }
            });
            var ctx_2 = document.getElementById('myChart_m').getContext('2d');
            var chart_2 = new Chart(ctx_2, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: "Nombre de personnes",
                        backgroundColor: 'rgba(230, 126, 34,0.5)',
                        borderColor: 'rgba(230, 126, 34,1.0)',
                        data: [],
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
                        }],
                    }
                }
            });
            (function($) {
                $('.valider-date').on('click', function(e) {
                    e.preventDefault();
                    var $a = $(this);
                    var $value = $("#date").val();
                    var url = $a.attr('href') + $value;
                    $a.text('Chargement');
                    $.ajax(url)
                        .done(function(data, text, jqxhr) {
                            $reponse = data;
                            var reponse = $reponse.split('/');
                            $data_temps = reponse[0];
                            $data_nbpersonne = reponse[1];
                            var $data_temps = JSON.parse($data_temps);
                            var $data_nbpersonne = JSON.parse($data_nbpersonne);
                            chart.data.datasets[0].data = $data_temps;
                            chart.data.datasets[1].data = $data_nbpersonne;
                            chart.update();
                        })
                        .fail(function(jqxhr) {
                            alert(jqxhr.reponseText);
                        })
                        .always(function() {
                            $a.text('Actualiser');
                        });
                });
                $('.valider-date-m').on('click', function(e) {
                    e.preventDefault();
                    var $a = $(this);
                    var $value = $("#date-m").val();
                    var url = $a.attr('href') + $value;
                    $a.text('Chargement');
                    $.ajax(url)
                        .done(function(data, text, jqxhr) {
                            data_m = data;
                            data_m = data_m.split('/');
                            data_nbpersonne = data_m[0];
                            data_date = data_m[1];
                            data_date = JSON.parse(data_date);
                            data_nbpersonne = JSON.parse(data_nbpersonne);
                            chart_2.data.datasets[0].data = data_nbpersonne;
                            chart_2.data.labels = data_date;
                            chart_2.update();

                        })
                        .fail(function(jqxhr) {
                            alert(jqxhr.reponseText);
                        })
                        .always(function() {
                            $a.text('Actualiser');
                        });
                });
            })($);
        };
    </script>
</body>

</html>