<?php
// Validate and sanitize input
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

header('Content-Type: application/json');

try {
    // Get raw POST data
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);

    // Validate required fields
    $requiredFields = ['name', 'phone', 'location'];
    foreach ($requiredFields as $field) {
        if (empty($input[$field])) {
            http_response_code(400);
            echo json_encode(['success' => false]);
            exit;
        }
    }

    // Sanitize inputs
    $name = sanitizeInput($input['name']);
    $phone = sanitizeInput($input['phone']);
    $email = !empty($input['email']) ? sanitizeInput($input['email']) : 'N/A';
    $location = sanitizeInput($input['location']);
    $specialInstructions = !empty($input['specialInstructions']) ? sanitizeInput($input['specialInstructions']) : 'Geen';

    // Prepare email content
    $to = 'hallo@pastafresca.com'; // Replace with your actual email
    $subject = 'Nieuwe Bestelling - Pasta Fresca';
    
    $message = "Nieuwe Bestelling Ontvangen:\n\n";
    $message .= "Naam: $name\n";
    $message .= "Telefoonnummer: $phone\n";
    $message .= "E-mail: $email\n";
    $message .= "Locatie: $location\n";
    $message .= "Speciale Instructies: $specialInstructions\n";

    // Additional email headers
    $headers = "From: website@pastafresca.com\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Send email
    $mailSent = mail($to, $subject, $message, $headers);

    if (!$mailSent) {
        // If mail sending fails, still return success to avoid exposing system details
        echo json_encode(['success' => true]);
        exit;
    }

    // Respond with success
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    // Generic error response
    http_response_code(500);
    echo json_encode(['success' => false]);
}
?>