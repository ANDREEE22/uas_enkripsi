<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

// Database configuration
$dbHost = 'localhost';
$dbName = 'chat_app';
$dbUser = 'root';
$dbPass = '';

try {
    $db = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8", $dbUser, $dbPass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'send':
        handleSendMessage($db);
        break;
    case 'get':
        handleGetMessages($db);
        break;
    case 'clear':
        handleClearChat($db);
        break;
    default:
        echo json_encode(['error' => 'Invalid action']);
}

function handleSendMessage($db) {
    $device = $_POST['device'] ?? '';
    $text = $_POST['text'] ?? '';
    $shift = isset($_POST['shift']) ? (int)$_POST['shift'] : 3;
    
    if (empty($text)) {
        echo json_encode(['error' => 'Message cannot be empty']);
        return;
    }
    
    $time = date('H:i');
    
    if ($device === '1') {
        $encryptedText = encryptText($text, $shift);
        
        saveMessage($db, '1', 'sent', $text, $text, $shift, $time);
        saveMessage($db, '2', 'received', $encryptedText, $text, $shift, $time);
        
        echo json_encode([
            'success' => true,
            'message' => $text,
            'original' => $text
        ]);
    } 
    elseif ($device === '2') {
        $decryptedText = decryptText($text, $shift);
        
        saveMessage($db, '2', 'sent', $text, $decryptedText, $shift, $time);
        saveMessage($db, '1', 'received', $decryptedText, $decryptedText, $shift, $time);
        
        echo json_encode([
            'success' => true,
            'message' => $text,
            'original' => $decryptedText
        ]);
    }
    else {
        echo json_encode(['error' => 'Invalid device']);
    }
}

function handleGetMessages($db) {
    $device = $_GET['device'] ?? '';
    
    try {
        $stmt = $db->prepare("SELECT * FROM messages WHERE device_id = ? ORDER BY created_at");
        $stmt->execute([$device]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $formattedMessages = [];
        foreach ($messages as $msg) {
            $formattedMessages[] = [
                'type' => $msg['message_type'],
                'text' => $msg['content'],
                'original' => $msg['original_content'],
                'time' => date('H:i', strtotime($msg['created_at']))
            ];
        }
        
        echo json_encode(['messages' => $formattedMessages]);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Failed to get messages: ' . $e->getMessage()]);
    }
}

function handleClearChat($db) {
    $device = $_GET['device'] ?? '';
    
    try {
        $stmt = $db->prepare("DELETE FROM messages WHERE device_id = ?");
        $stmt->execute([$device]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Failed to clear chat: ' . $e->getMessage()]);
    }
}

function saveMessage($db, $deviceId, $type, $content, $originalContent, $shift, $time) {
    try {
        $stmt = $db->prepare("INSERT INTO messages (device_id, message_type, content, original_content, shift_value) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$deviceId, $type, $content, $originalContent, $shift]);
    } catch (PDOException $e) {
        error_log('Failed to save message: ' . $e->getMessage());
    }
}

function encryptText($text, $shift) {
    $result = '';
    $shift = $shift % 95;
    
    for ($i = 0; $i < strlen($text); $i++) {
        $char = $text[$i];
        $code = ord($char);
        
        if ($code >= 32 && $code <= 126) {
            $newCode = 32 + ($code - 32 + $shift) % 95;
            $result .= chr($newCode);
        } else {
            $result .= $char;
        }
    }
    
    return $result;
}

function decryptText($text, $shift) {
    $result = '';
    $shift = $shift % 95;
    
    for ($i = 0; $i < strlen($text); $i++) {
        $char = $text[$i];
        $code = ord($char);
        
        if ($code >= 32 && $code <= 126) {
            $newCode = 32 + ($code - 32 - $shift + 95) % 95;
            $result .= chr($newCode);
        } else {
            $result .= $char;
        }
    }
    
    return $result;
}
?>