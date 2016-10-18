/**
 * Overlord's JS API.
 */

if (!window.jQuery) {  
     alert("jqeury is required for the Overlord JS API.")
}

const API_STATUS_VALUES = { success: "SUCCESS", error: "ERROR" };

const OE_API = {
	location: {name: "location", func: { getCityFromZip: "getCityFromZip", getCityByName: "getCityByName" }},
	group: {name: "group", func: { getCityFromZip: "getCityFromZip" }},
	photo: {name: "photo", func: { upload: "upload"}},
};

function jsonRequest(api, formid, requestVariables, onSuccess) {
	var postVariables = { oe_api: api, oe_api_type: 'json', oe_call: formid };
	$.extend(postVariables, requestVariables);
	
	var response = $.post( "/", postVariables, function( result ) {
		if (result.api_status == API_STATUS_VALUES.success) {
			console.log(result);
			onSuccess(result.response);
		}
		else if (result.api_status == API_STATUS_VALUES.error) {
			console.log("Json results returned with error!");
			console.log(result);
			//onFailure(result);
		}
		else {
			console.log("Json results returned with invalid api_status!");
			console.log(result);
			//onFailure(result);
		}
	}, "json")
		.fail(function() {
			console.log("jsonRequest failed api:" + api + ", formid:" + formid);
			console.log(requestVariables);
		});
	
	return response;
}

/*
function SetLocationFromZip(zipCode) {
	jsonRequest(OE_API.location.name, OE_API.location.func.getCityFromZip, {zip: zipCode}, SetLocation);
}

function SetLocation(response) {
	$('input#city-input').val(response.city + ', ' + response.state);
	$('input#city').val(response.city_id);
}

function AutocompleteLocationName(name) {
	var response = jsonRequest(OE_API.location.name, OE_API.location.func.getCityByName, {city: name}, DisplayAutocompleteLocationOptions);
	
	console.log(response);
}

function DisplayAutocompleteLocationOptions(response) {
	$('#city-autocomplete').empty();
	
	$.each(response.matches, function (key, value) {
		$('#city-autocomplete').append('<div id="city-data" data-cityid="' + value.id + '">' + value.city + ', ' + value.state + '</div>');
	});
	$('#city-autocomplete').show();
	
	$('#city-autocomplete div').click(function() {
		$('input#city-input').val($(this).html());
		$('input#city').val($(this).data("cityid"));
		$(this).parent().hide();
	});
}

$(document).ready(function() {
	*//*
	 * Removed per request but keeping code just in case and for eidt profile implementation 
	 *//*

	$('input#zip').change(function() {
		SetLocationFromZip($(this).val());
	});
	
	$('input#city-input').on("keyup", function() {
		if ($(this).is(":focus")) {
			AutocompleteLocationName($(this).val());
		}
	});
	
	$('input#city-input').on("focusout", function() {
		setTimeout(function() {
			$('#city-autocomplete').hide();
		}, 150);
	});
	
  	*//*
	var predDropDown = $('<div class="zip-autocomplete"></div>').insertAfter($(this));
	
	predDropDown.empty();
	$.each(predictions, function(i, prediction) {
		predDropDown.append('<div>' + prediction.city + ', ' + prediction.state + '</div>');
	});
	
	predDropDown.show();
	*//*
		
});
*/