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
    session_start();

    if(isset($_SESSION['user_id'])){
        $user_id = $_SESSION['user_id'];
    }

    if(isset($_POST['title'])){
        $title = $_POST['title'];
    }

    if(isset($_POST['description'])){
        $description = $_POST['description'];
    }

    if(isset($_POST['levels'])){
        $levels = $_POST['levels'];
    }

    if(empty($user_id)){
        $json['error'] = true;
        $json['db_error'] = "Missing user id..";
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
            ${'level_' . $i . '_amount'} = $_POST['level_' . $i . '_amount'];
        }

        if(isset($_POST['level_' . $i . '_points'])){
            ${'level_' . $i . '_points'} = $_POST['level_' . $i . '_points'];
        }

        if(isset($_POST['level_' . $i . '_unlocks'])){
            ${'level_' . $i . '_unlocks'} = $_POST['level_' . $i . '_unlocks'];
        }

        if(empty(${'level_' . $i . '_amount'})){
            $json['error'] = true;
            $json['level_' . $i . '_amount_error'] = "Please enter the amount of times you have to complete this habit at this level to pass.";
        }

        if(empty(${'level_' . $i . '_points'})){
            $json['error'] = true;
            $json['level_' . $i . '_points_error'] = "Please enter the amount of points you earn when you pass this habit at this level.";
        }

        if(empty(${'level_' . $i . '_unlocks'}) && ${'level_' . $i . '_unlocks'} !== '0'){
            $json['error'] = true;
            $json['level_' . $i . '_unlocks_error'] = "Please enter the amount points needed to unlock this level.";
        }
    }

    if($json['error']){
        echo json_encode($json);
        exit();
    }

    // Add to Database
    include 'db.inc.php';

    //default values..
    $verified_by = 1;
    $type = 1;
    $streak = 0;
    $level = 1;
    $time = date('Y-m-d G:i:s');




    // Get level amount values and concat them into string..
    $level_amounts = '';
    for($i=1; $i <= (int)$levels; $i++){
        $level_amounts .= '|' . ${'level_' . $i . '_amount'} . '-' . ${'level_' . $i . '_points'} . '-' . ${'level_' . $i . '_unlocks'};
    }
    $level_amounts = substr($level_amounts, 1);

    $stmt = $db->prepare("INSERT INTO habits (user, verify, title, description, type, level, level_amounts, streak, lastsuccess) VALUES (?, ?, ?, ?, ?, ?, ? ,?, ?)");
    $stmt->bindParam(1, $user_id);
    $stmt->bindParam(2, $verified_by);
    $stmt->bindParam(3, $title);
    $stmt->bindParam(4, $description);
    $stmt->bindParam(5, $type);
    $stmt->bindParam(6, $level);
    $stmt->bindParam(7, $level_amounts);
    $stmt->bindParam(8, $streak);
    $stmt->bindParam(9, $time);
    $stmt->execute();

    echo json_encode($json);
    exit();
}

