<?php
session_start();
include 'incluir/conexion.php';
?>

<?php include 'incluir/encabezado.php'; ?>

<div class="jumbotron bg-primary text-white text-center">
    <h1 class="display-4"><i class="fas fa-rocket mr-3"></i> Labias Tech</h1>
    <p class="lead">Componentes de PC de Alta Gama - Potencia y Rendimiento</p>
    <hr class="my-4 bg-light">
    <p>Encuentra los mejores componentes para tu setup gaming o workstation.</p>
</div>

<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="text-center mb-4"><i class="fas fa-star mr-2"></i> Productos Destacados</h2>
        </div>
    </div>
    
    <div class="row">
        <?php
        // Consultar productos de la base de datos
        $consulta = "SELECT * FROM productos ORDER BY precio DESC";
        $resultado = $conexion->query($consulta);
        
        if ($resultado->num_rows > 0) {
            while ($producto = $resultado->fetch_assoc()) {
                $ruta_imagen = 'recursos/imagenes/' . $producto['imagen'];
                $categoria_class = '';
                
                switch($producto['categoria']) {
                    case 'GPU': $categoria_class = 'border-left border-danger'; break;
                    case 'CPU': $categoria_class = 'border-left border-primary'; break;
                    case 'RAM': $categoria_class = 'border-left border-success'; break;
                    default: $categoria_class = 'border-left border-warning'; break;
                }
                
                echo '
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm ' . $categoria_class . '">
                        <div class="card-header bg-light">
                            <span class="badge badge-info">' . $producto['categoria'] . '</span>
                            ' . ($producto['stock'] < 5 ? '<span class="badge badge-warning float-right">Últimas unidades</span>' : '') . '
                        </div>
                        <div class="card-body text-center">
                            <div style="height: 180px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                <img src="' . $ruta_imagen . '" class="img-fluid" alt="' . $producto['nombre'] . '" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                            </div>
                            <h5 class="card-title mt-3">' . $producto['nombre'] . '</h5>
                            <p class="card-text text-muted">' . substr($producto['descripcion'], 0, 80) . '...</p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <h4 class="text-primary mb-0">$' . number_format($producto['precio'], 2) . '</h4>
                                <span class="badge badge-secondary">Stock: ' . $producto['stock'] . '</span>
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            ' . (isset($_SESSION['usuario']) ? 
                                '<a href="carrito.php?accion=agregar&id=' . $producto['id_producto'] . '" class="btn btn-success btn-block">
                                    <i class="fas fa-cart-plus mr-2"></i>Agregar al Carrito
                                </a>' : 
                                '<a href="login.php" class="btn btn-outline-primary btn-block">
                                    <i class="fas fa-sign-in-alt mr-2"></i>Inicia sesión para comprar
                                </a>') . '
                        </div>
                    </div>
                </div>
                ';
            }
        } else {
            echo '<div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle fa-2x mb-3"></i>
                        <h4>No hay productos disponibles en este momento.</h4>
                        <p>Pronto añadiremos nuevos componentes.</p>
                    </div>
                  </div>';
        }
        ?>
    </div>
</div>

<?php include 'incluir/pie.php'; ?>