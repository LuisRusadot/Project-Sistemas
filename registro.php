<?php
// Crear carpeta uploads si no existe
if (!file_exists('uploads')) {
    mkdir('uploads', 0777, true);
}

$conexion = new mysqli("localhost", "root", "", "banco_hojas_vida");
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $conexion->real_escape_string($_POST['nombre']);
    $email = $conexion->real_escape_string($_POST['email']);
    $telefono = $conexion->real_escape_string($_POST['telefono']);
    $tecnica = $conexion->real_escape_string($_POST['tecnica']);
    $perfil = $conexion->real_escape_string($_POST['perfil']);
    $idiomas = $conexion->real_escape_string($_POST['idiomas'] ?? '');
    $certificacion = $conexion->real_escape_string($_POST['certificacion'] ?? '');
    $institucion_curso = $conexion->real_escape_string($_POST['institucion_curso'] ?? '');
    $fecha_cert = $conexion->real_escape_string($_POST['fecha_cert'] ?? '');

    $foto = '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
        $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nombre_archivo = uniqid() . '.' . $extension;
        $ruta_destino = 'uploads/' . $nombre_archivo;
        
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $ruta_destino)) {
            $foto = $nombre_archivo;
        }
    }

    $stmt = $conexion->prepare("INSERT INTO hoja_vida (nombre, email, telefono, tecnica, perfil, idiomas, certificacion, institucion_curso, fecha_cert, foto) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssss", $nombre, $email, $telefono, $tecnica, $perfil, $idiomas, $certificacion, $institucion_curso, $fecha_cert, $foto);
    
    if ($stmt->execute()) {
        echo "<script>alert('Registro exitoso'); window.location.href = 'Projecto_Hojas_De_Vidaa.html';</script>";
    } else {
        echo "<script>alert('Error al registrar: " . $conexion->error . "'); window.history.back();</script>";
    }
    
    $stmt->close();
}

$conexion->close();
?>
