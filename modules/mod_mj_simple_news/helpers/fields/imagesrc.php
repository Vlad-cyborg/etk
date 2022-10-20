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

if (!class_exists('JFormFieldImageSrc')){
	class JFormFieldImageSrc extends JFormField{
		
		protected $type = 'ImageSrc';
		
		protected $forceMultiple = true;
		
		public function getInput(){
			$html = array();
			$class = $this->element['class'] ? ' class="image-src ' . (string) $this->element['class'] . '"' : ' class="image-src"';
			$html[] = '<div id="' . $this->id . '"' . $class . '>';
			$options = $this->getOptions();
			$arr_value = (is_string($this->value) == true && strpos($this->value,',') !== false )?explode(',',$this->value):$this->value;
			$html[] = '<ul id="image_src">';
			$_html = array();

			foreach ($options as $i => $option){
			
				$checked = (in_array((string) $option->value, (array) $arr_value) ? ' checked="checked"' : '');
				$class = !empty($option->class) ? ' class="' . $option->class . '"' : '';
				$disabled = !empty($option->disable) ? ' disabled="disabled"' : '';
				$onclick = !empty($option->onclick) ? ' onclick="' . $option->onclick . '"' : '';
				$temp = '<li><span class="image-move"><span>&middot;</span><span>&middot;</span><span>&middot;</span></span>';
				$temp .= '<input type="checkbox" id="' . $this->id . $i . '" name="' . $this->name . '"' . ' value="'
					. htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8') . '"' .$checked . $class . $onclick . $disabled . '/>';

				$temp .= '<label for="' . $this->id . $i . '"' . $class . '>' . JText::_($option->text) . '</label>';
				$temp .= '</li>';
				$_html[$option->value] = $temp;
			}
			
			$_tmp = array();
			if(!empty($arr_value) && !empty($_html)){
			
				$_arr_key = array_keys($_html);
				$flag = true;	
				for($k = 0; $k < count($arr_value); $k++){
					if(array_search($arr_value[$k], $_arr_key) === false){
						$flag = false;
						break;
					}
				}
				if($flag){
					$_not_exit = array_diff($_arr_key,$arr_value);
					if(!empty($_not_exit)){
						for($i =  0; $i< count($_arr_key); $i++){
							if(isset($_not_exit[$i])){
								array_push($arr_value, $_not_exit[$i]);
							}
						}
					}
					for($j = 0; $j < count($arr_value) ;$j++){
						if(isset($arr_value[$j])){
							$_tmp[] = $_html[$arr_value[$j]];
						}
					}
				}else{
					$_tmp = $_html;
				}
			}else{
				$_tmp = $_html;
			}
		
			$html[] = implode('',$_tmp);
			$html[] = '</ul>';
			$html[] = '</div>';
			$this->addStylesheet();	
			$this->addJavaScript();	
			return implode("\n", $html);
		}

		protected function addJavaScript(){
			$document = JFactory::getDocument();
			$document->addScriptDeclaration("
					window.addEvent('domready', function(){
						try{
							var image_src = $(document.body).getElement('#image_src');
							new Sortables(image_src);
						} catch(e){
							console.log(e);
						}
					});
			");
			return true;
		}
		
		protected function addStyleSheet(){
			$document = JFactory::getDocument();
			$document->addStyleDeclaration("
				@-webkit-keyframes fadeInRight{
					0%{
						opacity:0;
						-webkit-transform:translateX(20px);
					}100%{
						opacity:1;
						-webkit-transform:translateX(0);
					}
				}
				
				@-moz-keyframes fadeInRight{
					0%{
						opacity:0;
						-moz-transform:translateX(20px);
					}100%{
						opacity:1;
						-moz-transform:translateX(0);
					}
				}
				
				@-o-keyframes fadeInRight{
					0%{
						opacity:0;
						-o-transform:translateX(20px);
					}100%{
						opacity:1;
						-o-transform:translateX(0);
					}
				}
				
				@keyframes fadeInRight{
					0%{
						opacity:0;
						transform:translateX(20px);
					}100%{
						opacity:1;
						transform:translateX(0);
					}
				}
				
				.animated.fadeInRight{
					-webkit-animation-name:fadeInRight;
					-moz-animation-name:fadeInRight;
					-o-animation-name:fadeInRight;
					animation-name:fadeInRight;
				}
				
				.image-src{
					clear:both;
					overflow:hidden;
					display:inline-block;
				}
				
				.image-src ul{
					list-style:none;
					margin:0;
					padding:0;
					width:250px;
				}
				
				.image-src ul li{
					list-style: none;
					cursor:move;
					margin:2px;
					overflow:hidden;
					border:1px solid #999;
					background:none repeat scroll 0 0 #EDEDED;
					color:#000;
					border-radius:3px;
					background: -moz-linear-gradient(center top , #FFFFFF 0%, #EDEDED 100%) repeat scroll 0 0 rgba(0, 0, 0, 0);
					-webkit-animation-duration:0.4s;
					-moz-animation-duration:0.4s;
					-o-animation-duration:0.4s;
					animation-duration:0.4s;
					-webkit-animation-fill-mode:both;
					-moz-animation-fill-mode:both;
					-o-animation-fill-mode:both;
					animation-fill-mode:both;	
					-webkit-animation-name:fadeInRight;
					-moz-animation-name:fadeInRight;
					-o-animation-name:fadeInRight;
					animation-name:fadeInRight;
				}
				
				.image-src ul li:hover,
				.image-src ul li:focus{
					background:none repeat scroll 0 0 #EDEDED;
					outline:none;
				}
				
				.image-src ul li span.image-move{
					float: left;
					height: 20px;
					line-height: 100%;
					padding: 7px 0 0 5px;
					width: 20px;
					cursor:move;
				}
				
				.image-src ul li span.image-move span{
					float:left;
					font-size: 43px;
					line-height: 5px;
				}
				
				.image-src ul li input{
					margin: 0 10px 0 0;
					height:30px;
					outline:none;
					float:left;
				}
				
				.image-src ul li input:focus{
					outline:none;
				}
				
				.image-src ul li label{
					clear:none;
					line-height:28px;
					font-weight:bold;
					display:block;
					margin:0;
				}
			");
			return true;
		}	
		
		protected function getOptions(){
			$options = array();
			foreach ($this->element->children() as $option){
				if ($option->getName() != 'option'){
					continue;
				}
				$tmp = JHtml::_(
					'select.option', (string) $option['value'], trim((string) $option), 'value', 'text',
					((string) $option['disabled'] == 'true')
				);
				$tmp->class = (string) $option['class'];
				$tmp->onclick = (string) $option['onclick'];
				$options[] = $tmp;
			}
			reset($options);
			return $options;
		}
		
	};
}