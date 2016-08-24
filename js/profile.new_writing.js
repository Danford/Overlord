/**
 * 
 */

function albumselect() {
	
	if( new_writing.album.value != 'New' ) {
		
		newalbum.style.display = 'none' ;
		
	} else {
		
		newalbum.style.display = 'block' ;
	}
	
}


function verify(){
	
	if( new_writing.album.value == 'New' && new_writing.new_album_title == '' ){
		alert( "You must supply an album title." ) ;
	} else if( new_writing.title.value.length > 75 ){
		alert( "Maximum title length is 75 characters." );
	} else if( new_writing.description.value.length > 255 ) {
		alert( "Maximum description length is 255 characters." ) ;
	} else {
		new_writing.submit() ;
	}
	
	
}