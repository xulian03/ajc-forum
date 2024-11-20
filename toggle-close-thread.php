<?php
    session_start();

    include 'db.php';

    if($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: home.php");
        exit();
    }

    if(!isset($_POST['thread'])) {
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

    

    $thread = $connection -> real_escape_string(validate($_POST['thread']));
    $user = $_SESSION['id'];
        

    $result = mysqli_query($connection, "SELECT 
      t.open AS thread_open
  FROM 
      thread t
  JOIN user u ON u.id = '$user'
    WHERE t.id = '$thread' AND (u.verified = 1 OR t.user = '$user')");

      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $open = $row["thread_open"] == 1 ? 0 : 1;
        $thread_query = mysqli_query($connection, "UPDATE thread SET open='{$open}' WHERE id='{$thread}'");
        
      }


    $response = ['success' => true, 'redirect' => 'thread.php?id=' . $thread];

    header('Content-Type: application/json');
    echo json_encode($response);

    exit();


?>