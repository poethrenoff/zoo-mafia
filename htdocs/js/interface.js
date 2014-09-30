function compareItem(id, button, confirm){
    var $button = $(button);
    $.get('/compare/add/' + id, {'confirm': confirm}, function (response){
        if (response.error) {
            alert(response.error);
        } else if (response.confirm && window.confirm(response.confirm)) {
            compareItem(id, button, true);
        } else if (response.message) {
            $('.compare').html(response.message);
            $button.hide('slow');
        }
    }, 'json');
    return false;
}