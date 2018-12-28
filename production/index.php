<?php

// Get composer stuff.
require_once '../vendor/autoload.php';

// Setup twig template engine..
$loader = new Twig_Loader_Filesystem('view/');
$twig = new Twig_Environment($loader);
//$twig = new Twig_Environment($loader, array('cache' => 'cache/'));

// Page Variables
$site_name = "Habit Builder";

// Check if Database is installed..
if(!file_exists ( 'php/db.inc.php' )) {
    // If not, load install page..
    $page_name = 'Install';
    echo $twig->render('install.twig', ['title' =>  $site_name . ' - ' . $page_name ]);
    exit();
}

// If Logout page..
session_start();
if(isset($_GET["logout"]) && $_GET["logout"] === "1"){
    session_unset();
    session_destroy();
    session_write_close();
    setcookie(session_name(),'',0,'/');
}

// Check if user is logged in..

if(isset($_SESSION['username'])){

    // check which internal page to load..
    if(isset($_GET["page"]) && $_GET["page"] === "habits"){
        $page_name = 'Habits';
        echo $twig->render('habits.twig', ['title' =>  $site_name  . ' - ' . $page_name]);

    }else if(isset($_GET["page"]) && $_GET["page"] === "settings"){
        $page_name = 'Settings';
        echo $twig->render('settings.twig', ['title' =>  $site_name  . ' - ' . $page_name]);

    }else{
        echo $twig->render('game.twig', ['title' =>  $site_name]);
    }
}else{
    $page_name = 'Login';
    echo $twig->render('login.twig', ['title' =>  $site_name . ' - ' . $page_name ]);
}






