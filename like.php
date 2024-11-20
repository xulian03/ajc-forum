<?php
    session_start();

    include 'db.php';

    if($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: home.php");
        exit();
    }

    if(!isset($_POST['table']) || !isset($_POST['id'])) {
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
    $id = $connection -> real_escape_string(validate($_POST['id']));
    $user = $_SESSION['id'];

    

    $result = mysqli_query($connection, "SELECT 
      t.likes AS table_likes, 
      u.likes AS user_likes
  FROM 
      {$table} t
  JOIN 
      user u ON u.id = '$user'
  WHERE
      t.id = '$id'");

      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $likes = json_decode($row["user_likes"], true) != null ? json_decode($row["user_likes"], true) : array();
        $index = array_search($id, $likes);
        if (in_array($id, $likes)) {
            unset($likes[$index]);
            $likes = array_values($likes);
        } else {
            $likes[] = $id;
            $likes = array_values($likes);
        }
        $likes_encoded = json_encode($likes);
        $user_query = mysqli_query($connection, "UPDATE user SET likes='{$likes_encoded}' WHERE id='{$user}'");
        
        $value = in_array($id, $likes) ? $row["table_likes"] + 1 : $row["table_likes"] - 1;
        $table_query = mysqli_query($connection, "UPDATE {$table} SET likes='{$value}' WHERE id='{$id}'");
        
        echo $value;
      }


?>