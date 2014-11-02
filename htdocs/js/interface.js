function compareItem(id, compareLink, confirm){
    var pid = $(compareLink).attr('pid');
    var $compareLink = $(compareLink);
    var $in_compare = $('.in-compare[pid=' + pid + ']');
    
    $.get('/compare/add/' + id, {'confirm': confirm}, function (response){
        if (response.error) {
            alert(response.error);
        } else if (response.confirm && window.confirm(response.confirm)) {
            compareItem(id, compareLink, true);
        } else if (response.message) {
            $('div.compare').html(response.message);
            $compareLink.prop('disabled', true);
            $in_compare.show('slow');
        }
    }, 'json');
    return false;
}

function buyItem(buyLink){
    var pid = $(buyLink).attr('pid');
    var $product_value = $('.product_value[pid=' + pid + ']');
    var $product_select = $('.product_select[pid=' + pid + ']');
    var $in_cart = $('.in-cart[pid=' + pid + ']');
    
    var id = $product_select.val();
    var quantity = $product_value.html();
    
    $.get('/cart/add/' + id + '/', {quantity: quantity}, function (response){
        $("div.cart").html(response);
        $in_cart.show('slow');
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
    var $qntCell = $row.find('td').eq(2);
    var $costCell = $row.find('td').eq(3);
    
    qnt = qnt + shift;
    
    if (qnt > 0) {
        $qntInput.val(qnt);
        $qntCell.find('span').html(qnt);
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
    var $totalQntCell = $totalRow.find('td').eq(2);
    var $totalSumCell = $totalRow.find('td').eq(3);
    $totalQntCell.html(totalQnt);
    $totalSumCell.html(totalSum);
    
    $('#cart').ajaxSubmit(function(response){
        $("div.cart").html(response);
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

function setMark(mark) {
    $('.vote .star').removeClass('active');
    $('.vote .star:lt(' + mark + ')').addClass('active');
}
function addReview(reviewLink) {
    $('#review').show('slow');
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
    $('.picture-slideshow').cycle();
    
    $('.picture-slideshow a').click(function(){
        var image = new Image();
        image.src = $(this).attr('href');
        image.onload = function() {
            $.modal("<img src='" + image.src + "'/>", {
                opacity: 30,
                overlayClose: true,
                closeHTML: '<a class="modalCloseImg" title="Закрыть"></a>'
            });
        };
        return false;
    });

    $(document).bind('click', function(e) {
        var $target = $(e.target);
        if (!($target.is('.in-cart') || $target.parents('.in-cart').length ||
                $target.is('.in-compare') || $target.parents('.in-compare').length)) {
            $('.in-cart').hide('slow'); $('.in-compare').hide('slow');
        }
    });

    $('input[href]').bind('click', function(e) {
        if (!$(this).attr('confirm') || confirm($(this).attr('confirm'))) {
            location.href = $(this).attr('href');
        }
    });
    
    $('select').selectric({
        inheritOriginalWidth: true
    });
    $('.brand select').on('change', function(){
        $(this).parents('form:first').submit();
    });

    $('.vote .star').mouseenter(function(){
        setMark($(this).attr('mark'));
    }).click(function(){
        $.post('/product/vote/' + $(this).attr('id'), {mark: $(this).attr('mark')}, function(data){
            var rating = Math.round(data.rating);
            $('.vote').attr('rating', rating); $('.vote').mouseleave();
        }, 'json');
    });
    $('.vote').mouseleave(function(){
        setMark($(this).attr('rating'));
    });
    
    $('.card .tabs div.off').click(function(){
        $('.card .tabs div.on').css('display', 'none');
        $('.card .tabs div.on[for="' + $(this).attr('for') + '"]')
            .css('display', 'inline-block');
    
        $('.card .areas div[rel]').hide();
        $('.card .areas div[rel="' + $(this).attr('for') + '"]').show();
    });
    $('.card .tabs div.off:first').click();

    $('#review').ajaxForm({
        dataType: 'json',
        success: function (response){
            if (response.error) {
                alert(response.error);
            } else if (response.message) {
                $('#review').hide(function() {
                    $('#review textarea').val('');
                    alert(response.message);
                });
            }
        }
    });
});
