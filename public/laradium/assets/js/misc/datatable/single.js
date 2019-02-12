(window["webpackJsonp"] = window["webpackJsonp"] || []).push([["/laradium/assets/js/misc/datatable/single"],{

/***/ "./resources/assets/js/misc/datatable/single.js":
/*!******************************************************!*\
  !*** ./resources/assets/js/misc/datatable/single.js ***!
  \******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function($) {$(function () {
  var dataTable = $('#' + datatable_config.id).DataTable({
    processing: true,
    serverSide: true,
    ajax: datatable_config.slug + '/data-table',
    columns: datatable_config.columns,
    order: datatable_config.order
  }).on('draw.dt', function () {
    $('.js-editable').editable({
      error: function error(response, newValue) {
        if (response.status !== 422) {
          return 'Something went wrong, please, try again later.';
        }

        return response.responseJSON.message;
      }
    });
    $.fn.tooltip && $('[data-toggle="tooltip"]').tooltip(); // switchUpdate();
  });
});
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js")))

/***/ }),

/***/ 1:
/*!************************************************************!*\
  !*** multi ./resources/assets/js/misc/datatable/single.js ***!
  \************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /Users/danielsgrietins/Projects/packages/laradium/resources/assets/js/misc/datatable/single.js */"./resources/assets/js/misc/datatable/single.js");


/***/ })

},[[1,"/laradium/assets/js/manifest","/laradium/assets/js/vendor"]]]);