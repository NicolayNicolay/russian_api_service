import {toasts} from "./helpers/toasts";

document.addEventListener('DOMContentLoaded', function () {
  if (document.getElementsByClassName('header-nav__mobile-pull')[0] !== undefined) {
    document.getElementsByClassName('header-nav__mobile-pull')[0].addEventListener('click', function () {
      document.getElementsByClassName('header-nav__mobile')[0].classList.toggle('active');
    })
  }
})

$(function () {
  var myUrl = new URL(window.location.href.replace(/#/g, "?"));

  if (myUrl.searchParams.has("auth-fail")) {
    $('.index-container-scroll').animate({
      scrollTop: $('#auth-fail').offset().top
    });
  }

  return false;
});

// Уведомления об отсутствии прав на совершение действия
$(document).ready(function() {
  let disabled = $('a.forbidden');
  disabled.each((i, link) => {
    $(link).bind('click', function() {
      toasts.showToast('У вас нет прав на совершение этого действия',
        'toastAlertDanger',
        ['text-white', 'bg-danger']);
    })
  })
})

window.breakPoitable = {
  data: function () {
    return {
      breakPoint: {},
    }
  },
  methods: {
    setBreakPointValues: function () {
      var width = window.innerWidth;
      this.breakPoint = {
        xs: width <= 767,
        sm: width > 767 && width <= 991,
        md: width > 991 && width <= 1199,
        lg: width > 1199 && width <= 1439,
        xl: width > 1439 && width <= 1900,
        xxl: width > 1900,

        leSm: width <= 991,
        leMd: width <= 1199,
        leLg: width <= 1439,
        leXl: width <= 1900,
        leXxl: width <= 9999,

        geSm: width > 767,
        geMd: width > 991,
        geLg: width > 1199,
        geXl: width > 1439,
        geXxl: width > 1900,
      }
    }
  },
  created: function () {
    this.setBreakPointValues()
    var resizeTimer = null;
    window.addEventListener('resize', function (event) {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(function () {
        console.log('resized');
        this.setBreakPointValues()
      }, 50)
    }, true);
  },
}
window.filtrable = {
  filters: {
    integer: function (value) {
      if (value) {
        return value.toLocaleString('ru-RU', {maximumFractionDigits: 0});
      } else {
        return value;
      }
    },
    number: function (value) {
      if (value) {
        return value.toLocaleString('ru-RU', {maximumFractionDigits: 2, minimumFractionDigits: 2});
      } else {
        return value;
      }
    },
    date: function (value) {
      var date = moment(value)
      return date.format('YYYY-MM-DD HH:mm:ss');
    },

  }
};
window.routable = {
  data: function () {
    return {
      errors: {},
      routes: {},
      options: {},
      formProcessing: false,
      loadTimer: null,
      loading: true,
    }
  },

  methods: {
    dummyFunc: function () {
    },
    route: function (route, val, val2, val3) {
      var link = this.routes[route];
      if (!link) {
        link = route;
      }
      link = link.replace('%1', val);
      link = link.replace('%2', val2);
      link = link.replace('%3', val3);
      link = link.replace('__1', val);
      link = link.replace('__2', val2);
      link = link.replace('__3', val3);
      link = link.replace('%', val);
      if (val2) {
        link = link.replace('name', val2);
      }
      return link;
    },
    redirect: function (url) {
      window.location.href = url;
    },
  },
  mixins: [filtrable],
};

