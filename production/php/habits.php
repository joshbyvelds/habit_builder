<?php
/**
 * Created by PhpStorm.
 * User: JoshB
 * Date: 12/28/2018
 * Time: 3:59 PM
 */

$json = [];
$json['error'] = false;

if(isset($_POST['form_type'])){
    $type = $_POST['form_type'];
}

if(empty($type)){
    $json['error'] = true;
    $json['db_error'] = "Form Type Missing...";
}

if($json['error']){
    echo json_encode($json);
    exit();
}

if($type === 'add'){
    if(isset($_POST['title'])){
        $title = $_POST['title'];
    }

    if(empty($title)){
        $json['error'] = true;
        $json['title_error'] = "Please add a title for your new habit.";
    }

    if(isset($_POST['description'])){
        $title = $_POST['description'];
    }

    if(empty($title)){
        $json['error'] = true;
        $json['description_error'] = "Please add a description for your new habit.";
    }

    if($json['error']){
        echo json_encode($json);
        exit();
    }
}

