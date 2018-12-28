(function($){
    var currentHabitLevel = 1;
    // Habit Functions..

    function addHabitLevel(){
        currentHabitLevel += 1;
        $("#new_habit_form .levels button").before('<div class="level"><h3>Level '+ currentHabitLevel +'</h3><label for="level_'+ currentHabitLevel +'_amount">Amount:</label><input type="number" name="level_'+ currentHabitLevel +'_amount"><br /><label for="level_'+ currentHabitLevel +'_points">Points:</label><input type="number" name="level_'+ currentHabitLevel +'_points"></div>');
    }

    function setupHabits(){
        $("#add_new_habit_level").on('click', addHabitLevel);
        $( "#new_habit_form" ).submit(function( event ) {
            event.preventDefault();
            $('.errorbox').hide();
            $.post("php/habits.php", $( this ).serialize(), function(json_return){
                json_return = JSON.parse(json_return);

                if(json_return.error){
                    if(json_return.title_error){$("#title_error").html(json_return.title_error).slideDown();}
                    if(json_return.description_error){$("#description_error").html(json_return.description_error).slideDown();}
                    if(json_return.db_error){$("#db_error").html(json_return.db_error).slideDown();}

                    if(json_return.level_errors) {
                        json_return.level_errors.forEach(function (element) {
                            console.log(element);
                        });
                    }

                }else{
                    window.location.reload();
                }
            });
        });
    }

    // Login functions
    function setupLogin(){
        $( "#login_form" ).submit(function( event ) {
            event.preventDefault();
            $('.errorbox').hide();
            $.post("php/login.php", $( this ).serialize(), function(json_return){
                json_return = JSON.parse(json_return);

                if(json_return.error){
                    if(json_return.username_error){$("#username_error").html(json_return.username_error).slideDown();}
                    if(json_return.password_error){$("#password_error").html(json_return.password_error).slideDown();}
                    if(json_return.db_error){$("#db_error").html(json_return.db_error).slideDown();}
                }else{
                    window.location.reload();
                }
            });
        });
    }

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
        setupHabits();
        setupDBInstall();
        setupLogin();
    }

    $(document).ready(init);
}(jQuery));

