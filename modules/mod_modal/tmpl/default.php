<?php
defined('_JEXEC') or die('Restricted access');
// Include  Css
JHtml::stylesheet('modules/'.$module->module.'/css/style.css');
//Получаем параметры модуля
$dateh = $params->get('dateh', '');
$name = $params->get('name', '');
$area = $params->get('area', '');
$link = $params->get('link', '');
$img ="<img src='/{$params->get('img', '')}'>";
?>

<?php if(!isset($_COOKIE['modal'])) {?>

<!-- Модальное Окно  -->
<div id="overlay">
    <div class="popup">
		<p class="top_text_modal">Внимание АКЦИЯ! Успей до <?php echo "$dateh";?></p>
        <p class="hmodal"><?php echo "$name";?></p>
         <div class="pl-left">
			<?php echo "$img";?>
         </div>
         <div class="pl-right">
			<?php echo "$area";?><br><a href="<?php echo "$link";?>">Подробнее...</a>
         </div>              
        <button class="close" title="Закрыть" onclick="document.getElementById('overlay').style.display='none';"></button>
    </div>
</div>


<script type="text/javascript">
	var delay_popup = 5000;
	setTimeout("document.getElementById('overlay').style.display='block'", delay_popup);
</script>

<?php }?>