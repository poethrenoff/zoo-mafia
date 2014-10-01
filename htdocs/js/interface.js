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
    var $buyLink = $(buyLink);
    var id = $buyLink.siblings('select').val();
    var quantity = $buyLink.siblings('input').val();
    
    $.get('/cart/add/' + id + '/', {quantity: quantity}, function (response){
        $(".cart").html(response);
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