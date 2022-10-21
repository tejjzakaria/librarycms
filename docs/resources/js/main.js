$(document).ready(function () {
    $(document).delegate('*[data-toggle="lightbox"]', 'click', function (e) {
        e.preventDefault();
        $(this).ekkoLightbox();
    });
    $(document).on('click', '.sidebar-nav a', function (e) {
        var _this = $(this);
        $('.sidebar-nav a.active').removeClass('active');
        $('.sidebar-nav li.active').removeClass('active');
        if ($(_this).hasClass('has-arrow')) {
            var temp = $(_this).next('ul');
            $('.sidebar-nav').find('ul.sub-menu').not(temp).collapse('hide');
            $(_this).closest('li').toggleClass('active');
            $(_this).toggleClass('active').next('ul').collapse('toggle');
        } else {
            $(_this).toggleClass('active');
        }
    });
    $('.sidebar-toggle').on('click', function () {
        $('body').toggleClass('closed');
    });
});