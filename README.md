CUSTOM Course MENU block for moodle 3.1 Created By Norbert Czirjak

This menu is created for the OEAW Theme based on Klass theme and it is using the jquery cookie library for the actual menupoint css settings.


Install steps:

1.You need to copy the course_custom_menu directory to the blocks directory
2.you need to add the jquery cookie js file to your theme/header.php
<script type="text/javascript" src="YOUR URL/jquery.cookie.js"></script>

and for the menu please add the following 

<script type="text/javascript">
                
        $(document).ready(function(){
               
            /* 
             * The block menu JQuery commands
             * hide all menupoint in the first load */
            
            $(".oeaw_custom_menu_content").hide();
            $(".oeaw_custom_menu_root").css('background-color', '#b9b9b9');
            
            $(".oeaw_custom_menu_content_row a").click(function(){        
                var id = $(this).attr('id');
                
                //removie cookie
                $.removeCookie("clickedName");     
                // create a new cookie, 7 days lifetime and it will available
                // in the whole site -> /
                $.cookie("clickedName",id , {expires: 7, path:'/'});
                
                var oldClass = $("#"+id).attr('class');                
                var newClass = oldClass+' active';     
                $("#"+id).removeClass( oldClass ).addClass( newClass);                  
                $("#"+id).css('color', 'black');
                
            }); 
            
            if($.cookie("clickedName") != null){
                            
                var cookieName = $.cookie("clickedName");                
                var oldClass = $("#"+cookieName).attr('class');
                var newClass = oldClass+' active';
                $("#"+cookieName).removeClass( oldClass ).addClass( newClass);                        
                $("#"+cookieName).css('color', 'black');                
            }
            
            
            // if the user clicked to the root text
            $(".oeaw_custom_menu_root_header a").click(function(){  
                $(".oeaw_custom_menu_root").css('background-color', '#b9b9b9');
                //hide all menupoint
                $(".oeaw_custom_menu_content").hide();
                // get the clicked id
                var id = $(this).attr('id');
                var newId = id;
                //removie cookie
                $.removeCookie("menuName");     
                // create a new cookie, 7 days lifetime and it will available
                // in the whole site -> /
                $.cookie("menuName", newId, {expires: 7, path:'/'});
                //we change the root to the content in the class name
                newId = newId.replace("cmr", "cmc");        
                //and we add the active class to it
                var oldClass = $("#"+newId).attr('class');
                var newClass = oldClass+' active';                
                
                $("#"+newId).removeClass( oldClass ).addClass( newClass);        
                //show the new class in the selected menu content
                $("#"+newId).show();                
                
                var arr = newId.split("_");                
                var headerClass = "oeaw_custom_menu_root_"+arr[2];                
                $("#"+headerClass).css('background-color', '#016771');
                
            });   
            
            //if we already have a cookie then change the classes
            if($.cookie("menuName") != null){
                            
                var cookieName = $.cookie("menuName");
                var openedMenu = cookieName.replace("cmr", "cmc"); 
                var oldClass = $("#"+openedMenu).attr('class');
                var newClass = oldClass+' active';

                var arr = cookieName.split("_");
                var headerClass = "oeaw_custom_menu_root_"+arr[2];                
                $("#"+headerClass).css('background-color', '#016771');
                
                $("#"+openedMenu).removeClass( oldClass ).addClass( newClass);        
                $("#"+openedMenu).show();
                
                
                
            }
            
        });
    </script>
	
3.Log on to your site admin and go to the Notifications menupoint. Here the new block will be available, so please click to the "Upgrade Moodle database now" button.

The Frontend Part:

Go to the courses and  turn editing on. Here you can add this new block to the site.