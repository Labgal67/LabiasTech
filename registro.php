<?php
session_start();
include 'incluir/conexion.php';

$error = '';
$exito = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['password']);
    $confirmar_password = trim($_POST['confirmar_password']);
    
    // Validaciones
    if (empty($usuario) || empty($password)) {
        $error = "Todos los campos son obligatorios.";
    } elseif ($password !== $confirmar_password) {
        $error = "Las contraseñas no coinciden.";
    } elseif (strlen($password) < 4) {
        $error = "La contraseña debe tener al menos 4 caracteres.";
    } else {
        // Verificar si el usuario ya existe
        $consulta = "SELECT id_usuario FROM usuarios WHERE usuario = '$usuario'";
        $resultado = $conexion->query($consulta);
        
        if ($resultado->num_rows > 0) {
            $error = "El nombre de usuario ya está registrado.";
        } else {
            // Insertar nuevo usuario con md5 (PHP 5.2 compatible)
            $password_hash = md5($password); // Cambiado de password_hash() a md5()
            $insertar = "INSERT INTO usuarios (usuario, password) VALUES ('$usuario', '$password_hash')";
            
            if ($conexion->query($insertar)) {
                $exito = "¡Registro exitoso! Ahora puedes iniciar sesión.";
            } else {
                $error = "Error al registrar el usuario: " . $conexion->error;
            }
        }
    }
}
?>

<?php include 'incluir/encabezado.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h4><i class="fas fa-user-plus mr-2"></i> Crear Cuenta</h4>
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
                
                <?php if ($exito): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $exito; ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="usuario"><i class="fas fa-user mr-2"></i> Nombre de Usuario</label>
                        <input type="text" class="form-control" id="usuario" name="usuario" required 
                               placeholder="Ingresa tu usuario" value="<?php echo isset($_POST['usuario']) ? $_POST['usuario'] : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock mr-2"></i> Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required 
                               placeholder="Mínimo 4 caracteres">
                    </div>
                    
                    <div class="form-group">
                        <label for="confirmar_password"><i class="fas fa-lock mr-2"></i> Confirmar Contraseña</label>
                        <input type="password" class="form-control" id="confirmar_password" name="confirmar_password" required 
                               placeholder="Repite tu contraseña">
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-user-plus mr-2"></i> Registrarse
                        </button>
                    </div>
                </form>
                
                <div class="text-center mt-3">
                    <p>¿Ya tienes una cuenta? <a href="login.php" class="text-primary">Inicia sesión aquí</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'incluir/pie.php'; ?>