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
});

$(function () {
    var ok = $('html').css('width');
    ok = parseInt(ok);
    $('.right').click(function () {
        var x = $(".caroussel-conteneur").css('margin-left');
        x = parseInt(x);
        x = x - ok;
        $(".caroussel-conteneur").css('margin-left', x);
    });
    $('.left').click(function () {
        var x = $(".caroussel-conteneur").css('margin-left');
        x = parseInt(x);
        x = x + ok;
        $(".caroussel-conteneur").css('margin-left', x);
    });
});