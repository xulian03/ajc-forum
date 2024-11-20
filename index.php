<?php
  $cookie_lifetime = 30 * 24 * 60 * 60;
  session_set_cookie_params($cookie_lifetime);

  session_start();

  if (isset($_SESSION['logged'])) {
    $time_elap = time() - $_SESSION['logged'];
    $timeout = 24 * 3600;
    $_SESSION['logged'] = time(); 

    if($time_elap > $timeout) {
      session_unset();
      session_destroy();
      header("Location: index.php?error=La sesion ha expirado.");
      exit;
    }

    header("Location: home.php"); 
    exit;
  }

  $active = isset($_SESSION['active']) ? $_SESSION['active'] : 0;
?>

<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
    />
    <link rel="stylesheet" href="css\styles-login.css" />
    <link
      href="https://ieti-camacho.edu.co/images/iconos/icono.png"
      rel="shortcut icon"
    />
    <title>Foro Camacho</title>
  </head>

  <body>
    <div class="camacho-icon">
      <img src="ieti-logo.png" alt="ieti-camacho" />
    </div>
    
    <div class="<?php echo $active == 1 ? 'container active' : 'container'; ?>" id="container">
      <div class="camacho-icon">
        <img src="ieti-logo.png" alt="ieti-camacho">
      </div>
      <div class="form-container sign-up">
        <form action="register.php" method="post">
          <h1>Crear cuenta</h1>
          <input type="text" placeholder="Usuario" name="id" minlength="3" maxlength="15" value="<?php echo isset($_GET['id']) && $active == 1 ? $_GET['id'] : "";?>"/>
          <input type="text" placeholder="Nombre" name="name" minlength="3" maxlength="50" value="<?php echo isset($_GET['name']) && $active == 1 ? $_GET['name'] : "";?>"/>
          <input type="text" placeholder="Documento" name="doc" minlength="6" maxlength="25" value="<?php echo isset($_GET['doc']) && $active == 1 ? $_GET['doc'] : "";?>"/>
          <div class="pass-container">
            <input type="password" placeholder="Contraseña" name="password" minlength="8" maxlength="25"/>
            <button type="button" class="toggle-password">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                  <circle cx="12" cy="12" r="3"></circle>
                </svg>
            </button>
        </div>
        <div class="pass-container">
          <input type="password" placeholder="Confirma tu contraseña" name="password-confirm" minlength="8" maxlength="25"/>
          <button type="button" class="toggle-password">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                  <circle cx="12" cy="12" r="3"></circle>
                </svg>
          </button>
      </div>
      <?php if (isset($_GET['registererror'])) { ?>
     		<p class="error"><?php echo $_GET['registererror']; ?></p>
     	  <?php } ?>
          <button type="submit" class="button">Registrarse</button>
        </form>
      </div>
      <div class="form-container sign-in">
        <form action="login.php" method="post">
          <h1>Iniciar sesion</h1>
          <input type="text" name="id" placeholder="Usuario o Documento" minlength="6" maxlength="25" value="<?php echo isset($_GET['id']) && $active == 0 ? $_GET['id'] : "";?>" />
          <div class="pass-container">
            <input type="password" name="password" placeholder="Contraseña" minlength="8" maxlength="25" />
            <button type="button" class="toggle-password">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                  <circle cx="12" cy="12" r="3"></circle>
                </svg>
            </button>
        </div>
        <?php if (isset($_GET['error'])) { ?>
     		<p class="error"><?php echo $_GET['error']; ?></p>
     	  <?php } ?>
          <a href="#">¿Olvidaste tu contraseña?</a>
          <button type="submit" class="button">Iniciar sesión</button>
        </form>
      </div>
      <div class="toggle-container">
        <div class="toggle">  
          <div class="toggle-panel toggle-left">
            <h1>¿Nuevo? ¡Mucho gusto!</h1>
            <p>Registrate para ingresar</p>
            <span>o</span>
            <button class="hidden" id="login">Inicia sesion</button>
          </div>
          <div class="toggle-panel toggle-right">
            <h1>¡Qué gusto verte de nuevo!</h1>
            <p>Inicia sesión para ingresar</p>
            <span>o</span>
            <button class="hidden" id="register">Registrate</button>
          </div>
        </div>
        
      </div>

    
    <script src="js/script.js"></script>
  </body>
</html>
