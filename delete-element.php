<?php
    session_start();

    include 'db.php';

    if($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: home.php");
        exit();
    }

    if(!isset($_POST['table']) || !isset($_POST['element'])) {
        header("Location: home.php");
        exit();
    }

    function validate($str) {
        return htmlspecialchars(stripslashes(trim($str)));
    }

    if(!isset($_SESSION['id'])) {
        echo 0;
        exit();
    }

    

    $table = $connection -> real_escape_string(validate($_POST['table']));
    $element = $connection -> real_escape_string(validate($_POST['element']));
    $user = $_SESSION['id'];

    $allowedTables = ['thread', 'post', 'new'];

    if (!in_array($table, $allowedTables)) {
        echo json_encode(['error' => 'Invalid table']);
        exit();
    }
    $parent = null;
    if($table == "post") {
        $result = mysqli_query($connection, "SELECT 
            p.parent AS post_parent
        FROM 
            post p
        WHERE
            p.id = '$element'");
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $parent = $row['post_parent'];
        }
    }
        

    $delete = mysqli_query($connection, "
    DELETE t 
    FROM {$table} t
    JOIN user u ON u.id = '$user'
    WHERE t.id = '$element' AND (u.verified = 1 OR t.user = '$user')
");

    

    if($parent == null) { 
        if($table == "new") {
            $response = ['success' => true, 'redirect' => 'news.php'];
        } else {
            $response = ['success' => true, 'redirect' => 'home.php'];
        }
    } else {
        if(strpos($parent, "thread") !== false) {
            $response = ['success' => true, 'redirect' => 'thread.php?id=' . $parent];
        } else {
            $response = ['success' => true, 'redirect' => 'post.php?id=' . $parent];
        }
        
    }

    header('Content-Type: application/json');
    echo json_encode($response);

    exit();


?>