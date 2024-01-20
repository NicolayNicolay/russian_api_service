window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
  window.$ = window.jQuery = require('jquery');
  window.bs = require('bootstrap');
  require('bootstrap');
} catch (e) {
}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
  window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
  console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

let user_id = document.head.querySelector('meta[name="user_id"]');

import Echo from 'laravel-echo'

window.Pusher = require('pusher-js');

// if (user_id) {
//   window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     // cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     encrypted: false,
//     wsHost: window.location.hostname,
//     wsPort: 6001,
//     forceTLS: false,
//     disableStats: true,
//   });
//
//   window.Echo.private('App.User.' + user_id.content)
//     .listen('.user.notifications', (e) => {
//       Vue.notify({
//         group: 'notifications',
//         duration: -1,
//         title: e.title,
//         text: e.message,
//         type: 'info',
//         position: [
//           'bottom',
//           'right'
//         ],
//         closeOnClick: true
//       })
//     });
// }


