<?php
/**
 * @package MJ Simple News
 * @version 1.0.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2013 MicroJoom. All Rights Reserved.
 * @author MicroJoom http://www.microjoom.com
 * 
 */
defined('_JEXEC') or die;

if( !class_exists('MJ_Helper_Abstract') ){
	abstract class MJ_Helper_Abstract{
		
		/**
		 * Helper Abstract version
		 * @var string
		 */
		const VERSION = '1.0.0';
		
		/**
		 * Parse and build target attribute for links.
		 * @param string $value (_self, _blank, _windowopen, _modal)
		 * _blank 	Opens the linked document in a new window or tab
		 * _self 	Opens the linked document in the same frame as it was clicked (this is default)
		 * _parent 	Opens the linked document in the parent frame
		 * _top 	Opens the linked document in the full body of the window
		 * _windowopen  Opens the linked document in a Window
		 * _modal		Opens the linked document in a Modal Window
		 */
		
		public static function linkTarget($type='_self'){
			$target = '';
			switch($type){
				default:
				case '_self':
					break;
				case '_blank':
				case '_parent':
				case '_top':
					$target = 'target="'.$type.'"';
					break;
				case '_windowopen':
					$target = "onclick=\"window.open(this.href,'targetWindow','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,false');return false;\"";
					break;
				case '_modal':
					break;
			}
			return $target;
		}
		
		public static function createThumbs($item, $params, $prefix='imgf'){
			$images_path = array();
			$image = self::getAImage($item, $params, $prefix);
			$mode_resize = (int)$params->get($prefix.'_function');
			if(file_exists($image['src']) || @getimagesize($image['src'])){
				switch($mode_resize){
					case 0:
						$images_path = $image;
						break;
					case 1:
					case 2:
					case 3:
					case 4:
					case 5:
						if(file_exists($image['src'])){
							$jimage = new JImage($image['src']);
							$thumbSizes = array($params->get($prefix.'_width').'x'.$params->get($prefix.'_height'));
							$_image = $jimage->createThumbs($thumbSizes, $mode_resize, 'cache/resized');
							$image['src'] = $_image[0]->getPath();
							$images_path = $image;
						}else{
							$images_path = $image;
						}
						break;
					default:
						break;
				}
			}
			return $images_path;
		}
		
		
		
		/**
		 * strips all tag, except a, em, strong
		 * @param string $text
		 * @return string
		 */
		public static function _cleanText($text){
			//$text = strip_tags($text, '<a><b><blockquote><code><del><dd><dl><dt><em><h1><h2><h3><i><kbd><p><pre><s><sup><strong><strike><br><hr>');
			$text = strip_tags($text, '<a><b>');
			$text = trim($text);
			return $text;
		}
		
		/**
		 * Check string if it's exits tags but not exits text
		 * @param string $text
		 * @return string
		 */
		public static function _trimEncode($text){
			$str = strip_tags($text);
			$str = str_replace(' ', '' , $str);
			$str = trim( $str, "\xC2\xA0\n" );
			return $str;
		}
		
		/**
		 * Truncate string by $length
		 * @param string $string
		 * @param int $length
		 * @param string $etc
		 * @return string
		 */
		public static function truncate($string, $length, $etc='...'){
			return defined('MB_OVERLOAD_STRING')
			? self::_mb_truncate($string, $length, $etc)
			: self::_truncate($string, $length, $etc);
		}

		/**
		 * Truncate string if it's size over $length
		 * @param string $string
		 * @param int $length
		 * @param string $etc
		 * @return string
		 */
		private static function _truncate($string, $length, $etc='...'){
			if ($length>0 && $length<strlen($string)){
				$buffer = '';
				$buffer_length = 0;
				$parts = preg_split('/(<[^>]*>)/', $string, -1, PREG_SPLIT_DELIM_CAPTURE);
				$self_closing_tag = split(',', 'area,base,basefont,br,col,frame,hr,img,input,isindex,link,meta,param,embed');
				$open = array();

				foreach($parts as $i => $s){
					if( false===strpos($s, '<') ){
						$s_length = strlen($s);
						if ($buffer_length + $s_length < $length){
							$buffer .= $s;
							$buffer_length += $s_length;
						} else if ($buffer_length + $s_length == $length) {
							if ( !empty($etc) ){
								$buffer .= ($s[$s_length - 1]==' ') ? $etc : " $etc";
							}
							break;
						} else {
							$words = preg_split('/([^\s]*)/', $s, - 1, PREG_SPLIT_DELIM_CAPTURE);
							$space_end = false;
							foreach ($words as $w){
								if ($w_length = strlen($w)){
									if ($buffer_length + $w_length < $length){
										$buffer .= $w;
										$buffer_length += $w_length;
										$space_end = (trim($w) == '');
									} else {
										if ( !empty($etc) ){
											$more = $space_end ? $etc : " $etc";
											$buffer .= $more;
											$buffer_length += strlen($more);
										}
										break;
									}
								}
							}
							break;
						}
					} else {
						preg_match('/^<([\/]?\s?)([a-zA-Z0-9]+)\s?[^>]*>$/', $s, $m);
						//$tagclose = isset($m[1]) && trim($m[1])=='/';
						if (empty($m[1]) && isset($m[2]) && !in_array($m[2], $self_closing_tag)){
							array_push($open, $m[2]);
						} else if (trim($m[1])=='/') {
							$tag = array_pop($open);
							if ($tag != $m[2]){
								// uncomment to to check invalid html string.
								// die('invalid close tag: '. $s);
							}
						}
						$buffer .= $s;
					}
				}
				// close tag openned.
				while(count($open)>0){
					$tag = array_pop($open);
					$buffer .= "</$tag>";
				}
				return $buffer;
			}
			return $string;
		}

		/**
		 * Truncate mutibyte string if it's size over $length
		 * @param string $string
		 * @param int $length
		 * @param string $etc
		 * @return string
		 */
		private static function _mb_truncate($string, $length, $etc='...'){
			$encoding = mb_detect_encoding($string);
			if ($length>0 && $length<mb_strlen($string, $encoding)){
				$buffer = '';
				$buffer_length = 0;
				$parts = preg_split('/(<[^>]*>)/', $string, -1, PREG_SPLIT_DELIM_CAPTURE);
				$self_closing_tag = explode(',', 'area,base,basefont,br,col,frame,hr,img,input,isindex,link,meta,param,embed');
				$open = array();

				foreach($parts as $i => $s){
					if (false === mb_strpos($s, '<')){
						$s_length = mb_strlen($s, $encoding);
						if ($buffer_length + $s_length < $length){
							$buffer .= $s;
							$buffer_length += $s_length;
						} else if ($buffer_length + $s_length == $length) {
							if ( !empty($etc) ){
								$buffer .= ($s[$s_length - 1]==' ') ? $etc : " $etc";
							}
							break;
						} else {
							$words = preg_split('/([^\s]*)/', $s, -1, PREG_SPLIT_DELIM_CAPTURE);
							$space_end = false;
							foreach ($words as $w){
								if ($w_length = mb_strlen($w, $encoding)){
									if ($buffer_length + $w_length < $length){
										$buffer .= $w;
										$buffer_length += $w_length;
										$space_end = (trim($w) == '');
									} else {
										if ( !empty($etc) ){
											$more = $space_end ? $etc : " $etc";
											$buffer .= $more;
											$buffer_length += mb_strlen($more);
										}
										break;
									}
								}
							}
							break;
						}
					} else {
						preg_match('/^<([\/]?\s?)([a-zA-Z0-9]+)\s?[^>]*>$/', $s, $m);
						//$tagclose = isset($m[1]) && trim($m[1])=='/';
						if (empty($m[1]) && isset($m[2]) && !in_array($m[2], $self_closing_tag)){
							array_push($open, $m[2]);
						} else if (trim($m[1])=='/') {
							$tag = array_pop($open);
							if ($tag != $m[2]){
								// uncomment to to check invalid html string.
								// die('invalid close tag: '. $s);
							}
						}
						$buffer .= $s;
					}
				}
				// close tag openned.
				while(count($open)>0){
					$tag = array_pop($open);
					$buffer .= "</$tag>";
				}
				return $buffer;
			}
			return $string;
		}
		
		/**
		 *
		 * @param unknown_type $item
		 * @param unknown_type $params
		 */
		private static function getAImage($item, $params, $prefix='imgf'){
			$mark = array();  $images_path = array();
			$images_cfg = $params->get($prefix.'_cfg');
			if(count($images_cfg) > 0){
				foreach($images_cfg as $image){
					$mark[] = str_replace($prefix.'_','',$image);
				}
			}
			
			if ( count($mark) > 0 ){
				$images_data = null;
				if (in_array('image_intro', $mark) || in_array('image_fulltext', $mark)){
					$images_data = json_decode($item->images, true);
				}

				foreach($mark as $type){
					switch ($type){
						case 'image_intro':
						case 'image_fulltext':
							if ( isset($images_data) && isset($images_data[$type]) && !empty($images_data[$type])){
								$image = array(
										'src' => $images_data[$type]
								);
								if (array_key_exists($type.'_alt', $images_data)){
									$image['alt'] = ($images_data[$type.'_alt'] != '')?$images_data[$type.'_alt']:$item->title;
								}
								if (array_key_exists($type.'_caption', $images_data)){
									/* $image['class'] = 'caption'; */
									$image['title'] = ($images_data[$type.'_caption'] != '')?$images_data[$type.'_caption']:$item->title;
								}
								if(file_exists($image['src']) || @getimagesize($image['src'])) {
									array_push($images_path, $image);
								}
							}
							break;
						case 'inline_introtext':
							$text = $item->introtext;
						case 'inline_fulltext':
							if ($type == 'inline_fulltext'){
								$text = (isset($item->fulltext) && $item->fulltext != '')?$item->fulltext:'';
							}
							$inline_images = self::getInlineImages($text);
							for ($i=0; $i<count($inline_images); $i++){
								$inline_images[$i]['alt'] = $item->title;
								$inline_images[$i]['title'] = $item->title;
								if(file_exists($inline_images[$i]['src']) || @getimagesize($inline_images[$i]['src'])) {
									array_push($images_path, $inline_images[$i]);
								}
							}
							break;
								
						case 'external':
							$exf = $params->get($prefix.'_external_url', 'images/article/{id}/');
							preg_match_all('/{([a-zA-Z0-9_]+)}/', $exf, $m);
							if ( count($m)==2 && count($m[0])>0 ){
								$compat = 1;
								foreach ($m[1] as $property){
									!property_exists($item, $property) && ($compat=0);
								}
								if ($compat){
									$replace = array();
									foreach ($m[1] as $property){
										$replace[] = is_null($item->$property) ? '' : $item->$property;
									}
									$exf = str_replace($m[0], $replace, $exf);
								}
							}
							$files = self::getExternalImages($exf);
							for ($i=0; $i<count($files); $i++){
								if(file_exists($files[$i]) || @getimagesize($files[$i])) {
									array_push($images_path, array('src'=>$files[$i], 'title'=>$item->title, 'alt'=>$item->title));
								}
							}
							break;
						default:
							break;
					}
				}
			}
				
			if ( count($images_path) == 0 && $params->get($prefix.'_placeholder', 1)==1){
				$placeholder_path = $params->get($prefix.'_placeholder_path','modules/mod_mj_simple_news/assets/img/nophoto.png');
				if(file_exists($placeholder_path) || @getimagesize($placeholder_path)){
					$images_path[] = array('src'=> $placeholder_path,'title'=>$item->title, 'alt'=>$item->title, 'class'=>'placeholder');
				}
			}
			return is_array($images_path) && count($images_path) ? $images_path[0] : null;
		}

		
		/**
		 * Get all image url|path in $text.
		 * @param string $text
		 * @return string
		 */
		private static function getInlineImages($text){
			$images = array();
			$searchTags = array(
					'img'	=> '/<img[^>]+>/i',
					'input'	=> '/<input[^>]+type\s?=\s?"image"[^>]+>/i'
			);
			foreach ($searchTags as $tag => $regex){
				preg_match_all($regex, $text, $m);
				if ( is_array($m) && isset($m[0]) && count($m[0])){
					foreach ($m[0] as $htmltag){
						$tmp = JUtility::parseAttributes($htmltag);
						if ( isset($tmp['src']) ){
							if ($tag == 'input'){
								array_push( $images, array('src' => $tmp['src']) );
							} else {
								array_push( $images, $tmp );
							}
						}
					}
				}
			}
			return $images;
		}

		/**
		 *
		 * @param string $path
		 * @return multitype:multitype:unknown  |Ambigous <multitype:, boolean, multitype:unknown multitype:unknown  >
		 */
		private static function getExternalImages($path){
			jimport('joomla.filesystem.folder');
			$files = array();
			// check if $path is url
			$path = trim($path);
			$isHttp = stripos($path, 'http') === 0;
			if ($isHttp){
				if ( !JUri::isInternal($path) ){
					// is external, test if is valid
					if ( version_compare(JVERSION, '3.0.0', '>=') ){
						// is Joomla 3
						$http = JHttpFactory::getHttp();
						$head = $http->head($path);
						if ($head->code == 200 || $head->code == 302 || $head->code == 304){
							// is valid url
							if (preg_match('/image/', $head->headers['Content-Type'])){
								// is true image
								$files[] = $path;
							}
						}
					} else {
						// for Joomla 3 older
						$files[] = $path;
					}
					if (!count($files)){ var_dump('Url is not valid'); }
					return $files;
				} else {
					$uri = JUri::getInstance($path);
					$uri_path = (string)$uri->getPath();
					$uri_base = (string)JURI::base(true);
					if (stripos($uri_path, $uri_base)===0 && ($baselen = strlen($uri_base))){
						$uri_path = substr($uri_path, $baselen);
					}
					$path = JPATH_BASE.$uri_path;
				}
			}
			
			if ( ($realpath = realpath($path))===false ){
				return $files;
			}

			if ( is_file($realpath) ){
				$files[] = $realpath;
			} else if ( is_dir($realpath) ){
				$files = JFolder::files($path, '.jpg|.png|.gif|.JPG|.PNG|.GIF', false, true);
			}
			return $files;
		}
	}
}