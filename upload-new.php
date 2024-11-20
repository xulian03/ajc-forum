<?php
    session_start();

    include 'db.php';

    unset($_SESSION['title_new']);
    unset($_SESSION['content_new']);
    
    
    if(!isset($_POST['title'])) {
        $_SESSION['title_new'] = 1;
        header("Location: create-new.php");
	    exit();
    }
    if(!isset($_POST['content'])) {
        $_SESSION['content_new'] = 1;
        header("Location: create-new.php");
	    exit();
    }

    $id = uniqid('new_', true);
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

    $options = array_filter($_POST, function($option) {
        return strpos($option, 'option-') === 0 && !empty($_POST[$option]);
    }, ARRAY_FILTER_USE_KEY);
    
    $optionsMap = [];

    foreach ($options as $key => $value) {
        $optionsMap[$connection -> real_escape_string(validate($value))] = 0;
    }

    $title = $connection -> real_escape_string(validate($_POST['title']));
    $content = validate($_POST['content']);
    $content = str_replace(array("\r\n", "\r"), "\n", $content);
    $content = preg_replace("/(\n\s*){2,}/", "\n\n", $content);
    $content = $connection->real_escape_string($content);

    if(empty($title) || empty($content)) {
        header("Location: create-new.php");
        exit();
    }

    $consult = mysqli_query($connection, "
        SELECT 1 
        FROM user 
        WHERE id = '$user' AND verified = 1
    ");

    if(mysqli_num_rows($consult) == 0) {
        header("Location: create-new.php");
        exit();
    }

    $time = time();
    $result = mysqli_query($connection, "INSERT INTO new (id, user, title, content, images, date) 
        VALUES ('{$id}','{$user}','{$title}','{$content}','{$images_encoded}','{$time}')");
    $select_user = mysqli_query($connection, "SELECT threads FROM user WHERE (id='$user')");
    while($row = mysqli_fetch_array($select_user, MYSQLI_ASSOC)) {
        $threads = json_decode($row["threads"], true) != null ? json_decode($row["threads"], true) : array();
        $threads[] = $id;
        $threads_encoded = json_encode($threads);
    }

    $response = ['redirect' => 'news.php'];
    header('Content-Type: application/json');
    echo json_encode($response);

    exit();
?>