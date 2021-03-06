(function($){
    var currentHabitLevel = 1;
    // Habit Functions..

    function addHabitLevel(){
        currentHabitLevel += 1;
        $("#levels_amount").val(currentHabitLevel);
        $("#new_habit_form .levels button").before('<div class="level"><h3>Level '+ currentHabitLevel +'</h3><label for="level_'+ currentHabitLevel +'_amount">Amount:</label><input type="number" name="level_'+ currentHabitLevel +'_amount" min="1"><div class="errorbox" id="level_'+ currentHabitLevel +'_amount_error">This is a error</div><br /><label for="level_'+ currentHabitLevel +'_points">Points per day:</label><input type="number" name="level_'+ currentHabitLevel +'_points" min="1"><div class="errorbox" id="level_'+ currentHabitLevel +'_points_error">This is a error</div><br /><label for="level_'+ currentHabitLevel +'_unlocks">Unlocks at # points:</label><input type="number" name="level_'+ currentHabitLevel +'_unlocks" min="1"><div class="errorbox" id="level_'+ currentHabitLevel +'_unlocks_error">This is a error</div><br /></div>');
    }

    function passHabit($btn){
        // Update DB
        var habit_id = $btn.data('habit-id');
        $.post('php/habits', {'form_type':'pass', 'id':habit_id}, function(json_return){
            json_return = JSON.parse(json_return);

            if(json_return.error){
                $("#habit_error").hide().html(json_return.habit_error).slideDown();
            }

            $("#habit_" + habit_id + " .points").html(json_return.points);
            $("#habit_" + habit_id + " .streak").html(json_return.streak);
            $("#habit_" + habit_id + " .next").html(json_return.next);
            $("#habit_" + habit_id + " .last").html(json_return.last);

            if(json_return.percent){
                $("#habit_" + habit_id + " .percent").show().html(json_return.percent + "%");
            }else{
                $("#habit_" + habit_id + " .percent").hide();
            }

            if(json_return.level_update){
                alert("Yay, Habit Level Updated.");
            }
        });
    }

    function failHabit($btn){
        // Update DB
        var habit_id = $btn.data('habit-id');
        $.post('php/habits', {'form_type':'fail', 'id':habit_id}, function(json_return){
            json_return = JSON.parse(json_return);

            if(json_return.error){
                $("#habit_error").hide().html(json_return.habit_error).slideDown();
            }

            $("#habit_" + habit_id + " .streak").html(0);
            $("#habit_" + habit_id + " .next").html(json_return.next);
            $("#habit_" + habit_id + " .fails").html(parseInt($("#habit_" + habit_id + " .fails").html()) + 1);
        });
    }

    function setupHabits(){
        $("#add_new_habit_level").on('click', addHabitLevel);
        $("#current_habits .pass_btn").on('click', function(){passHabit($(this));});
        $("#current_habits .fail_btn").on('click', function(){failHabit($(this));});
        $( "#new_habit_form" ).submit(function( event ) {
            event.preventDefault();
            $('.errorbox').hide();
            $.post("php/habits.php", $( this ).serialize(), function(json_return){
                json_return = JSON.parse(json_return);

                if(json_return.error){
                    if(json_return.title_error){$("#title_error").html(json_return.title_error).slideDown();}
                    if(json_return.description_error){$("#description_error").html(json_return.description_error).slideDown();}
                    if(json_return.db_error){$("#db_error").html(json_return.db_error).slideDown();}

                    for(var i = 1; i <= currentHabitLevel; i++){
                        if(json_return['level_' + i + '_amount_error']){$("#level_"+ i + "_amount_error").html(json_return['level_' + i + '_amount_error']).slideDown();}
                        if(json_return['level_' + i + '_points_error']){$("#level_"+ i + "_points_error").html(json_return['level_' + i + '_points_error']).slideDown();}
                        if(json_return['level_' + i + '_unlocks_error']){$("#level_"+ i + "_unlocks_error").html(json_return['level_' + i + '_unlocks_error']).slideDown();}
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

