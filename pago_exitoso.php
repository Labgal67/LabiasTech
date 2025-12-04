<?php
session_start();
include 'incluir/conexion.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Obtener información del pedido si existe
$id_pedido = isset($_GET['id_pedido']) ? (int)$_GET['id_pedido'] : 0;
$info_pedido = null;

if ($id_pedido > 0) {
    $consulta = "SELECT p.*, u.usuario FROM pedidos p 
                 JOIN usuarios u ON p.id_usuario = u.id_usuario 
                 WHERE p.id_pedido = $id_pedido AND p.id_usuario = " . $_SESSION['id_usuario'];
    $resultado = $conexion->query($consulta);
    
    if ($resultado->num_rows > 0) {
        $info_pedido = $resultado->fetch_assoc();
    }
}
?>

<?php include 'incluir/encabezado.php'; ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-success shadow-lg">
                <div class="card-header bg-success text-white text-center">
                    <h3 class="mb-0"><i class="fas fa-check-circle fa-2x mr-2"></i> ¡Compra Exitosa!</h3>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-trophy text-success fa-5x mb-3"></i>
                        <h2 class="text-success">Gracias por tu compra, <?php echo $_SESSION['usuario']; ?>!</h2>
                    </div>
                    
                    <?php if ($info_pedido): ?>
                        <div class="alert alert-info text-left">
                            <h5><i class="fas fa-receipt mr-2"></i> Detalles de tu pedido:</h5>
                            <hr>
                            <p><strong>Número de pedido:</strong> #<?php echo str_pad($info_pedido['id_pedido'], 6, '0', STR_PAD_LEFT); ?></p>
                            <p><strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($info_pedido['fecha'])); ?></p>
                            <p><strong>Total:</strong> $<?php echo number_format($info_pedido['total'], 2); ?></p>
                            <p><strong>Estado:</strong> <span class="badge badge-success"><?php echo ucfirst($info_pedido['estado']); ?></span></p>
                        </div>
                    <?php endif; ?>
                    
                    <div class="alert alert-light">
                        <h5><i class="fas fa-shipping-fast mr-2"></i> Información de envío</h5>
                        <p>Tu pedido ha sido procesado exitosamente. Recibirás un correo electrónico con los detalles de seguimiento en las próximas 24 horas.</p>
                        <p class="mb-0"><strong>Tiempo estimado de entrega:</strong> 3-5 días hábiles</p>
                    </div>
                    
                    <div class="mt-4">
                        <a href="index.php" class="btn btn-primary btn-lg mr-3">
                            <i class="fas fa-home mr-2"></i> Volver al Inicio
                        </a>
                        <a href="index.php" class="btn btn-outline-success btn-lg">
                            <i class="fas fa-shopping-bag mr-2"></i> Seguir Comprando
                        </a>
                    </div>
                </div>
                <div class="card-footer text-center text-muted">
                    <small>¿Tienes preguntas sobre tu pedido? Contáctanos en info@labiastech.com</small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'incluir/pie.php'; ?>