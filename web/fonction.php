<?php
include('pwd.php');
setlocale(LC_TIME, 'fr_FR', 'fra');
ini_set('memory_limit', '-1');

try {
    $bdd = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

function decryptage($chaine, $cle)
{
    $alpha = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $liste = "";
    $i = 0;
    while ($i < strlen($chaine)) {
        try {
        $index = strpos($alpha, $chaine[$i]);
            $numero = $index - $cle;
            $cle = $cle + $cle;
            if ($cle > strlen($alpha)) {
                $cle = $cle - strlen($alpha) - 1;
            }
            if ($numero < 0) {
                $numero = $numero + strlen($alpha);
            }
            $liste = $liste . $alpha[$numero];
        } catch (Exception $e) {
            $liste = $liste . $chaine[$i];
        }
        $i++;
    }
    return $liste;
}

function historique_temps($datemin, $datemax, $interval)
{
    global $bdd;
    // Récupération des valeurs du jour
    $cantine = $bdd->prepare('SELECT nb_personne, debit, id FROM time WHERE temps > "' . $datemin . '" AND temps < "' . $datemax . '"  ORDER BY `time`.`id` DESC ');

    $cantine->execute(array());
    $cantine = $cantine->fetchall(); // Organiser les valeurs recupérées en tableaux
    $data = array();
    $z = 0;
    $i = 0;
    $i = count($cantine)  - 1;
    $nb_personne_total = 0;
    $debit_total = 0;
    for ($i; $i > 0; $i--) {
        $nb_personne = $cantine[$i][0];
        $debit = $cantine[$i][1];
        $nb_personne_total = $nb_personne_total + $nb_personne;
        $debit_total = $debit_total + $debit;
        $z = $z + 1;
        if ($z == $interval) {
            $moy_temps_attente = $nb_personne_total / $debit_total;
            $data[] = round($moy_temps_attente);
            $z = 0;
            $nb_personne_total = 0;
            $debit_total = 0;
        }
    }
    $data = json_encode($data);
    return $data;
}

function historique_personne($datemin, $datemax, $interval)
{
    global $bdd;
    // Récupération des valeurs du jour
    $cantine = $bdd->prepare('SELECT nb_personne FROM time WHERE temps > "' . $datemin . '" AND temps < "' . $datemax . '" ORDER BY `id` DESC ');
    $cantine->execute(array());
    $cantine = $cantine->fetchall(); // Organiser les valeurs recupérées en tableaux
    $data = array();
    $z = 0;
    $total_personne = 0;
    $i = count($cantine) - 1;
    for ($i; $i > 0; $i--) {
        $nb_personne = $cantine[$i][0];
        $total_personne = $total_personne + $nb_personne;
        $z = $z + 1;
        if ($z == $interval) {
            $moy_personne = $total_personne / $interval;
            $data[] = round($moy_personne);
            $z = 0;
            $total_personne = 0;
        }
    }
    $data = json_encode($data);
    return $data;
}

function historique_debit($datemin, $datemax, $interval)
{
    global $bdd;
    // Récupération des valeurs du jour
    $cantine = $bdd->prepare('SELECT debit FROM time WHERE temps > "' . $datemin . '" AND temps < "' . $datemax . '" ORDER BY `id` DESC ');
    $cantine->execute(array());
    $cantine = $cantine->fetchall(); // Organiser les valeurs recupérées en tableaux
    $data = array();
    $z = 0;
    $date_m = $date = date("Y-m-01", strtotime($datemin));
    $jour = date("N", strtotime($date_m));
    $i = count($cantine) - 1;
    $week = 0;
    $total_debit = 0;
    for ($i; $i > 0; $i--) {
        if ($jour == 6) {
            $data[] =  $week;
            $data[] =  $week;
            $jour = 1;
        }
        $debit = $cantine[$i][0];
        $total_debit = $total_debit + $debit;
        $z = $z + 1;

        if ($z == $interval) {
            $moy_debit = $total_debit;
            $data[] = round($moy_debit);
            $z = 0;
            $total_debit = 0;
            $jour = $jour + 1;
        }
    }
    $data = json_encode($data);
    return $data;
}

function historique_date($datemin, $datemax)
{
    global $bdd;
    // Récupération des valeurs du jour
    $cantine = $bdd->prepare('SELECT temps FROM time WHERE temps > "' . $datemin . '" AND temps < "' . $datemax . '" ORDER BY `id` DESC ');
    $cantine->execute(array());
    $cantine = $cantine->fetchall(); // Organiser les valeurs recupérées en tableaux
    $data = array();
    $date_m = $date = date("Y-m-01", strtotime($datemin));
    $jour = date("N", strtotime($date_m));
    $i = count($cantine) - 1;
    for ($i; $i > 0; $i--) {
        if ($jour == 6) {
            $date = $cantine[$i][0];
            $data[] = utf8_encode(strftime("%A %d %B ", strtotime($date . " -2 days")));
            $data[] = utf8_encode(strftime("%A %d %B ", strtotime($date . " -1 days")));
            $jour = 1;
        }
        $date = $cantine[$i][0];
        $date = utf8_encode(strftime("%A %d %B ", strtotime($date)));
        $data[] = $date;
        $i = $i - 130;
        $jour = $jour + 1;
    }
    $data = json_encode($data);
    return $data;
}

function historique_mois($date)
{
    global $bdd;
    $date_min = date('Y-m-01', strtotime($date));
    $date_max = utf8_encode(strftime("%A %d %B ", strtotime($date . " +1 month")));
    $cantine = $bdd->prepare('SELECT temps, sum(debit) FROM time WHERE temps > "' . $date_min . '" AND temps < "' . $date_max . '" GROUP BY  DAY(temps) ORDER BY id ASC');
    $cantine->execute(array());
    $val = array();
    $date = array();
    $datejour = $date_min;
    $cantine = $cantine->fetchall(PDO::FETCH_ASSOC);
    $z = count($cantine) - 1;
    for ($i = 0; $i <= $z;) {
        $debit = $cantine[$i]['sum(debit)'];
        $date_bdd = date('Y-m-d', strtotime($cantine[$i]['temps']));
        if ($datejour == $date_bdd) {
            $val[] = $debit;
            $i++;
        } else {
            $val[] = 0;
        }
        $date[] = utf8_encode(strftime("%A %d %B ", strtotime($datejour)));
        $datejour = date('Y-m-d', strtotime($datejour . "+1 day"));
    }
    return $date;
}

if (isset($_GET['date'])) {
    $datejour = $_GET['date'];
    $datejour1 = $date = date("Y-m-d", strtotime($datejour . " +1 days"));
    $interval = 5;
    $data = historique_temps($datejour, $datejour1, $interval);
    $data1 = historique_personne($datejour, $datejour1, $interval);
    echo $data;
    echo "/";
    echo $data1;
}

if (isset($_GET['date_m'])) {
    $date = $_GET['date_m'];
    $date_min = date('Y-m-01', strtotime($date));
    $date_max = $date = date("Y-m-01", strtotime($date . " +1 month"));
    $cantine = $bdd->prepare('SELECT temps , sum(debit) FROM time WHERE temps > "' . $date_min . '" AND temps < "' . $date_max . '" GROUP BY  DAY(temps) ORDER BY id ASC');
    $cantine->execute(array());
    $val = array();
    $date = array();
    $datejour = $date_min;
    $cantine = $cantine->fetchall(PDO::FETCH_ASSOC);
    $z = count($cantine) - 1;
    for ($i = 0; $i <= $z;) {
        $debit = $cantine[$i]['sum(debit)'];
        $date_bdd = date('Y-m-d', strtotime($cantine[$i]['temps']));
        if ($datejour == $date_bdd) {
            $val[] = $debit;
            $i++;
        } else {
            $val[] = 0;
        }
        $date[] = utf8_encode(strftime("%A %d %B ", strtotime($datejour)));
        $datejour = date('Y-m-d', strtotime($datejour . "+1 day"));
    }
    $data = json_encode($val);
    $val = json_encode($date);
    echo $data;
    echo "/";
    echo $val;
}

if (isset($_GET['r'])) {
    $data = $bdd->prepare('SELECT nb_personne, debit FROM now_time  ORDER BY `temps` DESC ,`id` DESC LIMIT 1');
    $data->execute(array());
    $data = $data->fetch(); // Organiser les valeurs recupérées en tableaux
    $nb_personne = $data["nb_personne"];
    $debit = $data["debit"];
    echo $data["nb_personne"];
    echo "/";
    echo $data["debit"];
}
