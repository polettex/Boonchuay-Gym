<?php
/**
 * BOONCHUAY GYM - CONTACT FORM HANDLER
 * Processes contact form submissions, saves to database, and sends emails
 */

// Include database connection
require_once 'config/db.php';

// Set content type to JSON
header('Content-Type: application/json');

// Initialize response array
$response = array('success' => false, 'message' => '');

// Check if form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitize and validate input data
    $nombre = isset($_POST['name']) ? trim(strip_tags($_POST['name'])) : '';
    $email = isset($_POST['email']) ? trim(strip_tags($_POST['email'])) : '';
    $telefono = isset($_POST['phone']) ? trim(strip_tags($_POST['phone'])) : '';
    $mensaje = isset($_POST['message']) ? trim(strip_tags($_POST['message'])) : '';

    // Validation
    $errors = array();

    // Validate name
    if (empty($nombre) || strlen($nombre) < 3) {
        $errors[] = 'El nombre debe tener al menos 3 caracteres';
    }

    // Validate email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email no válido';
    }

    // Validate phone
    if (empty($telefono) || !preg_match('/^[0-9]{9,15}$/', preg_replace('/\s+/', '', $telefono))) {
        $errors[] = 'Teléfono no válido';
    }

    // Validate message
    if (empty($mensaje) || strlen($mensaje) < 10) {
        $errors[] = 'El mensaje debe tener al menos 10 caracteres';
    }

    // If there are validation errors, return them
    if (!empty($errors)) {
        $response['message'] = implode(', ', $errors);
        echo json_encode($response);
        exit;
    }

    // ============================================
    // SAVE LEAD TO DATABASE
    // ============================================
    try {
        $stmt = $pdo->prepare("
            INSERT INTO leads (nombre, email, telefono, mensaje, origen) 
            VALUES (:nombre, :email, :telefono, :mensaje, :origen)
        ");

        $stmt->execute([
            'nombre' => $nombre,
            'email' => $email,
            'telefono' => $telefono,
            'mensaje' => $mensaje,
            'origen' => 'web'
        ]);

        $leadId = $pdo->lastInsertId();

        // Log successful database insertion
        error_log("New lead saved to database with ID: " . $leadId);

    } catch (PDOException $e) {
        // Log database error
        error_log("Database error saving lead: " . $e->getMessage());
        $response['message'] = 'Error al guardar el mensaje. Por favor, inténtalo de nuevo.';
        echo json_encode($response);
        exit;
    }

    // ============================================
    // SEND EMAIL NOTIFICATION
    // ============================================

    // Email configuration
    $to = 'davidpedrosa1988@gmail.com'; // Gym email
    $subject = 'Nuevo mensaje de contacto - Boonchuay Gym';

    // Create email body
    $email_body = "Has recibido un nuevo mensaje de contacto desde la web de Boonchuay Gym.\n\n";
    $email_body .= "Detalles del contacto:\n";
    $email_body .= "------------------------\n";
    $email_body .= "Nombre: " . $nombre . "\n";
    $email_body .= "Email: " . $email . "\n";
    $email_body .= "Teléfono: " . $telefono . "\n";
    $email_body .= "------------------------\n\n";
    $email_body .= "Mensaje:\n";
    $email_body .= $mensaje . "\n\n";
    $email_body .= "------------------------\n";
    $email_body .= "Este mensaje fue guardado en la base de datos con ID: " . $leadId . "\n";
    $email_body .= "Fecha: " . date('d/m/Y H:i:s') . "\n";

    // Email headers
    $headers = array();
    $headers[] = 'From: Boonchuay Gym Web <noreply@boonchuaygym.com>';
    $headers[] = 'Reply-To: ' . $email;
    $headers[] = 'X-Mailer: PHP/' . phpversion();
    $headers[] = 'Content-Type: text/plain; charset=UTF-8';

    // Send email (optional - may not work on local WAMP without mail server)
    $mail_sent = @mail($to, $subject, $email_body, implode("\r\n", $headers));

    if ($mail_sent) {
        error_log("Email notification sent successfully");

        // Optional: Send confirmation email to user
        $user_subject = 'Gracias por contactar con Boonchuay Gym';
        $user_body = "Hola " . $nombre . ",\n\n";
        $user_body .= "Gracias por contactar con Boonchuay Gym. Hemos recibido tu mensaje y te responderemos lo antes posible.\n\n";
        $user_body .= "Tu mensaje:\n";
        $user_body .= "------------------------\n";
        $user_body .= $mensaje . "\n";
        $user_body .= "------------------------\n\n";
        $user_body .= "Si tienes alguna pregunta urgente, no dudes en llamarnos al 931 70 98 45.\n\n";
        $user_body .= "Saludos,\n";
        $user_body .= "El equipo de Boonchuay Gym\n";
        $user_body .= "Carrer d'Eusebi Güell 14, 08830 Sant Boi de Llobregat\n";

        $user_headers = array();
        $user_headers[] = 'From: Boonchuay Gym <noreply@boonchuaygym.com>';
        $user_headers[] = 'Reply-To: davidpedrosa1988@gmail.com';
        $user_headers[] = 'X-Mailer: PHP/' . phpversion();
        $user_headers[] = 'Content-Type: text/plain; charset=UTF-8';

        @mail($email, $user_subject, $user_body, implode("\r\n", $user_headers));
    } else {
        error_log("Email notification failed (this is normal on local WAMP without mail server)");
    }

    // Success response (lead was saved to database regardless of email status)
    $response['success'] = true;
    $response['message'] = 'Mensaje enviado y guardado correctamente';
    $response['lead_id'] = $leadId;

} else {
    $response['message'] = 'Método de solicitud no válido';
}

// Return JSON response
echo json_encode($response);
exit;
?>