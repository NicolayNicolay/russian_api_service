$(function () {
  var slideout = new window.Slideout({
    'panel': document.getElementById('bd_content'),
    'menu': document.getElementById('bd_sidebar'),
    'padding': 220,
    'tolerance': 70,
    touch: false
  }).on('beforeopen', function () {
    var height = $(window).outerHeight() > $(window).height() ? $(window).outerHeight() : $(window).height();
    $('#bd_sidebar').height(height);
    $('html, body').css({
      height: height + 'px',
      width: '100%',
      overflow: 'hidden'
    });
  }).on('close', function () {
    $('html, body').removeAttr('style');
  });

  $('.open-menu, .close-menu').on('click', function (e) {
    e.preventDefault();
    slideout.toggle();
  });

  $(window).resize(function () {
    if (window.innerWidth >= 768 && $('html').hasClass('slideout-open')) {
      slideout.toggle();
    }
  });
});
