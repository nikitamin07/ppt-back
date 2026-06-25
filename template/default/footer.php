<?php
if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}
?>
    
</div>
<div id="callback-modal" class="site-modals modal fade-scale" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
		<div class="modal-header">
			<div class="modal-title">
				Закажите звонок
			</div>
			<div class="modal-subtitle">
				Наш специалист свяжется с вами после оставления заявки.
			</div>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">
			<form id="sendRequestTg" autocomplete="off">
				<div class="input-group">
					<label>
						Имя
						<span style="color: red; "><span class="form-required starrequired">*</span></span>
					</label>
					<input autocomplete="off" id="requestName" name="name" class="form-control" type="text" required/>
				</div>
				<div class="input-group">
					<label>
						Телефон
						<span style="color: red; "><span class="form-required starrequired">*</span></span>
					</label>
					<input autocomplete="off" id="requestPhone" name="phone" class="form-control" type="text" required/>
				</div>
				<div class="form-group">
					<div class="form-user-consent">
						<label>
							<input required id="personalAgreement" name="policy" class="" type="checkbox" checked="">
							<span class="checkbox-text">
								Я согласен на обработку персональных данных
							</span>
						</label>
					</div>				
				</div>
				<input type="hidden" id="recaptchaResultToken">
				<div class="recaptcha-container">
				</div>
				<button type="submit" class="btn btn-primary" >Отправить</button>
				<div class="text-required"><span style="color: red; "><span class="form-required starrequired">*</span></span> - обязательные поля </div>
				<div id="modal_errors"></div>
				<div id="modal_success"></div>
			</form>
		</div>
    </div>
  </div>
</div>
    
<footer class="footer">
	<div class="container">
		<div class="row foot1">
			<div class="pcol col-12 col-sm-6 col-lg-3">
				<div class="foot-title">
					О компании
				</div>
				<ul class="foot-menu">
					<li><a href="./company">О нас</a></li>
					<li><a href="./delivery">Доставка</a></li>			
					<li><a href="./contacts">Контакты</a></li>	
					<li>
						<span data-toggle="modal" data-target="#callback-modal" class="btn-callback">
							Заказать звонок
						</span>
					</li>				
				</ul>
			</div>
			<div class="pcol col-12 col-sm-6 col-lg-3">
				<div class="phone-wr">
					<i class="fas fa-phone-alt"></i>
					<a href="tel:80296918417">8 (029) 691-84-17</a>
				</div>
				<div class="phone-wr">
					<i class="fas fa-phone-alt"></i>
					<a href="tel:80259264845">8 (025) 926-48-45</a>
				</div>
				<div class="phone-wr">
					<i class="fas fa-envelope"></i>
					<a href="mailto:7206856@mail.ru">7206856@mail.ru</a>
				</div>
				<div class="work-time">
					<i class="far fa-clock"></i>
					Время работы: Пн-Пт (9.00 - 18.00)
				</div>
				<div class="social-wr">
					<a class="social-img tg" target="_blank" href="https://t.me/+375296918417">
						<img alt="Написать в Телеграм" src="template/inc/images/telegram.svg" width="32" height="32">
					</a>
					<a class="social-img" target="_blank" href="viber://chat?number=%2B375296918417">
						<img alt="Написать в Вайбер" src="template/inc/images/viber.svg" width="32" height="32">
					</a>
				</div>
			</div>
			<div class="pcol col-12 col-sm-12 col-lg-6">
			<div class="footer-map-yandex" style="width: 100%; height: 250px; position:relative;overflow:hidden;"></div>

			</div>
		</div>
		<div class="foot2 row align-items-center">
			<div class="pcol col-12 col-md-6 copybox">
				<div class="copyright">
					© ЧТУП "РешениеСтройДизайн", 2020. Продажа пенопласта в Минске.
				</div>
			</div>
			<div class="pcol col-12 col-md-6">
				<div class="footer-logo">
					<a href="./">
						<img width="60" height="52" height="48" alt="logo" src="template/inc/images/logo.webp" onerror="this.src='template/inc/images/no_image.jpg'"/>
					</a>
				</div>
			</div>
			<div class="col-12 col-md-6 copybox">
				<div class="copyright">
					Разработано <a href="https://vk.com/dyinglight07" target="_blank">Nikita Minchukou</a> 
				</div>
			</div>
		</div>
	</div>
</footer>
   
<span class="f_up"><i class="fa fa-angle-up"></i></span>


   
	
    <!--css-->
    <?php output_styles('footer');  ?>
		

    <!--js-->
	<?php output_scripts('footer'); ?>

</body>
</html>
