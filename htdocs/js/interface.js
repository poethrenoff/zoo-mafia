function compareItem(id, compareLink, confirm){
    var $compareLink = $(compareLink);
    $.get('/compare/add/' + id, {'confirm': confirm}, function (response){
        if (response.error) {
            alert(response.error);
        } else if (response.confirm && window.confirm(response.confirm)) {
            compareItem(id, button, true);
        } else if (response.message) {
            $('.compare').html(response.message);
            $compareLink.hide('slow');
        }
    }, 'json');
    return false;
}

function buyItem(buyLink){
    var pid = $(buyLink).attr('pid');
    var $product_value = $('.product_value[pid=' + pid + ']');
    var $product_select = $('.product_select[pid=' + pid + ']');
    
    var $in_basket = $('.in-basket[pid=' + pid + ']');
    
    var id = $product_select.val();
    var quantity = $product_value.html();
    
    $.get('/cart/add/' + id + '/', {quantity: quantity}, function (response){
        $(".cart").html(response);
        $in_basket.show('slow');
    });
    
    return false;
}

function incItem(incLink){
    return shiftItem(incLink, +1);
}

function decItem(decLink){
    return shiftItem(decLink, -1);
}

function shiftItem(shiftLink, shift){
    var $row = $(shiftLink).parents('tr:first');
    var $qntInput = $row.find('input[name^=quantity]');
    var $priceInput = $row.find('input[name^=price]');
    var qnt = parseInt($qntInput.val());
    var price = parseInt($priceInput.val());
    var $qntCell = $row.find('td').eq(3);
    var $costCell = $row.find('td').eq(4);
    
    qnt = qnt + shift;
    
    if (qnt > 0) {
        $qntInput.val(qnt);
        $qntCell.html(qnt);
        $costCell.html(qnt * price);
        
        updateCart();
    }
    
    return false;
}

function updateCart(){
    var totalQnt = 0; var totalSum = 0;
    $('#cart').find('input[name^=quantity]').each(function(){
        var $qntInput = $(this);
        var $priceInput = $qntInput.parent().find('input[name^=price]');
        var qnt = parseInt($qntInput.val());
        var price = parseInt($priceInput.val());
        totalQnt += qnt;
        totalSum += qnt * price;
    });
    
    var $totalRow = $('#cart').find('tr:last');
    var $totalQntCell = $totalRow.find('td').eq(1);
    var $totalSumCell = $totalRow.find('td').eq(2);
    $totalQntCell.find('strong').html(totalQnt);
    $totalSumCell.find('strong').html(totalSum);
    
    $('#cart').ajaxSubmit(function(response){
        $(".cart").html(response);
    });
}

function callback() {
    $.get('/callback', function (response){
        $(response).modal({
            opacity: 30,
            overlayClose: true,
            closeHTML: '<a class="modalCloseImg" title="Закрыть"></a>'
        });
    });
    return false;
}

function consultItem(id){
    $.get('/consult/' + id, function (response){
        $(response).modal({
            opacity: 30,
            overlayClose: true,
            closeHTML: '<a class="modalCloseImg" title="Закрыть"></a>'
        });
    });
    return false;
}

function product_shift(productLink, shift) {
    var pid = $(productLink).attr('pid');
    var $product_value = $('.product_value[pid=' + pid + ']');
    var product_value = parseInt($product_value.html());
    if (!isNaN(product_value)) {
        product_value = product_value + shift;
        if (product_value > 0 && product_value < 10) {
            $product_value.html(product_value);
        }
    }
    return false;        
}

$(function() {
    $('.product_select').change(function() {
        var pid = $(this).attr('pid');
        var product_price = $(this).find('option:selected').attr('price');
        $('.product_price[pid=' + pid + ']').html(product_price + ' руб');
    }).change();

    $('.product_inc').click(function() {
        return product_shift(this, 1);
    });
    $('.product_dec').click(function() {
        return product_shift(this, -1);
    });
    
    $('.brand-slideshow').cycle();
    $('.teaser-slideshow').cycle();
    
    $(document).bind('click', function(e) {
        var $target = $(e.target);
        if (!($target.is('.in-basket') || $target.parents('.in-basket').length)) {
            $('.in-basket').hide('slow');
        }
    });
});
