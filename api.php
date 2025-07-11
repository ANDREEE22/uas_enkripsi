<?php
// Tambahkan error reporting di awal
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Mulai session dengan pengaturan yang lebih kompatibel
session_start([
    'cookie_lifetime' => 86400,
    'read_and_close'  => false,
    'use_strict_mode' => true
]);
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

// Inisialisasi session jika belum ada
if (!isset($_SESSION['perangkat1'])) {
    $_SESSION['perangkat1'] = [];
    $_SESSION['perangkat2'] = [];
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'send':
        handleSendMessage();
        break;
    case 'get':
        handleGetMessages();
        break;
    case 'clear':
        handleClearChat();
        break;
    default:
        echo json_encode(['error' => 'Aksi tidak valid']);
}

function handleSendMessage() {
    $device = $_POST['device'] ?? '';
    $text = $_POST['text'] ?? '';
    $shift = isset($_POST['shift']) ? (int)$_POST['shift'] : 3;
    
    if (empty($text)) {
        echo json_encode(['error' => 'Pesan tidak boleh kosong']);
        return;
    }
    
    $result = '';
    $shift = $shift % 95;
    $time = date('H:i');
    
    if ($device === '1') {
        // Enkripsi dari perangkat 1
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
        
        $_SESSION['perangkat1'][] = [
            'type' => 'sent',
            'text' => $text,
            'time' => $time
        ];
        
        $_SESSION['perangkat2'][] = [
            'type' => 'received',
            'text' => $result,
            'time' => $time
        ];
        
        echo json_encode([
            'success' => true,
            'message' => $text
        ]);
    } 
    elseif ($device === '2') {
        // Dekripsi dari perangkat 2
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
        
        $_SESSION['perangkat2'][] = [
            'type' => 'sent',
            'text' => $text,
            'time' => $time
        ];
        
        $_SESSION['perangkat1'][] = [
            'type' => 'received',
            'text' => $result,
            'time' => $time
        ];
        
        echo json_encode([
            'success' => true,
            'message' => $text
        ]);
    }
    else {
        echo json_encode(['error' => 'Perangkat tidak valid']);
    }
}

function handleGetMessages() {
    $device = $_GET['device'] ?? '';
    
    if ($device === '1') {
        echo json_encode([
            'messages' => $_SESSION['perangkat1'] ?? []
        ]);
    } 
    elseif ($device === '2') {
        echo json_encode([
            'messages' => $_SESSION['perangkat2'] ?? []
        ]);
    }
    else {
        echo json_encode(['error' => 'Perangkat tidak valid']);
    }
}

function handleClearChat() {
    $device = $_GET['device'] ?? '';
    
    if ($device === '1') {
        $_SESSION['perangkat1'] = [];
    } 
    elseif ($device === '2') {
        $_SESSION['perangkat2'] = [];
    }
    
    echo json_encode(['success' => true]);
}
?>