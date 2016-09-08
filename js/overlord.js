/**
 * Overlord's JS API.
 */

if (!window.jQuery) {  
     alert("jqeury is required for the Overlord JS API.")
}

const API_STATUS_VALUES = { success: "SUCCESS", error: "ERROR" };

const OE_API = {
	location: {name: "location", func: { getCityFromZip: "getCityFromZip" }},
	group: {name: "group", func: { getCityFromZip: "getCityFromZip" }}
};

function jsonRequest(api, formid, requestVariables) {
	var postVariables = { oe_api: api, oe_formid: formid };
	$.extend(postVariables, requestVariables);
	
	console.log(postVariables);
	$.post( "/", postVariables, function( result ) {
		if (result.api_status == API_STATUS_VALUES.success) {
			//
		}
		else if (result.api_status == API_STATUS_VALUES.error) {
			console.log("Json results returned with error!");
			console.log(result);
		}
		else {
			console.log("Json results returned with invalid api_status!");
			console.log(result);
		}
		return result.response;
	}, "json");
}

$(document).ready(function() {
	$('input#zip').change(function() {
		var predictions = jsonRequest(OE_API.location.name, OE_API.location.func.getCityFromZip, {zip: $(this).val()});
		
		var predDropDown = $('<div class="zip-autocomplete"></div>').insertAfter($(this));
		
		predDropDown.empty();
		$.each(predictions, function(i, prediction) {
			predDropDown.append('<div>' + prediction.city + ', ' + prediction.state + '</div>');
		});
		
		predDropDown.show();
		
	});
});