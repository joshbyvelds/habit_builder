(function($){

    // DB Install functions..
    function setupDBInstall(){
        $( "#install_form" ).submit(function( event ) {
            event.preventDefault();
            $('.errorbox').hide();
            $.post("php/install.php", $( this ).serialize(), function(json_return){
                json_return = JSON.parse(json_return);

                if(json_return.error){
                    if(json_return.sql_user_error){$("#sql_user_error").html(json_return.sql_user_error).slideDown();}
                    if(json_return.sql_password_error){$("#sql_password_error").html(json_return.sql_password_error).slideDown();}
                    if(json_return.sql_database_error){$("#sql_database_error").html(json_return.sql_database_error).slideDown();}
                    if(json_return.admin_username_error){$("#admin_username_error").html(json_return.admin_username_error).slideDown();}
                    if(json_return.admin_password_error){$("#admin_password_error").html(json_return.admin_password_error).slideDown();}
                    if(json_return.db_error){$("#db_error").html(json_return.db_error).slideDown();}
                }else{
                    //window.location.replace('/');
                }
            });
        });
    }

    function init(){
        setupDBInstall();
    }

    $(document).ready(init);
}(jQuery));

