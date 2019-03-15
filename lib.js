 require(['jquery'], function($) {
    $(document).ready(function(){ 

        //main unit cookie
        if(readCookie("ccm-section")) {
            var id = readCookie("ccm-section");
            id = id.replace("ccm-section-", 'oeaw-cmc-');
            $('#'+id).show("slow");
            //the lesson content
            
            if(readCookie("ccm-lesson")) {
                $('#'+readCookie("ccm-lesson")).addClass('bold');
            }
            
            var lessonContent = readCookie("ccm-lesson-content");
            $('#'+lessonContent).show("slow");
            //the selected sub lesson
            if(readCookie("ccmc-lesson-page")) {
                var lsid = readCookie("ccmc-lesson-page");
                $('#'+lsid).addClass('bold');
            }
        }

        //main unit
        $('.ccm-section').click(function(){
            var id = $(this).attr("id");
            if(readCookie("ccm-section")) {               
                eraseCookie("ccm-section"); 
                eraseCookie("ccm-lesson");
                eraseCookie("ccm-lesson-content");
                eraseCookie("ccmc-lesson-page");
            }                      
            createCookie("ccm-section", id, 10);
        });
       
        //main unit arrow
        $('.ccmc-section').click(function(){
            
            if(readCookie("ccm-section")) {
                $('#'+readCookie("ccm-section")).hide();               
                eraseCookie("ccm-section"); 
                eraseCookie("ccm-lesson");
                eraseCookie("ccm-lesson-content");
                eraseCookie("ccmc-lesson-page");
            }
            var id = $(this).attr("id");
            id = id.replace("ccmc-section-", "oeaw-cmc-");
            createCookie("ccm-section", id, 10);
            $('#'+id).show("slow");
       });

        //the lesson text   
        $('.custom_menu_selected_lesson').click(function(){
            var id = $(this).attr("id");
            createCookie("ccm-lesson", id, 10);
            id = id.replace('menu_course_section_value_', 'oeaw-cml-content-');
            createCookie("ccm-lesson-content", id, 10);
            $('#'+id).show("slow");           
        });
        
        //the lesson arrow
        $('.custom_menu_selected_lesson_arrow').click(function() {
            var id = $(this).attr("id");
            if(readCookie("ccm-lesson") == id) {
                var closeContent = id.replace('oeaw-cmlc-', 'oeaw-cml-content-');               
                $('#'+closeContent).hide("slow");   
                eraseCookie("ccm-lesson");
                eraseCookie("ccm-lesson-content");
                eraseCookie("ccmc-lesson-page");
            }else {
                createCookie("ccm-lesson", id, 10);
                var lessonContent = id.replace('oeaw-cmlc-', 'oeaw-cml-content-');
                createCookie("ccm-lesson-content", lessonContent, 10);
                $('#'+lessonContent).show("slow");
            }
        });
       
        //the selected sub lesson page
        $('.custom_menu_selected_lesson_page').click(function(){
            if(readCookie("ccmc-lesson-page")) {
                $('#'+readCookie("ccmc-lesson-page")).hide();
                eraseCookie("ccmc-lesson-page");
           }
           createCookie("ccmc-lesson-page", $(this).attr("id"), 10);
           $('#'+readCookie("ccmc-lesson-page")).addClass('bold');
        });
    });
});   