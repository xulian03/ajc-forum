<?php
    session_start();

    include 'db.php';

    unset($_SESSION['title_thread']);
    unset($_SESSION['content_thread']);
    
    
    if(!isset($_POST['title'])) {
        $_SESSION['title_thread'] = 1;
        header("Location: create-thread.php");
	    exit();
    }
    if(!isset($_POST['content'])) {
        $_SESSION['content_thread'] = 1;
        header("Location: create-thread.php");
	    exit();
    }

    $id = uniqid('thread_', true);
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
    
    $survey = 0;
    $title_survey = $_POST['title-survey'];

    $options = array_filter($_POST, function($option) {
        return strpos($option, 'option-') === 0 && !empty($_POST[$option]);
    }, ARRAY_FILTER_USE_KEY);
    
    $optionsMap = [];

    foreach ($options as $key => $value) {
        $optionsMap[$connection -> real_escape_string(validate($value))] = 0;
    }

    if(isset($title_survey) && !empty($options)) {
        $survey = 1;
    }

    $title = $connection -> real_escape_string(validate($_POST['title']));
    $content = validate($_POST['content']);
    $content = str_replace(array("\r\n", "\r"), "\n", $content);
    $content = preg_replace("/(\n\s*){2,}/", "\n\n", $content);
    $content = $connection->real_escape_string($content);
    $title_survey = $connection -> real_escape_string(validate(isset($title_survey) ? $title_survey : ""));
    $multi_select = isset($_POST['multi-select']) ? 1 : 0;

    if(empty($title) || empty($content)) {
        header("Location: create-thread.php");
        exit();
    }
    $time = time();
    $result = mysqli_query($connection, "INSERT INTO thread (id, user, title, content, survey, images, date) 
        VALUES ('{$id}','{$user}','{$title}','{$content}','{$survey}','{$images_encoded}','{$time}')");
    $select_user = mysqli_query($connection, "SELECT threads FROM user WHERE (id='$user')");
    while($row = mysqli_fetch_array($select_user, MYSQLI_ASSOC)) {
        $threads = json_decode($row["threads"], true) != null ? json_decode($row["threads"], true) : array();
        $threads[] = $id;
        $threads_encoded = json_encode($threads);
        $update_user = mysqli_query($connection, "UPDATE user SET threads='{$threads_encoded}' WHERE id='{$user}'");
    }

    if($result && $survey == 1) {
        $options_encoded = json_encode($optionsMap);
        $survey_result = mysqli_query($connection, "INSERT INTO survey (id, title, votes, multi_select) VALUES ('{$id}','{$title_survey}','{$options_encoded}','{$multi_select}')");
    }

    $response = ['redirect' => 'thread.php?id=' . $id];
    header('Content-Type: application/json');
    echo json_encode($response);

    exit();
?>