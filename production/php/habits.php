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

    if(isset($_POST['description'])){
        $description = $_POST['description'];
    }

    if(isset($_POST['levels'])){
        $levels = $_POST['levels'];
    }

    if(empty($title)){
        $json['error'] = true;
        $json['title_error'] = "Please add a title for your new habit.";
    }

    if(empty($description)){
        $json['error'] = true;
        $json['description_error'] = "Please add a description for your new habit.";
    }

    if(empty($levels)){
        $json['error'] = true;
        $json['db_error'] = "Missing the amount of levels this habit has.";
    }

    for($i=1; $i <= (int)$levels; $i++){
        if(isset($_POST['level_' . $i . '_amount'])){
            ${'level_' . $i . 'amount'} = $_POST['level_' . $i . '_amount'];
        }

        if(isset($_POST['level_' . $i . '_points'])){
            ${'level_' . $i . 'points'} = $_POST['level_' . $i . '_points'];
        }

        if(isset($_POST['level_' . $i . '_unlocks'])){
            ${'level_' . $i . 'unlocks'} = $_POST['level_' . $i . '_unlocks'];
        }

        if(empty(${'level_' . $i . 'amount'})){
            $json['error'] = true;
            $json['level_' . $i . '_amount_error'] = "Please enter the amount of times you have to complete this habit at this level to pass.";
        }

        if(empty(${'level_' . $i . 'points'})){
            $json['error'] = true;
            $json['level_' . $i . '_points_error'] = "Please enter the amount of points you earn when you pass this habit at this level.";
        }

        if(empty(${'level_' . $i . 'unlocks'})){
            $json['error'] = true;
            $json['level_' . $i . '_unlocks_error'] = "Please enter the amount points needed to unlock this level.";
        }
    }




    if($json['error']){
        echo json_encode($json);
        exit();
    }
}

