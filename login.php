<?php
    session_start();

    include 'db.php';

    unset($_SESSION['active']);

    
    if(!isset($_POST['id']) || !isset($_POST['password'])) {
        header("Location: index.php");
	    exit();
    }

    function validate($str) {
        return htmlspecialchars(stripslashes(trim($str)));
    }

    $id = $connection -> real_escape_string(validate($_POST['id']));
	$pass = $connection -> real_escape_string(validate($_POST['password']));

    if(empty($id)) {
        header('Location: index.php?error=Usuario o documento invalido.');
        exit();
    }
    if(empty($pass)) {
        header('Location: index.php?error=Contraseña invalida.&id=' . $id);
        exit();
    }

    
    $result = mysqli_query($connection, "SELECT * FROM user WHERE (id='$id' AND password='$pass') OR (document='$id' AND password='$pass')");
    if(mysqli_num_rows($result) === 0) {
        header("Location: index.php?error=Usuario o contraseña incorrecto.&id=" . $id);
        exit();
    }
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