    
 $(document).ready(function(){   
    
    $('.oeaw_custom_menu_content').hide();
    
    function showonlyone(thechosenone) {
         $('.oeaw_custom_menu_content').each(function(index) {
              if ($(this).attr("id") == thechosenone) {
                   $(this).show(200);
              }
              else {
                   $(this).hide(600);
              }
         });

    }
});