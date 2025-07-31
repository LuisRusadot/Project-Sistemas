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
    $habilidades = isset($_POST['habilidades']) ? implode(', ', $_POST['habilidades']) : '';

    $foto = '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
        $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nombre_archivo = uniqid() . '.' . $extension;
        $ruta_destino = 'uploads/' . $nombre_archivo;
        
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $ruta_destino)) {
            $foto = $nombre_archivo;
        }
    }

    // Insertar hoja de vida (agrega habilidades)
    $stmt = $conn->prepare("INSERT INTO hoja_vida (nombre, email, telefono, tecnica, perfil, idiomas, certificacion, institucion_curso, fecha_cert, foto, habilidades) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssss", $nombre, $email, $telefono, $tecnica, $perfil, $idiomas, $certificacion, $institucion_curso, $fecha_cert, $foto, $habilidades);
    
    if ($stmt->execute()) {
        $id_hoja_vida = $stmt->insert_id;

        // Insertar experiencia laboral
        for ($i = 1; $i <= 3; $i++) {
            $empresa = $conn->real_escape_string($_POST["empresa$i"] ?? '');
            $inicio = !empty($_POST["inicio$i"]) ? $_POST["inicio$i"] . "-01" : null;
            $fin = !empty($_POST["fin$i"]) ? $_POST["fin$i"] . "-01" : null;
            $puesto = $conn->real_escape_string($_POST["puesto$i"] ?? '');
            $funciones = $conn->real_escape_string($_POST["funciones$i"] ?? '');
            if ($empresa || $puesto || $funciones) {
                $stmt_exp = $conn->prepare("INSERT INTO experiencia (id_hoja_vida, empresa, inicio, fin, puesto, funciones) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt_exp->bind_param("isssss", $id_hoja_vida, $empresa, $inicio, $fin, $puesto, $funciones);
                $stmt_exp->execute();
                $stmt_exp->close();
            }
        }

        // Insertar educación
        for ($i = 1; $i <= 3; $i++) {
            $institucion = $conn->real_escape_string($_POST["institucion$i"] ?? '');
            $ubicacion = $conn->real_escape_string($_POST["ubicacion$i"] ?? '');
            $titulo = $conn->real_escape_string($_POST["titulo$i"] ?? '');
            $fecha = $_POST["fecha$i"] ?? null;
            if ($institucion || $titulo) {
                $stmt_edu = $conn->prepare("INSERT INTO educacion (id_hoja_vida, institucion, ubicacion, titulo, fecha) VALUES (?, ?, ?, ?, ?)");
                $stmt_edu->bind_param("issss", $id_hoja_vida, $institucion, $ubicacion, $titulo, $fecha);
                $stmt_edu->execute();
                $stmt_edu->close();
            }
        }

        echo "<script>alert('Registro exitoso'); window.location.href = 'Projecto_Hojas_De_Vidaa.html';</script>";
    } else {
        echo "<script>alert('Error al registrar: " . $conn->error . "'); window.history.back();</script>";
    }
    
    $stmt->close();
}

$conn->close();
?>
