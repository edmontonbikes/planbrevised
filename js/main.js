// This code defines what jQuery will do once the
// full page is loaded. "onLoad()" event
$(function () {

  // This is the stuff that jQuery will do
  
  
  $('#contact-info-submit').click( // when some clicks this guy
  
    function(e) { // run this function
    
        e.preventDefault(); // Don't submit the form

        // Check to see if the waiver is agreed to
        if ( $('#waiver-input').is(':checked') ) {
            
            // Tell the form with id "contact-info-form" to go ahead and submit
            $('#contact-info-form').submit();
        
        } else { // if not
            
            // Tell the user to do something (click the waiver agreement)
            alert('You must agree to the waiver before continuing.');
            
        } 
      
    }
  
  );
  
    
  
});

