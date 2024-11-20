<?php
    session_start();

    include 'db.php';
    
    if(!isset($_POST['id']) || !isset($_POST['name']) || !isset($_POST['doc']) || !isset($_POST['password']) || !isset($_POST['password-confirm'])) {
        $_SESSION['active'] = 1;
        header("Location: index.php");
	    exit();
    }

    function validate($str) {
        return htmlspecialchars(stripslashes(trim($str)));
    }

    $id = $connection -> real_escape_string(validate($_POST['id']));
	$name = $connection ->  real_escape_string(validate($_POST['name']));
    $doc = $connection ->  real_escape_string(validate($_POST['doc']));
	$password = $connection -> real_escape_string(validate($_POST['password']));
    $password_confirm = $connection -> real_escape_string(validate($_POST['password-confirm']));

    $query = mysqli_query($connection, "SELECT * FROM user WHERE (id='$id') OR (document='$doc')");

    if(mysqli_num_rows($query) > 0) {
        $_SESSION['active'] = 1;
        $complete_name = !empty($name) ? '&name=' . $name : '';
        header('Location: index.php?registererror=El usuario o documento ya existe.' . $complete_name);
        exit();
    }

    if(empty($id) || empty($name) || empty($doc) || empty($password) || empty($password_confirm)) {
        $_SESSION['active'] = 1;
        $complete_id = !empty($id) ? "&id=$id" : "";
        $complete_doc = !empty($doc) ? "&doc=$doc" : "";
        $complete_name = !empty($name) ? "&name=$name" : "";
        header("Location: index.php?registererror=Debes llenar todos los campos." .  $complete_id . $complete_doc . $complete_name);
        exit();
    }

    if(!is_numeric($doc)) {
        $_SESSION['active'] = 1;
        $complete_id = !empty($id) ? "&id=$id" : "";
        $complete_name = !empty($name) ? "&name=$name" : "";
        header("Location: index.php?registererror=El documento no es válido." . $complete_id . $complete_name);
        exit();
    }

    if($password !== $password_confirm) {
        $_SESSION['active'] = 1;
        $complete_id = !empty($id) ? "&id=$id" : "";
        $complete_doc = !empty($doc) ? "&doc=$doc" : "";
        $complete_name = !empty($name) ? "&name=$name" : "";
        header("Location: index.php?registererror=Las contraseñas no coinciden." . $complete_id . $complete_doc . $complete_name);
        exit();
    }

    $result = mysqli_query($connection, "INSERT INTO user (id, document, name, password) VALUES ('{$id}','{$doc}','{$name}','{$password}')");

    $_SESSION['id'] = $id;
    $_SESSION['logged'] = time();
    
    if (isset($_SESSION['redirect'])) {
        $redirect = $_SESSION['redirect'];
        unset($_SESSION['redirect']);
        header("Location: $redirect");
    } else {
        header('Location: home.php');
    }
    exit();
?>