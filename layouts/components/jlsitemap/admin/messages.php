<?php
/**
 * @package    JLSitemap Component
 * @version    1.10.2
 * @author     Joomline - joomline.ru
 * @copyright  Copyright (c) 2010 - 2020 Joomline. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://joomline.ru/
 */

defined('_JEXEC') or die;

if (!$displayData) return;

$messages = $displayData;
?>
<div id="systemMessages">
	<?php foreach ($messages as $message):
		$message['type'] = ($message['type'] == 'message') ? 'success' : $message['type'];
		?>
		<div class="message <?php echo $message['type']; ?>">
			<?php echo $message['message']; ?>
		</div>
	<?php endforeach; ?>
</div>