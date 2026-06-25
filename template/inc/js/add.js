function number_format( number, decimals, dec_point, thousands_sep ) {	

	var i, j, kw, kd, km;

	
	if( isNaN(decimals = Math.abs(decimals)) ){
		decimals = 2;
	}
	if( dec_point == undefined ){
		dec_point = ",";
	}
	if( thousands_sep == undefined ){
		thousands_sep = ".";
	}

	i = parseInt(number = (+number || 0).toFixed(decimals)) + "";

	if( (j = i.length) > 3 ){
		j = j % 3;
	} else{
		j = 0;
	}

	km = (j ? i.substr(0, j) + thousands_sep : "");
	kw = i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep);
	
	kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).replace(/-/, 0).slice(2) : "");


	return km + kw + kd;
}

function onSuccessCaptcha(token){
	$('#recaptchaResultToken').val(token);
}
	$('#sendRequestTg').on('submit', function(e){
		e.preventDefault();
		var name = $('#requestName').val();
		var tel_number = $('#requestPhone').val();
		var policy = $('#personalAgreement').prop('checked');
		
		var product = 'none';
		if($('.modal-body #product_name').length!=0){
			product = $('.modal-body #product_name').val();
		}
		
		var errors = [];
		
		var ppattern  = /(?:\+375|80)\s?\(?\d\d\)?\s?\d\d(?:\d[\-\s]\d\d[\-\s]\d\d|[\-\s]\d\d[\-\s]\d\d\d|\d{5})/;
		
		if (ppattern.test(tel_number) == false){
			errors.push('Введите корректный телефон.');
		} else if(!policy) {
			errors.push('Подтвердите согласие на обработку персональных данных.');
		}
		
		$('#modal_errors').html('');
		$('#modal_success').html('');
		if (errors.length > 0){
			$('#modal_errors').html(errors.join(' '));
		} else {
			var token = $('#recaptchaResultToken').val();
			$.ajax({
				url: 'notification.php',
				type: 'POST',
				data: {name: name, phone: tel_number, policy: policy, product: product, g_recaptcha_response: token},
				success: function(resp){
					if(resp=='success'){
						$('#requestName').val('');
						$('#requestPhone').val('');
						$('#modal_success').html('Заявка успешно отправлена! Наш специалист свяжется с Вами в ближайшее время');
					} else {
						$('#modal_errors').html($('#modal_errors').html() + resp);
					}
				}
			});
		}
	})

$('button[name = edit_page]').click( function () {
	$.post('functions/admin/edit_redirect.php',{href : location.href}, function (data) {
		data = JSON.parse(data);
		if (data.success) {
			window.location = data.url;
		} else {
			alert(data.error)
		}
	})
})

function delivery_back1() {
	$('#go_to_data').css("display", "block");
	$('.big-cart').css("display", "block");
	$('#go_to_delivery2').css("display", "none");
	$('.user-buy-data').css("display", "none");
	$('#empty_trash').css("display", "flex");
	$('#cart_page_name').text('Корзина');
}