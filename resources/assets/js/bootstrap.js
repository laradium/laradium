
window._ = require('lodash');
window.Popper = require('popper.js').default;

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    global.$ = global.jQuery = require('jquery');

    require('bootstrap');
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

window.datetimepicker = require('jquery-datetimepicker');
window.swal = require('sweetalert2');
window.toastr = require('toastr');
window.select2 = require('select2');
window.jstree = require('jstree');
window.toastr = require('toastr');
window.editable = require('x-editable/dist/bootstrap3-editable/js/bootstrap-editable.min');
window.slimScroll = require('jquery-slimscroll');

$.ajaxSetup({
	headers: {
		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	}
});

$(function() {
   $(document).on('click', 'li.has_sub a', function () {
        $(this).next('ul').slideToggle();
   });

   if($(document).find('li.has_sub a').hasClass('active')) {
       $('li.has_sub a.active').next('ul').slideDown()
   }
   
	$('.slimscrollleft').slimScroll({
		height: 'auto',
		position: 'right',
		size: "7px",
		color: '#828e94',
		wheelStep: 5
	});
});