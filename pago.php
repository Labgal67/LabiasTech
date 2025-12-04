<?php
session_start();
include 'incluir/conexion.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Verificar si el carrito tiene productos
if (empty($_SESSION['carrito'])) {
    header("Location: carrito.php");
    exit();
}

// Calcular total del carrito
$total = 0;
foreach ($_SESSION['carrito'] as $item) {
    $consulta = "SELECT precio FROM productos WHERE id_producto = " . $item['id_producto'];
    $resultado = $conexion->query($consulta);
    if ($resultado->num_rows > 0) {
        $producto = $resultado->fetch_assoc();
        $total += $producto['precio'] * $item['cantidad'];
    }
}

// Procesar el pago
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Crear el pedido
    $id_usuario = $_SESSION['id_usuario'];
    $insertar_pedido = "INSERT INTO pedidos (id_usuario, total) VALUES ('$id_usuario', '$total')";
    
    if ($conexion->query($insertar_pedido)) {
        $id_pedido = $conexion->insert_id;
        
        // 2. Agregar detalles del pedido
        foreach ($_SESSION['carrito'] as $item) {
            $consulta = "SELECT precio FROM productos WHERE id_producto = " . $item['id_producto'];
            $resultado = $conexion->query($consulta);
            
            if ($resultado->num_rows > 0) {
                $producto = $resultado->fetch_assoc();
                
                // Insertar detalle
                $insertar_detalle = "INSERT INTO detalles_pedido (id_pedido, id_producto, cantidad, precio_unitario) 
                                     VALUES ('$id_pedido', '" . $item['id_producto'] . "', '" . $item['cantidad'] . "', '" . $producto['precio'] . "')";
                $conexion->query($insertar_detalle);
                
                // Actualizar stock
                $actualizar_stock = "UPDATE productos SET stock = stock - " . $item['cantidad'] . " 
                                     WHERE id_producto = " . $item['id_producto'];
                $conexion->query($actualizar_stock);
            }
        }
        
        // 3. Limpiar carrito
        $_SESSION['carrito'] = array();
        
        // 4. Redirigir a confirmación
        header("Location: pago_exitoso.php?id_pedido=" . $id_pedido);
        exit();
    } else {
        $error = "Error al procesar el pedido: " . $conexion->error;
    }
}
?>

<?php include 'incluir/encabezado.php'; ?>

<div class="container">
    <h1 class="text-center mb-4"><i class="fas fa-credit-card mr-2"></i> Proceso de Pago</h1>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-shopping-bag mr-2"></i> Resumen de tu compra</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $total_carrito = 0;
                                foreach ($_SESSION['carrito'] as $item) {
                                    $consulta = "SELECT * FROM productos WHERE id_producto = " . $item['id_producto'];
                                    $resultado = $conexion->query($consulta);
                                    
                                    if ($resultado->num_rows > 0) {
                                        $producto = $resultado->fetch_assoc();
                                        $subtotal = $producto['precio'] * $item['cantidad'];
                                        $total_carrito += $subtotal;
                                        
                                        echo '
                                        <tr>
                                            <td>' . $producto['nombre'] . '</td>
                                            <td>' . $item['cantidad'] . '</td>
                                            <td>$' . number_format($producto['precio'], 2) . '</td>
                                            <td>$' . number_format($subtotal, 2) . '</td>
                                        </tr>
                                        ';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-money-bill-wave mr-2"></i> Total a pagar</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h3 class="text-center text-primary">$<?php echo number_format($total, 2); ?></h3>
                    </div>
                    
                    <form method="POST" action="">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle mr-2"></i> Información importante</h6>
                            <small>
                                Este es un proceso de pago simulado para fines educativos.
                                No se realizará ningún cargo real a tu tarjeta.
                            </small>
                        </div>
                        
                        <div class="form-group">
                            <label for="metodo_pago">Método de pago</label>
                            <select class="form-control" id="metodo_pago" disabled>
                                <option selected>Pago simulado (Demo)</option>
                            </select>
                        </div>
                        
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-success btn-lg btn-block">
                                <i class="fas fa-check-circle mr-2"></i> Confirmar y Pagar
                            </button>
                            <a href="carrito.php" class="btn btn-outline-secondary btn-block mt-2">
                                <i class="fas fa-arrow-left mr-2"></i> Volver al carrito
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'incluir/pie.php'; ?>