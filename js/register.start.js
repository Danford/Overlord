
function check_age() {

	var n = new Date() ;
	var b = new Date( start.birth_year.value , ( start.birth_month.value - 1 ), start.birth_day.value,0,0,0,0 ) ;
	var d = new Date( ( n.getFullYear() - 18 ), n.getMonth() , n.getDate() + 1 , 0,0,0,0 ) ;

	return ( d > b ) ;
	
}



function validate() {

	var invalidsn = /[\/?\.\,<>\[\]\{\}!@#$%\^&\*\(\)~`+=\\\|\'\";: ]/ ;
	var invalidemail = /[\/?\,<>\[\]\{\}!#$%\^&\*\(\)~`=\\\|\'\";: ]/ ;
	var validemail = /.*@.*\..*/ ;
	
	if( start.screen_name.value == ''  ) {
		
		alert( 'Screen Name cannot be blank.' ) ;
		start.screen_name.focus() ;
	
	} else if( start.screen_name.value.search( invalidsn ) > 0  ) {
		
		alert( 'Screen Name contains invalid characters.' ) ;
		start.screen_name.focus() ;
	
	} else if( start.email == '' ) {	 
		
		alert( 'Email cannot be blank.') ;
		start.email.focus() ;
	
	} else if( start.email == '' ){
		
		alert( 'Email cannot be blank.') ;
		start.email.focus() ;
		
	} else if( start.email.value.search( invalidemail ) > 0  ) {
		
		alert( 'Email contains invalid characters.' ) ;
		start.email.focus() ;
	
	} else if( start.email.value.search( validemail ) !=  0 ) {
		
		alert( 'Email is not a valid format.' ) ;
		start.email.focus() ;
		
	} else if( start.email.value != start.confirmemail.value ){
		
		alert( 'Email confirmation does not match.' ) ;
		start.confirmemail.focus() ;
		
	} else if( start.password.value != start.confirmpassword.value ){
		
		alert( 'Password confirmation does not match.' ) ;
		start.password.focus() ;
		
	} else if ( start.birth_month.value == '__' ) {
		
		alert( 'You must supply your birth month.' )
		start.birth_month.focus() ;
		
	} else if ( start.birth_day.value == '__' ) {
		
		alert( 'You must supply your birth day.' )
		start.birth_day.focus() ;
		
	} else if ( start.birth_year.value == '__' ) {
		
		alert( 'You must supply your birth year.' )
		start.birth_year.focus() ;
		
	} else if ( check_age() == false  ){
		
		alert( 'You must be at least 18 years old to join this website.' ) ;
		
	} else if ( ! start.tos_agree.checked ) {
		
		alert( 'You must agree to the terms of service.' ) ;
	} else {
		
		start.submit() ;
	}
	
}