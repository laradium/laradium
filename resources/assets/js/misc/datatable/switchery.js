export default () => {
    let elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));

    elems.forEach(function (html) {
        if (!$(html).data('switchery')) {
            new Switchery(html, {
                disabled: $(html).data('disabled') === 'yes' ? true : false
            });
        }
    });
}