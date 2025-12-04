<?php
session_start();
include 'incluir/conexion.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['password']);
    
    if (empty($usuario) || empty($password)) {
        $error = "Por favor ingresa usuario y contraseña.";
    } else {
        // Usar md5 para compatibilidad con PHP 5.2
        $password_hash = md5($password);
        
        // Buscar usuario en la base de datos
        $consulta = "SELECT id_usuario, usuario, password FROM usuarios WHERE usuario = '$usuario' AND password = '$password_hash'";
        $resultado = $conexion->query($consulta);
        
        if ($resultado->num_rows == 1) {
            $usuario_data = $resultado->fetch_assoc();
            
            // Iniciar sesión
            $_SESSION['usuario'] = $usuario_data['usuario'];
            $_SESSION['id_usuario'] = $usuario_data['id_usuario'];
            
            // Redirigir a la página principal
            header("Location: index.php");
            exit();
        } else {
            $error = "Usuario o contraseña incorrectos.";
        }
    }
}
?>

<?php include 'incluir/encabezado.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow">
            <div class="card-header bg-success text-white text-center">
                <h4><i class="fas fa-sign-in-alt mr-2"></i> Iniciar Sesión</h4>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error; ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="usuario"><i class="fas fa-user mr-2"></i> Usuario</label>
                        <input type="text" class="form-control" id="usuario" name="usuario" required 
                               placeholder="Ingresa tu usuario" value="<?php echo isset($_POST['usuario']) ? $_POST['usuario'] : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock mr-2"></i> Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required 
                               placeholder="Ingresa tu contraseña">
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fas fa-sign-in-alt mr-2"></i> Iniciar Sesión
                        </button>
                    </div>
                </form>
                
                <div class="text-center mt-3">
                    <p>¿No tienes cuenta? <a href="registro.php" class="text-success">Regístrate aquí</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'incluir/pie.php'; ?>