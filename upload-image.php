<?php
    session_start();

    if($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo 'error';
        exit();
    }

    if(!isset($_FILES['files']) || !is_uploaded_file($_FILES['files']['tmp_name'])) {
        echo 'error';
        exit();
    }

    $allowedTypes = ['image/jpeg', 'image/png'];
    if(!in_array($_FILES['files']['type'], $allowedTypes)) {
        echo 'error';
        exit();
    }

    $user = $_SESSION['id'];
    
    $dir = 'images/';
    $subdir = $dir . $user . '/';
    
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    
    if (!is_dir($subdir)) {
        mkdir($subdir, 0777, true);
    }
    
    $images = [];
    
    foreach ($_FILES['files']['tmp_name'] as $key => $tmp_name) {
        if (!is_uploaded_file($tmp_name)) {
            continue;
        }
    
        $fileType = $_FILES['files']['type'][$key];
        if (!in_array($fileType, $allowedTypes)) {
            continue;
        }
    
    $uuid = uniqid('', true);
        $originalName = basename($_FILES['files']['name'][$key]);
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $newFileName = $uuid . '.' . $extension;
    
        $uploadFile = $subdir . $newFileName;
    
        if (move_uploaded_file($tmp_name, $uploadFile)) {
            $images[] = $uploadFile;
        }
    }

    $images_encoded = json_encode($images);

    

?>