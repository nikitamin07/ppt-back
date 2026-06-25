
function setContentLook(){
	$('.admin-panel')
		.css('min-height', window.innerHeight - $('header').outerHeight())
		.css('margin-top', $('header').outerHeight());
		
	$('.ck-sticky-panel__content').css('top', $('header').outerHeight());
}

setContentLook();

window.addEventListener('resize', setContentLook);


$('#partition-name').on('input', function(){
	$('.partition-name').html(this.value);
});
$('#page-title').on('input', function(){
	$('.page-title').html(this.value);
});
$('#block-key').on('input', function(){
	$('.block-key').html(this.value);
});
$('#form-key').on('input', function(){
	$('.form-key').html(this.value);
});

if ($('#cke-form-editor').length > 0){
	var editor = CKEDITOR.replace( 'cke-form-editor', {});
	editor.on('change', function(){
		var content = $("<div/>").html(this.getData()).text();
		var fields = content.match(/\[.*?\]/gi);
		if (fields === null){
			fields = [];
		}
		var tmp = [];
		fields.forEach(function(item){
			tmp.push('['+item.slice(1, -1).replace('  ', ' ').split(' ')[0]+']');
		});
		
		$('.fields').html(tmp.join(', ')+'.');
	});
	var content = $("<div/>").html(editor.getData()).text();
	var fields = content.match(/\[.*?\]/gi);
	if (fields === null){
		fields = [];
	}
	var tmp = [];
	fields.forEach(function(item){
		tmp.push('['+item.slice(1, -1).replace('  ', ' ').split(' ')[0]+']');
	});
	
	$('.fields').html(tmp.join(', ')+'.');
}





CKEDITOR.replaceClass = 'cke';

	
	
$(document).on('click', '.remove-meta', function(){
	$(this).parents('.meta-field').remove();
});


$('.add-meta').click(function(){
	$('.meta-list').append('<div class="form-group meta-field">\
						<div class="row">\
							<div class="col-md-4">\
								<input type="text" name="meta_key[]" class="form-control mb-1" placeholder="'+s_locale['Ключ']+'">\
							</div>\
							<div class="col-10 col-md-7">\
								<textarea name="meta_value[]" class="form-control mb-1" placeholder="'+s_locale['Значение']+'"></textarea>\
							</div>\
							<div class="col-2 col-md-1 text-center">\
								<i class="text-danger fas fa-times remove-meta"></i>\
							</div>\
						</div>\
					</div>');
});


$('.render-img').each(function(){
	var $wrap = $('<div class="render-wrapper" style="display: flex; align-items: center;"><img style="min-width: 25px; margin-right: 5px; width: 25px;" src="'+this.value+'"></div>');
	$(this).parent().append($wrap);
	$(this).css('width', 'auto');
	$wrap.append(this);
});

$('.render-img').on('input', function(){
	$(this).prev().attr('src', this.value);
});

$('.open-file-browser').click(function(e){
	e.preventDefault();
	open_file_browser();
	return false;
});

var file_browser_callback = null;

function open_file_browser(callback){
	file_browser_callback = callback;
	var html = '<div class="modal" id="file-manager" tabindex="-1" role="dialog">\
	  <div class="modal-dialog modal-dialog-centered" role="document">\
		<div class="modal-content">\
		  <div class="modal-body">\
			<button style="right: -40px;" type="button" class="close" data-dismiss="modal" aria-label="Close">\
			  <span aria-hidden="true">&times;</span>\
			</button>\
			<iframe src="admin?cke_browse&file_browser='+(typeof callback != 'undefined' ? '&callback=' : '')+'" border="0" width="100%" height="500px">\
		  </div>\
		</div>\
	  </div>\
	</div>';
	$('body').append(html);
	$('#file-manager').modal('show');
	$('#file-manager').on('hidden.bs.modal', function (e) {
		parent.file_browser_callback = null;
		$('#file-manager').remove();
	})
}

function hide_file_browser(){
	$('#file-manager').modal('hide');
}

$('.mailing-sending-form').on('submit', function(event){
	event.preventDefault();
	//$('.mail-send-btn').text('В процессе').prop('disabled', true);
	var template = $('select[name="template"]').val();
	var mail_title = $('textarea[name="mail_title"]').val().trim();
	let current=0;
	let isAllSent = false;
	$('.mailing-results').each(function (){
		if($(this).children('.mailing-status[data-state="0"][data-error="0"]').length != 0){
			current = ($(this).attr("data-mail-iterator") - 1) / 14;
			isAllSent = true;
			return false;
		}
	});
	if(!isAllSent) {
		$('.mailing-results').each(function (){
			$(this).children('.mailing-status').attr('data-state', '0').attr('data-error', '0').text('Не отправлено');
		});
		current=0;
		alert('Письмо было отправлено всем получателям в списке. Теперь отправка начнется адресатам из начала списка.');
	}
	let lastItem = false;
	for(let i=0; i<50; i++){
		let adresses = [];
		current1 = current * 14;
		for(let j=1; j<=14; j++){
			if($('.mailing-results[data-mail-iterator="' + String(current1+j) + '"] .mailing-status[data-state="0"][data-error="0"]').length !== 0) {
				adresses.push({'id': $('.mailing-results[data-mail-iterator="' + String(current1+j) + '"]').attr('data-adress-id'), 'adress': $('.mailing-results[data-mail-iterator="' + String(current1+j) + '"] .mailing-adress').text().trim()});
			} else {
				lastItem = true;
				break;
			}
		}
		let email_adresses = JSON.stringify(adresses);
		$.ajax({
			url: '',
			method: 'post',
			dataType: 'json',
			data: {sending: true, template: template, email_adresses: email_adresses, mail_title: mail_title},
			success: function(data){
				data.forEach(setAdressState);
			}
		});
		current++;
		if(lastItem) break;
	}
return false;
	$('.mail-send-btn').text('Временно недоступно');
	var date = new Date();
	var now = date.getTime();
	now = now + 1000 * 3600;
	document.cookie = "mail_send_blocked=" + now + "; max-age=3600";
	countDownMail();
});

// $(document).ready( function(){
// 	var date = new Date();
// 	var now = date.getTime();
// 	var left_time = getCookie('mail_send_blocked') - now;
// 	if (left_time >= 0){
// 		$('.mail-send-btn').text('Временно недоступно').prop('disabled', true);
// 		countDownMail();
// 	}
// });

function getCookie(name) {
  var matches = document.cookie.match(new RegExp(
    "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
  ));
  return matches ? decodeURIComponent(matches[1]) : 0;
}

function countDownMail(){
	let date = new Date();
	let now = date.getTime();
	let left_time = getCookie('mail_send_blocked') - now;
	if (left_time <= 0){
		$('.mail-send-btn').text('Начать').prop('disabled', false);
		$('.mailing-blocked').text('');
	} else {
	let minutes = left_time > 0 ? Math.floor(left_time / 1000 / 60) % 60 : 0;
	let seconds = left_time > 0 ? Math.floor(left_time / 1000) % 60 : 0;
	$('.mailing-blocked').text('До следующей отправки: '+ minutes+' минут '+seconds+' секунд');
	setTimeout(countDownMail, 1000);
	}
}

function setAdressState(adressInfo) {
	$('.mailing-results[data-adress-id="' + String(adressInfo['id']) + '"] .mailing-status').attr('data-state', adressInfo['state']);
	if(adressInfo['error'] != 'noerror'){
		$('.mailing-results[data-adress-id="' + String(adressInfo['id']) + '"] .mailing-status').attr('data-error', '1');
		$('.mailing-results[data-adress-id="' + String(adressInfo['id']) + '"] .mailing-status').text('Не отправлено. ' + adressInfo['error']);
	} else {
		$('.mailing-results[data-adress-id="' + String(adressInfo['id']) + '"] .mailing-status').text('Отправлено');
		let not_sent = parseInt($('.mailing-not-sent').text());
		let mailing_errors = parseInt($('.mailing-errors').text());
	}
}