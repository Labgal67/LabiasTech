<?php
session_start();
include 'incluir/conexion.php';

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = array();
}

// Procesar acciones del carrito
if (isset($_GET['accion']) && isset($_GET['id'])) {
    $id_producto = (int)$_GET['id'];
    
    if ($_GET['accion'] == 'agregar') {
        // Agregar producto al carrito
        $encontrado = false;
        foreach ($_SESSION['carrito'] as &$item) {
            if ($item['id_producto'] == $id_producto) {
                $item['cantidad']++;
                $encontrado = true;
                break;
            }
        }
        
        if (!$encontrado) {
            $_SESSION['carrito'][] = array('id_producto' => $id_producto, 'cantidad' => 1);
        }
        
        header("Location: carrito.php");
        exit();
        
    } elseif ($_GET['accion'] == 'eliminar') {
        // Eliminar producto del carrito
        foreach ($_SESSION['carrito'] as $indice => $item) {
            if ($item['id_producto'] == $id_producto) {
                unset($_SESSION['carrito'][$indice]);
                break;
            }
        }
        $_SESSION['carrito'] = array_values($_SESSION['carrito']);
        
    } elseif ($_GET['accion'] == 'actualizar' && isset($_POST['cantidad'])) {
        // Actualizar cantidad
        foreach ($_SESSION['carrito'] as $indice => $item) {
            if ($item['id_producto'] == $id_producto) {
                $nueva_cantidad = (int)$_POST['cantidad'];
                if ($nueva_cantidad > 0) {
                    $_SESSION['carrito'][$indice]['cantidad'] = $nueva_cantidad;
                } else {
                    unset($_SESSION['carrito'][$indice]);
                    $_SESSION['carrito'] = array_values($_SESSION['carrito']);
                }
                break;
            }
        }
    }
}
?>

<?php include 'incluir/encabezado.php'; ?>

<div class="container">
    <h1 class="text-center mb-4"><i class="fas fa-shopping-cart mr-2"></i> Carrito de Compras</h1>
    
    <?php if (empty($_SESSION['carrito'])): ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-shopping-cart fa-3x mb-3"></i>
            <h3>Tu carrito está vacío</h3>
            <p>Agrega algunos productos para comenzar a comprar.</p>
            <a href="index.php" class="btn btn-primary mt-2">
                <i class="fas fa-store mr-2"></i> Ver Productos
            </a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>Producto</th>
                        <th>Precio Unitario</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = 0;
                    foreach ($_SESSION['carrito'] as $item) {
                        $consulta = "SELECT * FROM productos WHERE id_producto = " . $item['id_producto'];
                        $resultado = $conexion->query($consulta);
                        
                        if ($resultado->num_rows > 0) {
                            $producto = $resultado->fetch_assoc();
                            $subtotal = $producto['precio'] * $item['cantidad'];
                            $total += $subtotal;
                            
                            echo '
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="recursos/imagenes/' . $producto['imagen'] . '" alt="' . $producto['nombre'] . '" 
                                             style="width: 60px; height: 60px; object-fit: contain; margin-right: 15px;">
                                        <div>
                                            <strong>' . $producto['nombre'] . '</strong><br>
                                            <small class="text-muted">' . substr($producto['descripcion'], 0, 60) . '...</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle">$' . number_format($producto['precio'], 2) . '</td>
                                <td class="align-middle">
                                    <form method="POST" action="carrito.php?accion=actualizar&id=' . $item['id_producto'] . '" class="form-inline">
                                        <input type="number" name="cantidad" value="' . $item['cantidad'] . '" 
                                               min="1" max="' . $producto['stock'] . '" class="form-control form-control-sm mr-2" style="width: 80px;">
                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </form>
                                </td>
                                <td class="align-middle"><strong>$' . number_format($subtotal, 2) . '</strong></td>
                                <td class="align-middle">
                                    <a href="carrito.php?accion=eliminar&id=' . $item['id_producto'] . '" 
                                       class="btn btn-danger btn-sm" onclick="return confirm(\'¿Eliminar este producto del carrito?\')">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </a>
                                </td>
                            </tr>
                            ';
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-6">
                <a href="index.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left mr-2"></i> Seguir Comprando
                </a>
            </div>
            <div class="col-md-6 text-right">
                <div class="card bg-light">
                    <div class="card-body">
                        <h4 class="card-title">Resumen del Pedido</h4>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span>Subtotal:</span>
                            <span>$<?php echo number_format($total, 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Envío:</span>
                            <span>Gratis</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Total:</strong>
                            <strong class="text-primary">$<?php echo number_format($total, 2); ?></strong>
                        </div>
                        <div class="mt-3">
                            <?php if(isset($_SESSION['usuario'])): ?>
                                <a href="pago.php" class="btn btn-success btn-block btn-lg">
                                    <i class="fas fa-credit-card mr-2"></i> Proceder al Pago
                                </a>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-warning btn-block btn-lg">
                                    <i class="fas fa-sign-in-alt mr-2"></i> Inicia sesión para pagar
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'incluir/pie.php'; ?>