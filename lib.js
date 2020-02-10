 require(['jquery'], function($) {
     
    $(document).ready(function(){ 
        
        function getUrlVars() {
            var vars = {};
            var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
                vars[key] = value;
            });
            return vars;
        }

     
        if(window.location.href.indexOf("/course/") > -1
                || window.location.href.indexOf("/mod/") > -1
            ) {
        
            //lesson/view.php?id=
            //lesson/view.php?id=833&pageid=649
            var id = getUrlVars()["id"];
            var viewid = getUrlVars()["view"];
            var pageid = getUrlVars()["pageid"];
            var section = getUrlVars()["section"];
            
            //this is the course listing  page
            if(window.location.href.indexOf("/course/") > -1) {
                if(typeof id === 'undefined') {
                    return;
                }
            }else if (window.location.href.indexOf("/mod/") > -1) {
                
                if(window.location.href.indexOf("/mod/lesson/") > -1) {
                    //if we are inside a lesson page
                    if( (typeof id !== 'undefined') && (typeof pageid !== 'undefined')) {
                        var urls = $( 'a[href*="id='+id+'&pageid='+pageid+'"]' );
                        $.each( urls, function() {
                            if($(this).attr('id')) {
                                var  objid = $(this).attr('id'); 
                                $('#'+objid).removeClass('custom_menu_selected_lesson_page');
                                $('#'+objid).addClass('custom_menu_selected_lesson_page_bold');
                                
                            }
                        });
                    }else if(typeof id !== 'undefined') {
                    //if we are inside just a lesson main page
                        var urls = $( 'a[href*="id='+id+'"]' );
                        $.each( urls, function() {
                            
                            if($(this).attr('id')) {
                                var  objid = $(this).attr('id'); 
                                
                                if(objid = "menu_course_section_value_"+id) {
                                    $('#'+objid).removeClass('custom_menu_selected_lesson');
                                    $('#'+objid).addClass('custom_menu_selected_lesson_bold');
                                }
                            }
                        }); 
                    }
                }else {
                    //mod/choice/view.php?id=832
                    if(typeof id !== 'undefined') {
                       var urls = $( 'a[href*="id='+id+'"]' );
                        $.each( urls, function() {
                            if($(this).attr('id')) {
                                
                                var  objid = $(this).attr('id'); 
                                $('#'+objid).removeClass('custom_menu_selected_lesson_page');
                                $('#'+objid).addClass('custom_menu_selected_lesson_page_bold');
                            }
                        });  
                    }
                }
            }
        }
        
        //main unit cookie
        if(readCookie("ccm-section")) {
            var id = readCookie("ccm-section");
            id = id.replace("ccm-section-", 'oeaw-cmc-');
            $('#'+id).show("slow");
            //the lesson content
            $('[data-target="#'+id+'"]').addClass('block-ccm-unit-header-selected');
            
            
            if(readCookie("ccm-lesson")) {
                $('#'+readCookie("ccm-lesson"));
            }
            
            var lessonContent = readCookie("ccm-lesson-content");
            $('#'+lessonContent).show("slow");
            //the selected sub lesson
            if(readCookie("ccmc-lesson-page")) {
                var lsid = readCookie("ccmc-lesson-page");
                //$('#'+lsid).addClass('bold');
            }
        }

        //main unit
        $('.ccm-section').click(function(e){
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
        $('.ccmc-section').click(function(e){
            var id = $(this).attr("id");
            var compareid = id.replace("ccmc-section-", "ccm-section-");
            id = id.replace("ccmc-section-", "oeaw-cmc-");
            
            if(readCookie("ccm-section")) {
                if(readCookie("ccm-section") != id) {
                    $('#'+readCookie("ccm-section")).removeClass('block-ccm-unit-header-selected').addClass('block-ccm-unit-header');
                    $('#'+readCookie("ccm-section")).hide();
                    $('#'+id).removeClass('block-ccm-unit-header').addClass('block-ccm-unit-header-selected');
                }
                eraseCookie("ccm-section"); 
                eraseCookie("ccm-lesson");
                eraseCookie("ccm-lesson-content");
                eraseCookie("ccmc-lesson-page");
            }
            createCookie("ccm-section", id, 10);
            //remove the older classes
             if(id != compareid) {
                $( "div" ).find( ".block-ccm-unit-header-selected").removeClass('block-ccm-unit-header-selected').addClass('block-ccm-unit-header');
            }
            $('[data-target="#'+id+'"]').addClass('block-ccm-unit-header-selected');
            $('#'+id).show("slow");
       });

        //the lesson text   
        $('.custom_menu_selected_lesson').click(function(e){
            /*var subclasses = $('.custom_menu_selected_lesson_page.bold_menu');
            subclasses.removeClass('bold_menu');
            */
            var id = $(this).attr("id");
            createCookie("ccm-lesson", id, 10);
            id = id.replace('menu_course_section_value_', 'oeaw-cml-content-');
            createCookie("ccm-lesson-content", id, 10);
            $('#'+id).show("slow");           
        });
        
        //the lesson arrow
        $('.custom_menu_selected_lesson_arrow').click(function(e) {
            
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
        $('.custom_menu_selected_lesson_page').click(function(e){
            if(readCookie("ccmc-lesson-page")) {
                $('#'+readCookie("ccmc-lesson-page")).hide();
                eraseCookie("ccmc-lesson-page");
           }
           createCookie("ccmc-lesson-page", $(this).attr("id"), 10);
           $('#'+readCookie("ccmc-lesson-page"));
        });
    });
    
    
    
    function createCookie(name, value, days) {
        var expires;

        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toGMTString();
        } else {
            expires = "";
        }
        document.cookie = encodeURIComponent(name) + "=" + encodeURIComponent(value) + expires + "; path=/";
    }

    function readCookie(name) {
        var nameEQ = encodeURIComponent(name) + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) === ' ')
                c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0)
                return decodeURIComponent(c.substring(nameEQ.length, c.length));
        }
        return null;
    }

    function eraseCookie(name) {
        createCookie(name, "", -1);
    }
    //}); 
 });