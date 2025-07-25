<?php
// Configuración de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar si dompdf está instalado
$dompdf_path = 'dompdf/autoload.inc.php';
if (!file_exists($dompdf_path)) {
    die('<div style="color: red; padding: 20px; font-family: Arial;">
           <h2>Error crítico</h2>
           <p>La librería dompdf no está instalada correctamente.</p>
           <p>Por favor, descárguela de <a href="https://github.com/dompdf/dompdf" target="_blank">GitHub</a> y colóquela en la carpeta dompdf/</p>
         </div>');
}

require $dompdf_path;
use Dompdf\Dompdf;
use Dompdf\Options;

try {
    // Conexión a la base de datos
    $conn = new mysqli("localhost", "root", "", "banco_hojas_vida");
    if ($conn->connect_error) {
        throw new Exception("Error de conexión: " . $conn->connect_error);
    }

    // Consulta SQL
    $sql = "SELECT nombre, email, telefono, tecnica, perfil, idiomas, certificacion, 
                   DATE_FORMAT(fecha_registro, '%d/%m/%Y %H:%i') as fecha_registro 
            FROM hoja_vida 
            ORDER BY fecha_registro DESC";
    $resultado = $conn->query($sql);
    
    if (!$resultado) {
        throw new Exception("Error en la consulta: " . $conn->error);
    }

    // Construir HTML
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <style>
            body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
            .header { display: flex; align-items: center; margin-bottom: 20px; }
            .logo { height: 60px; margin-right: 20px; }
            h1 { color: #004080; margin: 0; font-size: 24px; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th { background-color: #004080; color: white; padding: 10px; text-align: left; }
            td { padding: 8px; border: 1px solid #ddd; }
            .footer { margin-top: 30px; font-size: 12px; color: #666; text-align: center; }
            .text-small { font-size: 12px; }
        </style>
    </head>
    <body>
        <div class="header">
            <img class="logo" src="https://i.ibb.co/mr9KNN8s/escudo-pb.png" alt="Logo ITIPB">
            <div>
                <h1>Banco de Oportunidades ITIPB</h1>
                <p class="text-small">Registro de Hojas de Vida - ' . date('d/m/Y H:i:s') . '</p>
            </div>
        </div>';

    if ($resultado->num_rows > 0) {
        $html .= '<table>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Técnica</th>
                        <th>Perfil</th>
                        <th>Idiomas</th>
                        <th>Certificaciones</th>
                        <th class="text-small">Registrado</th>
                    </tr>';

        while ($fila = $resultado->fetch_assoc()) {
            $html .= '<tr>
                        <td>' . htmlspecialchars($fila['nombre']) . '</td>
                        <td>' . htmlspecialchars($fila['email']) . '</td>
                        <td>' . htmlspecialchars($fila['telefono']) . '</td>
                        <td>' . htmlspecialchars($fila['tecnica']) . '</td>
                        <td>' . nl2br(htmlspecialchars(substr($fila['perfil'], 0, 100)) . '...') . '</td>
                        <td>' . htmlspecialchars($fila['idiomas']) . '</td>
                        <td>' . htmlspecialchars($fila['certificacion']) . '</td>
                        <td class="text-small">' . htmlspecialchars($fila['fecha_registro']) . '</td>
                    </tr>';
        }

        $html .= '</table>';
    } else {
        $html .= '<p style="text-align: center; color: #666; margin-top: 50px;">No hay registros disponibles</p>';
    }

    $html .= '<div class="footer">
                Generado automáticamente por el sistema ITIPB - ' . date('d/m/Y') . '
              </div>
              </body>
              </html>';

    // Configurar Dompdf
    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $options->set('isHtml5ParserEnabled', true);
    
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();

    // Encabezados para descarga correcta
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="hojas_de_vida_itipb.pdf"');
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');

    // Enviar el PDF al navegador
    echo $dompdf->output();

} catch (Exception $e) {
    die('<div style="color: red; padding: 20px; font-family: Arial; border: 1px solid red; margin: 20px;">
           <h2>Error al generar el PDF</h2>
           <p>' . htmlspecialchars($e->getMessage()) . '</p>
           <p>Por favor, intente nuevamente o contacte al administrador.</p>
         </div>');
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>