jQuery(document).ready(function() {
"use strict";
 jQuery(".jshop_prod_attributes_compare input").remove();
	jQuery(".sel_compare select :contains('Выберите')").remove();
	jQuery(".sel_compare select").attr({multiple:"multiple"}).removeAttr("size").removeAttr("onchange");
});