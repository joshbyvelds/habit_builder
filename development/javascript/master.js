(function($){

    // DB Install functions..
    function submitInstall(){
        $.post('php/install', $("#install_form").serialize(), function(json_return){
            json_return = JSON.parse(json_return);

            if(json_return.error){
                $("#install_form .errorbox").html(json_return.error);
            }else{
                location.reload();
            }
        });
    }

    // Setup functions..
    function setupDBInstall(){
        $("#install_submit_btn").click(submitInstall);
    }

    function init(){
        setupDBInstall();
    }

    $(document).ready(init);
}(jquery));