<?php
/**
 * Created by PhpStorm.
 * User: JoshB
 * Date: 12/28/2018
 * Time: 3:59 PM
 */

$json = [];
$json['error'] = false;

date_default_timezone_set('America/Toronto');

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

    /*
    if(isset($_POST['type'])){
        $habit_type = $_POST['type'];
    }
    */

    if(isset($_POST['time'])){
        $habit_time = $_POST['time'];
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

    /*
    if(empty($habit_type)){
        $json['error'] = true;
        $json['type_error'] = "Please select a type for your new habit.";
    }
    */

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
            if($habit_type === 2) {
                $json['level_' . $i . '_amount_error'] = "Please enter the amount of time in minutes you have to do this habit at this level to pass.";
            }else{
                $json['level_' . $i . '_amount_error'] = "Please enter the amount of times you have to complete this habit at this level to pass.";
            }
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
    $streak = 0;
    $habit_type = 1;
    $fail = 0;
    $level = 1;
    $points = 0;
    $time = date('Y-m-d G:i:s');




    // Get level amount values and concat them into string..
    $level_amounts = '';
    for($i=1; $i <= (int)$levels; $i++){
        $level_amounts .= '|' . ${'level_' . $i . '_amount'} . '-' . ${'level_' . $i . '_points'} . '-' . ${'level_' . $i . '_unlocks'};
    }
    $level_amounts = substr($level_amounts, 1);

    $stmt = $db->prepare("INSERT INTO habits (enabled, user, verify, title, description, type, level, level_amounts, points, streak, fails, lastsuccess) VALUES (1, ?, ?, ?, ?, ?, ?, ? ,?, ?, ?, ?)");
    $stmt->bindParam(1, $user_id);
    $stmt->bindParam(2, $verified_by);
    $stmt->bindParam(3, $title);
    $stmt->bindParam(4, $description);
    $stmt->bindParam(5, $habit_type);
    $stmt->bindParam(6, $level);
    $stmt->bindParam(7, $level_amounts);
    $stmt->BindParam(8, $points);
    $stmt->bindParam(9, $streak);
    $stmt->bindParam(10, $fail);
    $stmt->bindParam(11, $time);
    $stmt->execute();

    echo json_encode($json);
    exit();
}

if($type === 'pass'){

    session_start();
    if(isset($_SESSION['user_id'])){
        $user_id = $_SESSION['user_id'];
    }

    if(isset($_POST['id'])){
        $habit_id = $_POST['id'];
    }

    if(empty($user_id)){
        $json['error'] = true;
        $json['habit_error'] = "Missing user id..";
    }

    if(empty($habit_id)){
        $json['error'] = true;
        $json['habit_error'] = "Missing habit ID..";
    }

    if($json['error']){
        echo json_encode($json);
        exit();
    }

    include 'db.inc.php';

    $time = date('Y-m-d G:i:s');

    $result = $db->prepare("SELECT level_amounts, level, streak, points FROM habits WHERE id = ?");
    $result->bindParam(1, $habit_id);
    $result->execute();
    $habit = $result->fetchAll(PDO::FETCH_ASSOC)[0];

    $mode = PHP_ROUND_HALF_EVEN;
    $precision = 4;

    $points_base = explode("-", explode("|", $habit['level_amounts'])[(int)$habit['level'] - 1])[1];
    $points_earned = $points_base + $habit['streak'];
    $points_next = $points_base + $habit['streak'] + 1;

    $level = (int)$habit['level'];
    $points = $habit['points'] + $points_earned;

    $percent = false;

    // Check if next level has been reached..
    if(count(explode("|", $habit['level_amounts'])) > $level){
        $percent = round(($points / (int)explode("-", explode("|", $habit['level_amounts'])[$level])[2]) * 100, 0, PHP_ROUND_HALF_DOWN);

        if($points >= (int)explode("-", explode("|", $habit['level_amounts'])[$level])[2]){
            $json['level_update'] = true;
            $level += 1;
            $points_base = explode("-", explode("|", $habit['level_amounts'])[(int)$habit['level']])[1];
            $points_next = $points_base + $habit['streak'] + 1;
            if(count(explode("|", $habit['level_amounts'])) > $level) {
                $percent = round(($points / (int)explode("-", explode("|", $habit['level_amounts'])[$level - 1])[2]) * 100, 0, PHP_ROUND_HALF_DOWN);
            }else{
                $percent = false;
            }
        }
    }

    // Update Habits Table..
    $stmt = $db->prepare("UPDATE habits SET level = ?, streak = streak + 1, points = points + ?, lastsuccess = ? WHERE id = ?");
    $stmt->bindParam(1, $level);
    $stmt->bindParam(2, $points_earned);
    $stmt->bindParam(3, $time);
    $stmt->bindParam(4, $habit_id);
    $stmt->execute();

    // Update Users Table
    $stmt2 = $db->prepare("UPDATE users SET points = points + ? WHERE id = ?");
    $stmt2->bindParam(1, $points_earned);
    $stmt2->bindParam(2, $user_id);
    $stmt2->execute();

    $json['points'] = round($points, 2, PHP_ROUND_HALF_DOWN);
    $json['streak'] = $habit['streak'] + 1;
    $json['next'] = $points_next;
    $json['last'] = $time;
    $json['percent'] = $percent;

    echo json_encode($json);
    exit();
}

if($type === 'fail'){
    if(isset($_POST['id'])){
        $habit_id = $_POST['id'];
    }

    if(empty($habit_id)){
        $json['error'] = true;
        $json['habit_error'] = "Missing habit ID..";
    }

    if($json['error']){
        echo json_encode($json);
        exit();
    }

    include 'db.inc.php';

    $result = $db->prepare("SELECT level_amounts, level, streak, points FROM habits WHERE id = ?");
    $result->bindParam(1, $habit_id);
    $result->execute();
    $habit = $result->fetchAll(PDO::FETCH_ASSOC)[0];

    $mode = PHP_ROUND_HALF_EVEN;
    $precision = 4;

    $points_base = explode("-", explode("|", $habit['level_amounts'])[(int)$habit['level'] - 1])[1];
    $points_earned = round(pow($points_base + 0.01, $habit['streak'] / 10), $precision, $mode);
    $points_next = round(pow($points_base + 0.01, (0 + 1) / 10), $precision, $mode);

    $stmt = $db->prepare("UPDATE habits SET streak = 0, fails = fails + 1 WHERE id = ?");
    $stmt->bindParam(1, $habit_id);
    $stmt->execute();

    $json['next'] = $points_next;

    echo json_encode($json);
    exit();
}

