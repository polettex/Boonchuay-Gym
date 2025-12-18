<?php
/**
 * BOONCHUAY GYM - CHATBOT ENDPOINT
 * Handles chatbot messages and provides responses
 * 
 * FUTURE INTEGRATIONS:
 * - AI API (OpenAI, Anthropic, etc.)
 * - n8n webhook for automation workflows
 * - Advanced NLP processing
 */

// Include database connection
require_once 'config/db.php';

// Set content type to JSON
header('Content-Type: application/json');

// Initialize response
$response = array(
    'success' => false,
    'message' => '',
    'bot_response' => ''
);

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get message from request
    $input = json_decode(file_get_contents('php://input'), true);
    $userMessage = isset($input['message']) ? trim($input['message']) : '';

    // Validate message
    if (empty($userMessage)) {
        $response['message'] = 'Mensaje vacío';
        echo json_encode($response);
        exit;
    }

    // ============================================
    // SAVE TO CHATBOT LOGS
    // ============================================
    try {
        $stmt = $pdo->prepare("
            INSERT INTO chatbot_logs (usuario, bot) 
            VALUES (:usuario, :bot)
        ");

        // We'll update the bot response after generating it
        $stmt->execute([
            'usuario' => $userMessage,
            'bot' => '' // Will be updated later
        ]);

        $logId = $pdo->lastInsertId();

    } catch (PDOException $e) {
        error_log("Error saving chatbot log: " . $e->getMessage());
    }

    // ============================================
    // GENERATE BOT RESPONSE
    // ============================================

    // Method 1: Check FAQ database for keyword matches
    $botResponse = checkFAQ($userMessage, $pdo);

    // If no FAQ match, use default response
    if (empty($botResponse)) {
        $botResponse = getDefaultResponse($userMessage);
    }

    // ============================================
    // FUTURE AI INTEGRATION POINT
    // ============================================
    /*
    // Uncomment and configure when ready to integrate AI

    // Example: OpenAI Integration
    $apiKey = 'your-openai-api-key';
    $apiUrl = 'https://api.openai.com/v1/chat/completions';

    $aiRequest = [
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            [
                'role' => 'system',
                'content' => 'Eres un asistente virtual para Boonchuay Gym, un gimnasio de artes marciales en Sant Boi de Llobregat. Responde de forma amable y profesional sobre Muay Thai, Boxeo, Jeet Kune Do y Kali.'
            ],
            [
                'role' => 'user',
                'content' => $userMessage
            ]
        ],
        'max_tokens' => 150
    ];

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($aiRequest));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ]);

    $aiResponse = curl_exec($ch);
    curl_close($ch);

    $aiData = json_decode($aiResponse, true);
    if (isset($aiData['choices'][0]['message']['content'])) {
        $botResponse = $aiData['choices'][0]['message']['content'];
    }
    */

    // ============================================
    // FUTURE N8N WEBHOOK INTEGRATION POINT
    // ============================================
    /*
    // Uncomment when n8n workflow is ready

    $n8nWebhookUrl = 'https://your-n8n-instance.com/webhook/boonchuay-chatbot';

    $webhookData = [
        'user_message' => $userMessage,
        'timestamp' => date('Y-m-d H:i:s'),
        'source' => 'web_chatbot'
    ];

    $ch = curl_init($n8nWebhookUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($webhookData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $n8nResponse = curl_exec($ch);
    curl_close($ch);

    // Process n8n response if needed
    // This could trigger automations like:
    // - Send notification to staff
    // - Create task in CRM
    // - Schedule follow-up email
    // - Log to analytics platform
    */

    // ============================================
    // UPDATE CHATBOT LOG WITH RESPONSE
    // ============================================
    try {
        $stmt = $pdo->prepare("UPDATE chatbot_logs SET bot = :bot WHERE id = :id");
        $stmt->execute([
            'bot' => $botResponse,
            'id' => $logId
        ]);
    } catch (PDOException $e) {
        error_log("Error updating chatbot log: " . $e->getMessage());
    }

    // Return success response
    $response['success'] = true;
    $response['bot_response'] = $botResponse;
    $response['message'] = 'Respuesta generada correctamente';

} else {
    $response['message'] = 'Método de solicitud no válido';
}

echo json_encode($response);
exit;

// ============================================
// HELPER FUNCTIONS
// ============================================

/**
 * Check FAQ database for keyword matches
 */
function checkFAQ($message, $pdo)
{
    $message = strtolower($message);

    try {
        // Get all FAQs
        $stmt = $pdo->query("SELECT pregunta, respuesta FROM faq");
        $faqs = $stmt->fetchAll();

        // Check for keyword matches
        foreach ($faqs as $faq) {
            $pregunta = strtolower($faq['pregunta']);

            // Simple keyword matching
            if (strpos($message, 'horario') !== false && strpos($pregunta, 'horario') !== false) {
                return $faq['respuesta'];
            }
            if (strpos($message, 'ubicación') !== false || strpos($message, 'donde') !== false) {
                if (strpos($pregunta, 'ubicación') !== false || strpos($pregunta, 'ubicados') !== false) {
                    return $faq['respuesta'];
                }
            }
            if (strpos($message, 'niño') !== false && strpos($pregunta, 'niño') !== false) {
                return $faq['respuesta'];
            }
            if (strpos($message, 'disciplina') !== false && strpos($pregunta, 'disciplina') !== false) {
                return $faq['respuesta'];
            }
            if (
                (strpos($message, 'precio') !== false || strpos($message, 'cuota') !== false) &&
                strpos($pregunta, 'mensualidad') !== false
            ) {
                return $faq['respuesta'];
            }
            if (strpos($message, 'prueba') !== false && strpos($pregunta, 'prueba') !== false) {
                return $faq['respuesta'];
            }
        }

    } catch (PDOException $e) {
        error_log("Error querying FAQ: " . $e->getMessage());
    }

    return '';
}

/**
 * Get default response based on keywords
 */
function getDefaultResponse($message)
{
    $message = strtolower($message);

    // Greeting responses
    if (preg_match('/\b(hola|buenos|buenas|hey)\b/', $message)) {
        return '¡Hola! Bienvenido a Boonchuay Gym. ¿En qué puedo ayudarte? Puedo informarte sobre nuestras disciplinas, horarios, ubicación y más.';
    }

    // Thank you responses
    if (preg_match('/\b(gracias|thanks)\b/', $message)) {
        return '¡De nada! ¿Hay algo más en lo que pueda ayudarte?';
    }

    // Discipline specific
    if (strpos($message, 'muay thai') !== false) {
        return 'El Muay Thai es el arte de las ocho extremidades. Tenemos clases para todos los niveles. ¿Te gustaría conocer nuestros horarios o probar una clase gratis?';
    }

    if (strpos($message, 'boxeo') !== false) {
        return 'Nuestras clases de boxeo están diseñadas para mejorar tu técnica, velocidad y condición física. ¿Quieres más información sobre horarios?';
    }

    // Default fallback
    return 'Gracias por tu mensaje. Para información más específica, puedes contactarnos al 931 70 98 45 o rellenar el formulario de contacto. ¿Hay algo más en lo que pueda ayudarte?';
}
?>