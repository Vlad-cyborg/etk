// jQuery('#sidebar-wrapper #menu-collapse').trigger('click');
jQuery(document).ready(function($) {
	$('body.admin>#wrapper').removeClass('open');
	$('body.admin>#wrapper').addClass('closed');
});

function switchParam() {
	var trig = jQuery("#params_param").prop("checked");
	var mystatus = !trig;
	jQuery("#param_xfields").prop( "disabled", mystatus);
}

function switchListCats() {
	var trig = jQuery("#param_trigcat").val();
	if(trig==0) {
		mystatus = true;
		size = 1;
	} else {
		mystatus = false;
		size = 7;
	}
	jQuery("#paramslist_cat").prop( "disabled", mystatus);
	jQuery("#paramslist_cat").attr('size', size);
}

function switchListManfs() {
	var trig = jQuery("#param_trigmanf").val();
	if(trig==0) {
		mystatus = true;
		size = 1;
	} else {
		mystatus = false;
		size = 7;
	}
	jQuery("#paramslist_manufacturers").prop( "disabled", mystatus);
	jQuery("#paramslist_manufacturers").attr('size', size);
}

function switchListAttrs() {
	var trig = jQuery("#param_attr").prop("checked");
	if(trig==0) {
		mystatus = true;
		size = 1;
	} else {
		mystatus = false;
		size = 7;
	}
	jQuery("#paramslist_attr").prop( "disabled", mystatus);
	jQuery("#paramslist_attr").attr('size', size);
	jQuery("li.attributes > input").prop( "disabled", mystatus);
}
	
function saveSelectedOptions(ele, htmlInputId) {
	htmlSelectId = '#'+jQuery(ele).attr('id');
	var list = new Array();
	var i = 0;
	jQuery(htmlSelectId + " :selected").each(function(j, selected){ 
		value = jQuery(selected).val(); 
		text = jQuery(selected).text();
		if (value!=0){
			i++;
			list[i] = value;
		}
	});
	jQuery(htmlInputId).val(list.join());
}
	
jQuery(document).ready(function() {
	switchListCats();
	switchListManfs();
	switchListAttrs();
	switchParam();
});
	
function imdExportYMlAddRow() {
	var newRow = jQuery('#custom_xml tbody tr:last-child').clone();
	parts = jQuery(newRow).attr('id').match(/(.+)-(.+)/);
	var id = parts[2];
	id = parseInt(id)+1;
	jQuery(newRow).attr('id', "row-" + id);
	
	var inputList = jQuery(newRow).find('td input');
	for (let i = 0; i < inputList.length; i++) {
		parts = jQuery(inputList[i]).attr('name').match(/(.+)-(.+)/);
		jQuery(inputList[i]).attr('name', parts[1] + '-' + id + ']');
		jQuery(inputList[i]).val('');
	};
	jQuery(newRow).appendTo( "#custom_xml tbody" );
}

function imdExportYMlRemoveRow(ele) {
	var rows = jQuery('#custom_xml tbody tr');
	if(rows.length>1) jQuery(ele).closest('tr').remove();
}
