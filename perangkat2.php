<?php
session_start();
if (!isset($_SESSION['perangkat2'])) {
    $_SESSION['perangkat1'] = [];
    $_SESSION['perangkat2'] = [];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perangkat 2 - Dekripsi Realtime</title>
    <link rel="stylesheet" href="css/style.css">
    
</head>

<body>
    
    <div class="header">
         <img src="img/oyenn.jpg" alt="Circular diagram" width="50px" height="50px" style="border-radius: 50%; object-fit: cover;">
        <h2>KUCING OREN</h2>
        <div class="dropdown">
            <button class="dropbtn" id="menuBtn">
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
            </button>
            <div class="dropdown-content" id="dropdownMenu">
                <div class="dropdown-item">Mode: Dekripsi</div>
                <div class="shift-input">
                    <label for="shiftValue">Shift:</label>
                    <input type="number" id="shiftValue" min="1" max="94" value="3">
                </div>
                <div class="dropdown-item view-decrypted-btn" id="viewDecrypted">Lihat Pesan Asli</div>
                <div class="dropdown-item clear-btn" id="clearChat">Hapus Riwayat</div>
            </div>
        </div>
        <div class="menu-backdrop" id="menuBackdrop"></div>
    </div>
    
    <div class="chat-container" id="chatContainer">
        <?php
        if (!empty($_SESSION['perangkat2'])) {
            foreach ($_SESSION['perangkat2'] as $index => $message) {
                echo '<div class="message '.$message['type'].'" data-id="'.$index.'" data-original="'.htmlspecialchars($message['text']).'">';
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

    <!-- Password Modal -->
    <div class="password-modal" id="passwordModal">
        <div class="password-box">
            <h3>Masukkan Sandi</h3>
            <p>Sandi: "INDONESIA" (huruf kapital)</p>
            <input type="password" id="passwordInput" placeholder="Masukkan sandi">
            <button id="submitPassword">Submit</button>
            <button class="cancel" id="cancelPassword">Batal</button>
        </div>
    </div>

    <script src="js/uu.js"></script>
</body>
</html>