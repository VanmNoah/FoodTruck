<?php
// Configuratie
$config = [
    'phone_number' => '+32468253584',
    'verification_code_length' => 6,
    'verification_code_expiry' => 5 * 60,
    'session_name' => 'secure_login_session'
];

// Beveiligde sessie configuratie
session_name($config['session_name']);
session_start([
    'cookie_httponly' => true,
    'cookie_secure' => true,
    'cookie_samesite' => 'Strict',
    'gc_maxlifetime' => 1800 // 30 minuten
]);

// CSRF bescherming
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Functie voor het genereren van een veilige verificatie code
function generateSecureCode($length) {
    $characters = '0123456789';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[random_int(0, strlen($characters) - 1)];
    }
    return $code;
}

// Functie voor het valideren van de telefoon
function validatePhoneNumber($number) {
    $pattern = '/^\+[1-9][0-9]{1,14}$/';
    return preg_match($pattern, $number);
}

// Functie voor het controleren van de verificatie code
function checkVerificationCode($code, $expiry) {
    if (!is_string($code) || strlen($code) !== 6) {
        return false;
    }
    if (time() > $expiry) {
        return false;
    }
    return true;
}

// Login status controleren
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

// Verificatie code versturen
if (isset($_POST['send_code']) && $_POST['send_code'] == 1) {
    if (!validatePhoneNumber($config['phone_number'])) {
        header('Location: ?error=Invalid+phone+number');
        exit;
    }

    $verification_code = generateSecureCode($config['verification_code_length']);
    $_SESSION['verification_code'] = $verification_code;
    $_SESSION['verification_code_expiry'] = time() + $config['verification_code_expiry'];
    
    if (function_exists('send_verification_code')) {
        send_verification_code($verification_code, $config['phone_number']);
    }
    
    // Voor demo doeleinden
    $_SESSION['demo_code'] = $verification_code;
    
    header('Location: ?code_sent=true');
    exit;
}

// Code verificatie
if (isset($_POST['verify_code']) && isset($_POST['csrf_token'])) {
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header('Location: ?error=Invalid+request');
        exit;
    }

    $user_code = $_POST['verification_code'];
    $stored_code = $_SESSION['verification_code'] ?? '';
    $expiry = $_SESSION['verification_code_expiry'] ?? 0;

    if (!checkVerificationCode($user_code, $expiry)) {
        header('Location: ?error=Code+has+expired');
        exit;
    }

    if ($user_code === $stored_code) {
        $_SESSION['logged_in'] = true;
        session_regenerate_id(true);
        header('Location: dashboard.php');
        exit;
    } else {
        header('Location: ?error=Invalid+verification+code');
        exit;
    }
}

// Template
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
    <title>Veilige Login</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 350px;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }
        button {
            background-color: #4285f4;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 12px 20px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #3367d6;
        }
        .alert {
            padding: 10px;
            background-color: #f44336;
            color: white;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .success {
            background-color: #4CAF50;
        }
        .message {
            text-align: center;
            margin: 15px 0;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Veilige Login</h2>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['code_sent']) || isset($_SESSION['verification_code'])): ?>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <div class="form-group">
                    <label for="verification_code">Verificatie Code:</label>
                    <input type="text" id="verification_code" name="verification_code" required
                           placeholder="Voer de code in die naar je telefoon is gestuurd">
                </div>
                <button type="submit" name="verify_code">Verifiëren & Inloggen</button>
            </form>
            <p class="message">
                Een verificatie code is verstuurd naar je telefoon.
                <?php if (isset($_SESSION['demo_code'])): ?>
                    <br><strong>Demo code: <?php echo $_SESSION['demo_code']; ?></strong>
                <?php endif; ?>
            </p>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                <input type="hidden" name="send_code" value="1">
                <button type="submit" style="background-color: #888; margin-top: 10px;">Nieuwe Code Versturen</button>
            </form>
        <?php else: ?>
            <p class="message">Klik op de knop hieronder om een verificatie code te ontvangen op je telefoon.</p>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                <input type="hidden" name="send_code" value="1">
                <button type="submit">Verstuur Verificatie Code</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>