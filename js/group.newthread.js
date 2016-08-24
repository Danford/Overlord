/**
 * 
 */


function verify(){
	
	if( newthread.subject.value.length > 100 ){
		alert( "Maximum subject length is 100 characters." );
	} else if( newthread.subject.value.length == 0 ){
		alert( "Name is not an optional field." );
	} else if( newthread.message.value.length == 0 ){
		alert( "Message can't be blank." );
	} else {
		newthread.submit() ;
	}
}