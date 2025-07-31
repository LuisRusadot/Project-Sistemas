<?php
require_once 'conexion.php'; // Incluir la conexión

// Crear carpeta uploads si no existe
if (!file_exists('uploads')) {
    mkdir('uploads', 0777, true);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $email = $conn->real_escape_string($_POST['email']);
    $telefono = $conn->real_escape_string($_POST['telefono']);
    $tecnica = $conn->real_escape_string($_POST['tecnica']);
    $perfil = $conn->real_escape_string($_POST['perfil']);
    $idiomas = $conn->real_escape_string($_POST['idiomas'] ?? '');
    $certificacion = $conn->real_escape_string($_POST['certificacion'] ?? '');
    $institucion_curso = $conn->real_escape_string($_POST['institucion_curso'] ?? '');
    $fecha_cert = $conn->real_escape_string($_POST['fecha_cert'] ?? '');

    $foto = '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
        $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nombre_archivo = uniqid() . '.' . $extension;
        $ruta_destino = 'uploads/' . $nombre_archivo;
        
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $ruta_destino)) {
            $foto = $nombre_archivo;
        }
    }

    $stmt = $conn->prepare("INSERT INTO hoja_vida (nombre, email, telefono, tecnica, perfil, idiomas, certificacion, institucion_curso, fecha_cert, foto) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssss", $nombre, $email, $telefono, $tecnica, $perfil, $idiomas, $certificacion, $institucion_curso, $fecha_cert, $foto);
    
    if ($stmt->execute()) {
        echo "<script>alert('Registro exitoso'); window.location.href = 'Projecto_Hojas_De_Vidaa.html';</script>";
    } else {
        echo "<script>alert('Error al registrar: " . $conn->error . "'); window.history.back();</script>";
    }
    
    $stmt->close();
}

$conn->close();
?>
