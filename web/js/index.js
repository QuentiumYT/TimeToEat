$(function () {
    $('.js-scrollTo').on('click', function () {
        var page = $(this).attr('href');
        var speed = 750;
        $('html, body').animate({
            scrollTop: $(page).offset().top
        }, speed);
        return false;
    });
});

$(function () {
    var conteneur_burger = document.getElementsByClassName('conteneur-burger');
    var menu1 = document.getElementById('menu');
    $(conteneur_burger).click(function () {
        $(menu1).slideToggle("slow");
    });
    var largeur_fenetre = $(window).width();
    if (largeur_fenetre < 600) {
        $(myChart).css('height', '200px');
    };
});