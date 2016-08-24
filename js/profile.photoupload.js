/**
 * 
 */

function albumselect() {
	
	if( imageupload.album.value != 'New' ) {
		
		newalbum.style.display = 'none' ;
		
	} else {
		
		newalbum.style.display = 'block' ;
	}
	
}

function avatarChecked() {
	
	if( imageupload.setavatar.checked && imageupload.private.value == "1" ){
		
		alert( "An image viewable by friends only cannot be set to your profile photo." ) ;
		
		imageupload.setavatar.checked = false ;
	}
	
}

function verify(){
	
	var u = imageupload.photo.value ;
	
	var o = [ '.png', '.PNG', '.jpg', '.JPG' ] ;
	
	if( 
			( u.substr( u.length - 5 ) != '.jpeg') && 
			( u.substr( u.length - 5 ) != '.JPEG' ) && 
			( o.indexOf( u.substr( u.length - 4 )) == -1 ) ) {
		
		alert( "File must be a .jpg, .jpeg, or .png file." );		
				
	} else if( imageupload.album.value == 'New' && imageupload.new_album_title == '' ){
		alert( "You must supply an album title." ) ;
	} else if( imageupload.title.value.length > 75 ){
		alert( "Maximum title length is 75 characters." );
	} else if( imageupload.description.value.length > 255 ) {
		alert( "Maximum description length is 255 characters." ) ;
	} else {
		imageupload.submit() ;
	}
	
	
}