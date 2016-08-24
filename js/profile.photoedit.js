/**
 * 
 */

function albumselect() {
	
	if( editphoto.album.value != 'New' ) {
		
		newalbum.style.display = 'none' ;
		
	} else {
		
		newalbum.style.display = 'block' ;
	}
	
}

function avatarChecked() {
	
	if( editphoto.setavatar.checked && editphoto.private.value == "1" ){
		
		alert( "An image viewable by friends only cannot be set to your profile photo." ) ;
		
		editphoto.setavatar.checked = false ;
	}
	
}

function verify(){
	
	if( editphoto.title.value.length > 75 ){
		alert( "Maximum title length is 75 characters." );
	} else if( editphoto.description.value.length > 255 ) {
		alert( "Maximum description length is 255 characters." ) ;
	} else {
		editphoto.submit() ;
	}
	
	
}