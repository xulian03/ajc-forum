<?php
    session_start();

    include 'db.php';

    $id = uniqid('post_', true);
    $user = $_SESSION['id'];

    function validate($str) {
        return htmlspecialchars(stripslashes(trim($str)));
    }

    
    $allowedTypes = ['image/jpeg', 'image/png'];
    
    $dir = 'images/';
    $subdir = $dir . $user . '/';
    
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    
    if (!is_dir($subdir)) {
        mkdir($subdir, 0777, true);
    }
    
    $images = [];
    
    if(isset($_FILES['files'])) {
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
            } else {
                echo "Failed to move file $tmp_name to $uploadFile.<br>";
            }
        }
    }

    $images_encoded = json_encode($images);
    
    $content = validate($_POST['content']);
    $content = str_replace(array("\r\n", "\r"), "\n", $content);
    $content = preg_replace("/(\n\s*){2,}/", "\n\n", $content);
    $content = $connection->real_escape_string($content);
    $thread = $_SESSION['reply-thread'];
    
    if(empty($content)) {
        header("Location: thread.php?id=" . $thread['id']);
        exit();
    }

    if($thread['open'] == 0) {
        header("Location: home.php");
        exit();
    }
    

    $time = time();
    $result = mysqli_query($connection, "INSERT INTO post (id, content, images, date, user, parent) 
        VALUES ('{$id}','{$content}','{$images_encoded}','{$time}','{$user}','{$thread['id']}')");

    $response = ['redirect' => 'thread.php?id=' . $thread['id']];
    header('Content-Type: application/json');
    echo json_encode($response);

    exit();
?>