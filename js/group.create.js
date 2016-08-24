/**
 * 
 */


function verify(){
	
	if( create.name.value.length > 75 ){
		alert( "Maximum title length is 75 characters." );
	} else if( create.name.value.length == 0 ){
		alert( "name is not an optional field." );
	} else if( create.short_desc.value.length == 0 ){
		alert( "You must supply a short description." );
	} else if( create.short_desc.value.length > 255 ) {
		alert( "Maximum short description length is 255 characters." ) ;
	} else {
		create.submit() ;
	}
	
	
}