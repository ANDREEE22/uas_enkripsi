<?php
session_start();
if (!isset($_SESSION['perangkat1'])) {
    $_SESSION['perangkat1'] = [];
    $_SESSION['perangkat2'] = [];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perangkat 1 - Enkripsi Realtime</title>
    <link rel="stylesheet" href="css/style.css">
    
</head>

<body>
    <div class="header">
        <img src="img/hytamm.jpeg" alt="Circular diagram" width="50px" height="50px" style="border-radius: 50%; object-fit: cover;">
        <h2>KUCING HYTAMM</h2>
        <div class="dropdown">
            <button class="dropbtn" id="menuBtn">
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
            </button>
            <div class="dropdown-content" id="dropdownMenu">
                <div class="dropdown-item">Mode: Enkripsi</div>
                <div class="shift-input">
                    <label for="shiftValue">Shift:</label>
                    <input type="number" id="shiftValue" min="1" max="94" value="3">
                </div>
                <div class="dropdown-item clear-btn" id="clearChat">Hapus Riwayat</div>
            </div>
        </div>
        <div class="menu-backdrop" id="menuBackdrop"></div>
    </div>
    
    <div class="chat-container" id="chatContainer">
        <?php
        if (!empty($_SESSION['perangkat1'])) {
            foreach ($_SESSION['perangkat1'] as $index => $message) {
                echo '<div class="message '.$message['type'].'" data-id="'.$index.'">';
                echo '<div>'.$message['text'].'</div>';
                echo '<div class="time">'.$message['time'].'</div>';
                echo '</div>';
            }
        }
        ?>
    </div>
    
    <form id="messageForm" class="input-area">
        <textarea id="messageInput" placeholder="Ketik pesan..." required></textarea>
        <button type="submit">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="22" y1="2" x2="11" y2="13"></line>
                <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
            </svg>
        </button>
    </form>

   <script src="js/hh.js"></script> 
</body>
</html>