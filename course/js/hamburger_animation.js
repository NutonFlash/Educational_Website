export function addHamburgerAnimation() {

    if ($('#hamburger').length !== 0) {
        $('#hamburger').hover(hamburgerHoverOver, hamburgerHoverOut);
        $('#hamburger').bind('click', function () {
            $('#sidebar').offcanvas('show');
        });
    }

    /* Hover animation of hamburger in the navbar */

    function hamburgerHoverOver() {
        let top = $('.hamb-top');
        let bottom = $('.hamb-bottom');
        top.addClass('transform');
        bottom.addClass('transform');
    }

    function hamburgerHoverOut() {
        let top = $('.hamb-top');
        let bottom = $('.hamb-bottom');
        top.removeClass('transform');
        bottom.removeClass('transform');
    }
}