/*
 * @package    System - JLSitemap Cron Plugin
 * @version    1.10.2
 * @author     Joomline - joomline.ru
 * @copyright  Copyright (c) 2010 - 2020 Joomline. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link       https://joomline.ru/
 */

try {
	document.addEventListener('DOMContentLoaded', function () {
		let xhr = new XMLHttpRequest(),
			params = Joomla.getOptions('jlsitemap_cron', '');
		xhr.open('GET', params.ajax_url, false);
		xhr.send();
		if (xhr.status !== 200) {
			console.error('Sitemap cron error:' + xhr.status + ': ' + xhr.statusText);
		} else {
			console.debug('Sitemap cron success');
		}
	});
} catch (e) {
	console.error('Sitemap cron error: ', e);
}