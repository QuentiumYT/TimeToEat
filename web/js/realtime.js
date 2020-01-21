$(function () {
    var conteneur_burger = document.getElementsByClassName('conteneur-burger');
    var menu1 = document.getElementById('menu');
    $(conteneur_burger).click(function () {
        $(menu1).slideToggle("slow");
    });
    var largeur_fenetre = $(window).width();
    var graph = document.getElementById(myChart);
    if (largeur_fenetre < 600) {
        $(myChart).css('height', '200px');
    };
});

window.onload = function () {
    var ctx = document.getElementById('myChart').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [''],
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
                    id: 'y-axis-2',
                    // grid line settings
                    gridLines: {
                        drawOnChartArea: false, // only want the grid lines for one axis to show up
                    },
                }],
            }
        }
    });

    function tempsreel() {
        var url = "fonction.php?r=0";
        $.ajax(url)
            .done(function (data, text, jqxhr) {
                $data = data;
                var $data = $data.split('/');
                $data_nbpersonne = $data[0];
                $data_debit = $data[1];
                $temps = $data_nbpersonne / $data_debit;
                chart.data.datasets[1].data.push($data_nbpersonne);
                chart.data.datasets[0].data.push($temps);
                var time = new Date();
                var h = time.getHours();
                if (h < 10) {
                    h = "0" + h
                }
                var m = time.getMinutes();
                if (m < 10) {
                    m = "0" + m
                }
                var s = time.getSeconds();
                if (s < 10) {
                    s = "0" + s
                }
                var time = h + ":" + m + ":" + s;
                chart.data.labels.push(time);
                chart.update();
            })
            .fail(function (jqxhr) {
                alert(jqxhr.reponseText);
            })
        setTimeout(tempsreel, 10000);
    };
    tempsreel();
}