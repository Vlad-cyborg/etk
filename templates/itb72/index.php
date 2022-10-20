<?php

$app = JFactory::getApplication();
$menu = $app->getMenu();

defined('_JEXEC') or die('Restricted access');
?>
<!doctype html>
<html lang="ru">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src="/templates/itb72/js/jquery.min.js"></script>
<script src="/templates/itb72/bootstrap-4/js/bootstrap.bundle.js"></script>
<jdoc:include type="head" />
<?php $this->_generator = 'itb72';?>
<link rel="preload" as="style" onload="this.rel='stylesheet'" type="text/css" href="/templates/itb72/bootstrap-4/css/bootstrap.min.css">
<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700' rel='preload' as="font" onload="this.rel='stylesheet'" type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700&subset=latin,cyrillic-ext,cyrillic' rel='preload' as="font" onload="this.rel='stylesheet'" type='text/css'>
<link rel="stylesheet" type="text/css" href="/templates/itb72/css/template.min.v9.css">

<meta name="yandex-verification" content="7bcc82d1ddd0c37e" />
<meta name="google-site-verification" content="AcgkIWUJzdyo5ZsCRD47LsX_XiZp5bYSaAMciKOTk_g" />

<meta property="og:locale" content="ru_RU" />
<meta property="og:type" content="website" />
<meta property="og:title" content="Инженерные системы «ЭнергоТеплоКомплект»" />
<meta property="og:description" content="Инженерные системы, счетчики электроэнергии, светодиодное освещение, греющий кабель, терморегуляторы, теплый пол" />
<meta property="og:url" content="https://etk72.ru/" />
<meta property="og:site_name" content="«ЭнергоТеплоКомплект»" />
<meta property="og:image" content="https://etk72.ru/images/sys/logo.png" />
<?php 
$isPaginationPage = JRequest::getVar('start', null);
if ($_SERVER['QUERY_STRING']):
	  if (!isset($isPaginationPage)){ ?>
		<meta name="robots" content="noindex"/>
<?php } 

 endif; ?>
</head>
<body>

<div class="top_line"></div>


<?php
	//Проверяем куки для вывода предупреждения if($_COOKIE["personal_alert"] != 'ok'){
		if(isset($_COOKIE["p-alert"])){
	?>

<!-- noindex -->
<div class="personal_alert">
<button class="close-personal close">X</button>
<p>Для отображения персонализированной информации и хранения личных настроек на локальном компьютере веб-сайт www.etk72.ru используют технологию cookie и аналогичные.</p>
<p>Если Вы щелкните по кнопке «Закрыть» или продолжите использование данного веб-сайта без изменения настроек технологии cookie в своем браузере, то это будет означать Ваше согласие на применение этой технологии компанией ООО «Энерготеплокомплект».</p>
<button class="close-personal btn btn-success">Закрыть</button>
</div>
<!--/ noindex -->

<script async src="/templates/itb72/js/jquery.cookie.js"></script>

<script>
	$(document).ready(function(){
	 $('.close-personal').click(function(){
	  $('div.personal_alert').css('display','none');
		$.cookie('personal_alert', 'ok', { expires: 7, path: '/' });
	  return false;
	 });
	});
</script>

		<?php } ?>

<span class="mob-menu-cat">Каталог</span>
<span class="mob-menu-nav"><img src="/img/icons/burger.svg" alt=""></span>

<div class="container-fluid c-top">
<div class="container">
<div class="row">
<div class="col-md-6 col-lg-3"><jdoc:include type="modules" name="top_left" /></div>


<div class="col-lg-5 d-none d-lg-block"><jdoc:include type="modules" name="top_center" /></div>


<div class="col-md-6 col-lg-4"><jdoc:include type="modules" name="top_right" />

<div id="user_ip" style="display: none;"><span>{$smarty.server.REMOTE_ADDR}</span></div>

<script>
    var user_ip = $('.user_ip span').text();
        $.get('https://api.sypexgeo.net/json/'+user_ip,
        function(data){
            $('.user_city').html(data.city.name_ru);
			var ucity = data.city.name_ru;
			if(ucity == 'Тюмень'){
				if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)){
                  jQuery("#tel_span").replaceWith("<a href=\"tel:+73452578309\">+7 (3452) 578-309</a>");
                }else{jQuery("#tel_span").text(' +7 (3452) 578-309');
                     }
			}else{
				if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)){
                 jQuery("#tel_span").replaceWith("<a href=\"\"></a>");
                }else{jQuery("#tel_span").text(' ');
                     }
			}
        });
  		
  		$(document).ready(function(){
			
			if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
			
			//jQuery("#tel_span").replaceWith("<a href=\"tel:+73452578309\">+7 (3452) 578-309</a>");
			$(".desctop_tel_cart").addClass("cut-down");
			
			} else {
				
				$(".mobile_tel_cart").addClass("cut-down");
			}
		});
</script>

<div class="tel"><span class="bespl">Бесплатно по РФ</span> +7 (800) 777-32-09</div>
<div class="tel"><span class="bespl">Ваш город </span><span id="user-city" class="user_city"></span><span style="font-size:16px;" id="tel_span"></span></div>
<div class="email" style="font-size:18px;" ><span class="email_span">Заявки 24/7 на email:</span> 7773209@mail.ru</div>



<div class="call_back">
	<p>Получить тех. консультацию</p>
	<div id="modal_form_call">
					<span id="modal_close_call">X</span>
					<p>Для получения бесплатной технической консультации нашего специалиста заполоните поля:</p>
					<form action="" method="POST">
						<input required type="text" placeholder="Телефон" name="price_tel">
						<input type="text" placeholder="Город" name="price_name">
						<input type="text" style="display:none;" value="" name="price_hid">

						<div class="personal">
							<input style="margin:5px;cursor:pointer;width: 10px;" type="checkbox" name="checkme" id="agree" checked>
							«Нажимая на кнопку, Вы даете согласие на обработку своих персональных данных,
							с <a href="/personal" target="_blank">положением об обработке персональных данных</a>
							и <a href="/policy" target="_blank">политикой в отношении обработки персональных данных</a> ознакомлен»
							</div>

							<script type="text/javascript">
							$(document).ready(function(){

								$('#continue').prop('disabled', false);

								$('#agree').change(function() {

									$('#continue').prop('disabled', function(i, val) {
									return !val;
									})
								});
							})
							</script>

						<input id="continue" type="submit" value="ОТПРАВИТЬ" name="call_sub" style="background: #b22 none repeat scroll 0 0;color: #fff;width: 110px;">
					</form>
	</div>
	<div id="overlay"></div>
	<a href="#" id="modal_a_call">Заказать звонок</a>
</div>

</div>

</div>

<div class="cont_menu"><jdoc:include type="modules" name="menu" /></div>

<div class="adaptive_cart"><jdoc:include type="modules" name="cart" /></div>

<?php
// Позиция модуля который будет виден только на главной  //
if ($menu->getActive() == $menu->getDefault()) {
	?>
		<!-- <div class="cv">
			<script type="text/javascript" language="javascript" src="//cdn1.kursvaliut.ru/custom_widgets/get_widget.php?lw=155&rw=10&font=arial&cft=ff8310&ctt=ffffff&ttb=0&cc=f2f2f2&cfb=ffffff&ct=000000&pd=2&pc=5&aiv=1&val[]=14&val[]=7&mf=10&avc=0&ac=0&aod=0"></script>
			<noscript><a href="//www.kursvaliut.ru" title="курс валют">kursvaliut.ru</a></noscript>
		</div> -->
		<div class="home_fix"></div>
	<?php
	}
?>

</div>
</div>



<div class="slider"><jdoc:include type="modules" name="slider" /></div>




<dic class="clearfix"></dic>


<div class="container">

<div class="row">
<div class="col-md-3"><jdoc:include type="modules" name="middle_left" style="xhtml" /></div>

<div class="col-md-9 w-cont">
<div class="breadcr"><jdoc:include type="modules" name="bread" style="xhtml" /></div>
<jdoc:include type="component" /> <jdoc:include type="message" />
<?php
	if ($_SERVER['REQUEST_URI'] == "/zakaz-tovara"){
		?>
			<div  style="width: 50%;float:left;" class="form_contacts" >

				<h2>Отправьте заявку<br> или техническое задание</h2>



				<form action="" method="POST" enctype="multipart/form-data">
				<label for="fio">Имя или организация</label><input type="text" value="" name="fio" id="fio"><br>
				<label for="tel">Телефон или email</label><input required type="text" value="" name="tel" id="tel"><br>
				<label for="mess">Текст запроса</label><textarea name="mess" id="mess" style="width: 255px;"> </textarea><br>
				<label for="doc">Прикрепить файл</label><input id="doc" type="file" name="doc"><br>

							<div class="personal">
							  <input style="margin:5px;cursor:pointer;width: 10px;" type="checkbox" name="checkme" id="agree2" checked>
							  «Нажимая на кнопку, Вы даете согласие на обработку своих персональных данных,
							  с <a href="/personal" target="_blank">положением об обработке персональных данных</a>
							  и <a href="/policy" target="_blank">политикой в отношении обработки персональных данных</a> ознакомлен»
							  </div>

				<input id="continue2" style="background: #ff8310;border: 1px solid #ddd;padding: 10px 20px;margin: 10px 0px;" type="submit" name="sub_contacts" value="ОТПРАВИТЬ">
				</form>
			</div>
		<?php
	}
	if ($_SERVER['REQUEST_URI'] == "/kontakty" OR $_SERVER['REQUEST_URI'] == "/zakaz-tovara"){
		?>
							  <script type="text/javascript">
								$(document).ready(function(){

								  $('#continue2').prop('disabled', false);

								  $('#agree2').change(function() {

									  $('#continue2').prop('disabled', function(i, val) {
										return !val;
									  })
								  });
								})
							  </script>
		<?php
	}
?>
</div>

<div class="middle_right"><jdoc:include type="modules" name="middle_right" style="xhtml" /></div>
<div class="post_content"><jdoc:include type="modules" name="post_content" style="xhtml" /></div>



</div>
</div>





<div class="container-fluid footer_cont">
	<div class="container">
		<div class="row">
		<?php if ($this->countModules('footer_left')) : ?>
		<div class="col-md-8 text-light"><jdoc:include type="modules" name="footer_left" /></div>
		<?php endif; ?>
		<div class="col-md-4 text-light"><jdoc:include type="modules" name="footer_right" />
		Поделиться в соц. сетях
		<script async src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js"></script>
		<script async src="//yastatic.net/share2/share.js"></script>
		<div class="ya-share2" data-services="vkontakte,facebook,odnoklassniki,moimir" data-counter=""></div>

		</div>

		</div>
	</div>
</div>


  <?php if ($this->countModules('portfolio')) : ?>
<div class="portfolio_cont">
	<div class="portfolio"><jdoc:include type="modules" name="portfolio" /></div>
</div>
  <?php endif; ?>


<div class="container-fluid count_cont">
<div class="container">
  <?php if ($this->countModules('count_left')) : ?>
<div class="count_left"><jdoc:include type="modules" name="count_left" /></div>
  <?php endif; ?>

  <?php if ($this->countModules('count_right')) : ?>
<div class="count_right"><jdoc:include type="modules" name="count_right" /></div>
  <?php endif; ?>
<? if($_SERVER['REQUEST_URI']=='/') echo " <p class=\"itb\"><a rel=\"nofollow\" href=\"https://itbweb.ru\" >Создание сайта веб-студия «IT-Бизнес»</a></p> "; else {echo "<p class=\"itb\">Сайт разработан «IT-Бизнес»</p>";} ?>
</div>
</div>


<script>
	$(document).ready(function() {
	$('a#modal_a_call').click( function(event){
		event.preventDefault();
		$('#overlay').fadeIn(400,
		 	function(){
				$('#modal_form_call')
					.css('display', 'block')
					.animate({opacity: 1, top: '50%'}, 200);
		});
	});

	$('#modal_close_call, #overlay').click( function(){
		$('#modal_form_call')
			.animate({opacity: 0, top: '45%'}, 200,
				function(){ // пoсле aнимaции
					$(this).css('display', 'none');
					$('#overlay').fadeOut(400);
				}
			);
	});
});


$(document).mouseup(function (e) {
    var container = $(".close");
    if (container.has(e.target).length === 0){
        $(".alert").hide();
    }
});

</script>





<?php if($_COOKIE['pop'] == 1){ ?>
<div id="background" ></div>
<div id="sliderBox">
    <br/>
    <input type="button" id="closeWelcomeBox" value="Закрыть"/><br /><br />
<p class="p_pop"> Есть вопросы? Или нужна консультация? <span style="color: #04b850;font-size: 20px;font-weight: bolder;margin-bottom: 15px;display: block;">Закажи, это бесплатно!</span></p>

<form method="post" action="">
<p>Телефон <input style="border: 1px solid #ccc;" name="tel_p" type="text" size="30"></p>
<input  type="submit" name="ok_p" value="Отправить" class="sub"/>
<p style="font-size: 12px;margin: 10px;">Нажимая на кнопку «Отправить», я даю <a targer="_blank" href="/personal">согласие на обработку персональных данных</a></p>
</form>
</div>



<?php
setcookie("pop", '1', time()+3600);

} ?>





<script src="/templates/itb72/js/app.min.js"></script>
<div id="button-up">
	<span>&uarr;</span>
</div>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-3LWWMPJ2CT"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-3LWWMPJ2CT');

//Функция, которая добавляет поле _antispam во все формы на странице
function appendAntiSpamField(){
	var forms = document.getElementsByTagName("form");
	for(var i = 0,l = forms.length;i < l;i++){
		var inp = document.createElement('input');
		inp.setAttribute("type","hidden");
		inp.setAttribute("name","_antispam");
		var d = new Date();
		//Случайное значение
		inp.value = "antispam_"+d.getMilliseconds();
		forms[i].appendChild(inp);
	}
}

document.addEventListener("DOMContentLoaded", appendAntiSpamField);
</script>

</body>
</html>


<?php
if (isset($_POST['ok_p'])){

if(!isset($_POST['_antispam'])){exit;}

	$tel_p = strip_tags($_POST['tel_p']);
	$tel_p = htmlentities($_POST['tel_p'], ENT_QUOTES, "UTF-8");
	$tel_p = htmlspecialchars($_POST['tel_p'], ENT_QUOTES);

$massaga = "Заявка \"Получи консультацию\" <br> ТЕЛ: $tel_p";
$headers=null;
$headers.="Content-Type: text/html; charset=utf-8\r\n";
$headers.="X-Mailer: PHP/".phpversion()."\r\n";
mail('energoteplokomplekt@yandex.ru,admin@etk72.ru', 'сообщение с сайта', $massaga,$headers);
echo "<script>alert('Спасибо, в ближайшее время с Вами свяжется наш специалист!')</script>";
}?>




<?php
//Форма обратный звонок
if (isset($_POST['call_sub'])){

if(!isset($_POST['_antispam'])){exit;}

	$price_name=strip_tags($_POST['price_name']);
	$price_tel=strip_tags($_POST['price_tel']);
	$price_hid=strip_tags($_POST['price_hid']);
	if ($price_hid != ""){exit;}
  $ulink='https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$massaga = "Заявка на консультацию-обратный звонок $ulink <br> Город: $price_name<br> ТЕЛ: $price_tel<br>";
$headers=null;
$headers.="Content-Type: text/html; charset=utf-8\r\n";
$headers.="X-Mailer: PHP/".phpversion()."\r\n";
mail('energoteplokomplekt@yandex.ru,admin@etk72.ru', 'Заявка на консультацию-обратный звонок', $massaga,$headers);
echo "<script>alert('Спасибо, в ближайшее время с Вами свяжется наш специалист!')</script>";
}









// Обрабатываем форму с раздела контакты
if (isset($_POST['sub_contacts'])){

	if(!isset($_POST['_antispam'])){exit;}

	//Генерируем дату
	$date_register = date('Y-m-d');

	$fio  = trim(strip_tags($_POST['fio']));
	$tel  = trim(strip_tags($_POST['tel']));
	$mess  = trim(strip_tags($_POST['mess']));


    // Файл
    $file = $_FILES["doc"]["name"];
        if(!empty($file))
			{

    		  $blacklist = array(".php", ".phtml", ".php3", ".php4", ".html", ".htm");

			  foreach ($blacklist as $item){
				if(preg_match("/$item\$/i", $_FILES['doc']['name'])) exit;
			  }

			  $file = $_SERVER['DOCUMENT_ROOT']."/uploads/".$_FILES["doc"]["name"];
			  $file_db = "/uploads/docs/".$_FILES["doc"]["name"];

				// Сохраняем файл
				$file_name = $_FILES["doc"]["tmp_name"];
				move_uploaded_file($file_name, $file);
				$filelink = "<a href=\"https://etk72.ru/uploads/{$_FILES["doc"]["name"]}\">Скачать</a>";
			}

	$headers=null;
	$headers.="Content-Type: text/html; charset=utf-8\r\n";
	$headers.="X-Mailer: PHP/".phpversion()."\r\n";
	$massaga = "ИМЯ - $fio<br>  Телефон - $tel<br>  Сообщение - $mess<br> $filelink";

    mail('energoteplokomplekt@yandex.ru,admin@etk72.ru', 'Заявка с раздела контакты', $massaga,$headers);

	echo "<script>alert('Мы ответим Вам в ближайшее время');</script>";
}
?>

<script>
    window.onload = () => {
        setTimeout(() => {
            const metrika = document.createElement('script')
            metrika.src = '/templates/itb72/js/metrika.js'
            metrika.async = true
            document.body.append(metrika)
        }, 1500)
        setTimeout(() => {
            const jivo = document.createElement('script')
            jivo.src = '//code.jivosite.com/script/widget/Er68IaHxHt'
            jivo.async = true
            document.body.append(jivo)
        }, 1900)
    }
</script>