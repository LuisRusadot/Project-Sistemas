
<?php
require_once 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

// Conexión a base de datos
$conexion = new mysqli("localhost", "root", "", "banco_hojas_vida");
$conexion->set_charset("utf8");

// Obtener el último registro insertado
$sql = "SELECT * FROM hoja_vida ORDER BY id DESC LIMIT 1";
$resultado = $conexion->query($sql);
if (!$resultado || $resultado->num_rows == 0) {
    die("No hay registros disponibles");
}
$datos = $resultado->fetch_assoc();

// Cargar la imagen si existe
$imagen_html = '';
if (!empty($datos['foto']) && file_exists("uploads/" . $datos['foto'])) {
    $imagen_base64 = base64_encode(file_get_contents("uploads/" . $datos['foto']));
    $tipo_imagen = pathinfo($datos['foto'], PATHINFO_EXTENSION);
    $imagen_html = "<img src='data:image/$tipo_imagen;base64,$imagen_base64' style='width:150px; height:auto; border-radius:8px;'>";
}

// Preparar el HTML del PDF
$html = "
    <h1 style='text-align:center;'>Hoja de Vida</h1>
    <div style='text-align:center;'>$imagen_html</div>
    <br>
    <p><strong>Nombre:</strong> {$datos['nombre']}</p>
    <p><strong>Correo:</strong> {$datos['email']}</p>
    <p><strong>Teléfono:</strong> {$datos['telefono']}</p>
    <p><strong>Técnica en ITIPB:</strong> {$datos['tecnica']}</p>
    <p><strong>Perfil Profesional:</strong><br>{$datos['perfil']}</p>
    <p><strong>Idiomas:</strong> {$datos['idiomas']}</p>
    <p><strong>Certificación:</strong> {$datos['certificacion']}</p>
    <p><strong>Institución del curso:</strong> {$datos['institucion_curso']}</p>
    <p><strong>Fecha de certificación:</strong> {$datos['fecha_cert']}</p>
";

// Inicializar Dompdf
$options = new Options();
$options->set('defaultFont', 'Arial');
$options->set('isRemoteEnabled', true);  // Necesario para imágenes base64
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("hoja_de_vida_{$datos['nombre']}.pdf", ["Attachment" => true]);
?>
