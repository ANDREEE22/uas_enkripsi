// perangkat 2      

// Menu toggle functionality
const menuBtn = document.getElementById('menuBtn');
const dropdownMenu = document.getElementById('dropdownMenu');
const menuBackdrop = document.getElementById('menuBackdrop');
        
menuBtn.addEventListener('click', function(e) {
    e.stopPropagation();
    dropdownMenu.classList.toggle('show');
    menuBackdrop.classList.toggle('show');
});
        
menuBackdrop.addEventListener('click', function() {
    dropdownMenu.classList.remove('show');
    menuBackdrop.classList.remove('show');
});
        
// Close menu when clicking outside
document.addEventListener('click', function(e) {
    if (!dropdownMenu.contains(e.target) && e.target !== menuBtn) {
        dropdownMenu.classList.remove('show');
        menuBackdrop.classList.remove('show');
    }
});

// Password protection for viewing decrypted messages
const passwordModal = document.getElementById('passwordModal');
const passwordInput = document.getElementById('passwordInput');
const submitPassword = document.getElementById('submitPassword');
const cancelPassword = document.getElementById('cancelPassword');
const CORRECT_PASSWORD = "INDONESIA";
let showDecrypted = false;
let passwordVerified = false;

document.getElementById('viewDecrypted').addEventListener('click', function(e) {
    e.preventDefault();
    dropdownMenu.classList.remove('show');
    menuBackdrop.classList.remove('show');
    
    if (!showDecrypted && !passwordVerified) {
        // Show password modal if trying to view decrypted for the first time
        passwordModal.classList.add('show');
        passwordInput.value = '';
        passwordInput.focus();
        return;
    }
    
    toggleDecryptedMessages();
});

submitPassword.addEventListener('click', function() {
    if (passwordInput.value === CORRECT_PASSWORD) {
        passwordVerified = true;
        passwordModal.classList.remove('show');
        toggleDecryptedMessages();
    } else {
        alert('Sandi salah! Silakan coba lagi.');
        passwordInput.value = '';
        passwordInput.focus();
    }
});

cancelPassword.addEventListener('click', function() {
    passwordModal.classList.remove('show');
});

passwordInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        submitPassword.click();
    }
});

function toggleDecryptedMessages() {
    showDecrypted = !showDecrypted;
    const viewBtn = document.getElementById('viewDecrypted');
    viewBtn.textContent = showDecrypted ? 'Lihat Pesan Terenkripsi' : 'Lihat Pesan Asli';
    
    const shiftValue = parseInt(document.getElementById('shiftValue').value);
    const messages = document.querySelectorAll('.message[data-original]');
    
    messages.forEach(message => {
        const originalText = message.getAttribute('data-original');
        const messageDiv = message.querySelector('div:first-child');
        
        if (showDecrypted) {
            // Tampilkan pesan terdekripsi
            const decryptedText = decryptText(originalText, shiftValue);
            messageDiv.textContent = decryptedText;
        } else {
            // Kembalikan ke pesan asli (terenkripsi)
            messageDiv.textContent = originalText;
            // Reset verifikasi password setiap kali kembali ke pesan terenkripsi
            passwordVerified = false;
        }
    });
}

// Fungsi untuk dekripsi teks
function decryptText(text, shift) {
    let result = '';
    shift = shift % 95;
    
    for (let i = 0; i < text.length; i++) {
        const char = text[i];
        const code = char.charCodeAt(0);
        
        if (code >= 32 && code <= 126) {
            const newCode = 32 + (code - 32 - shift + 95) % 95;
            result += String.fromCharCode(newCode);
        } else {
            result += char;
        }
    }
    
    return result;
}

// Fungsi untuk mengirim pesan dengan AJAX
document.getElementById('messageForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const messageInput = document.getElementById('messageInput');
    const shiftValue = document.getElementById('shiftValue').value;
    
    if (messageInput.value.trim() === '') return;
    
    // Kirim data ke server dengan path absolut
    fetch(window.location.pathname.replace('perangkat2.php', '') + 'api.php?action=send', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `device=2&text=${encodeURIComponent(messageInput.value)}&shift=${shiftValue}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Tambahkan pesan ke chat container
            addMessageToChat(data.message, 'sent');
            messageInput.value = '';
        }
    })
    .catch(error => console.error('Error:', error));
});
        
// Fungsi untuk menambahkan pesan ke tampilan
function addMessageToChat(message, type) {
    const chatContainer = document.getElementById('chatContainer');
    const now = new Date();
    const timeString = now.getHours().toString().padStart(2, '0') + ':' + 
                      now.getMinutes().toString().padStart(2, '0');
    
    const messageElement = document.createElement('div');
    messageElement.className = `message ${type}`;
    messageElement.setAttribute('data-original', message);
    messageElement.innerHTML = `
        <div>${message}</div>
        <div class="time">${timeString}</div>
    `;
    
    chatContainer.appendChild(messageElement);
    chatContainer.scrollTop = chatContainer.scrollHeight;
}
        
// Fungsi untuk memeriksa pesan baru
function checkForNewMessages() {
    fetch(window.location.pathname.replace('perangkat2.php', '') + 'api.php?action=get&device=2')
        .then(response => response.json())
        .then(data => {
            if (data.messages && data.messages.length > 0) {
                const chatContainer = document.getElementById('chatContainer');
                const existingMessages = chatContainer.querySelectorAll('.message[data-id]');
                const lastId = existingMessages.length > 0 ? 
                    parseInt(existingMessages[existingMessages.length - 1].getAttribute('data-id')) : -1;
                
                // Tambahkan hanya pesan baru
                data.messages.forEach((message, index) => {
                    if (index > lastId && message.type === 'received') {
                        const messageElement = document.createElement('div');
                        messageElement.className = `message ${message.type}`;
                        messageElement.setAttribute('data-id', index);
                        messageElement.setAttribute('data-original', message.text);
                        messageElement.innerHTML = `
                            <div>${message.text}</div>
                            <div class="time">${message.time}</div>
                        `;
                        chatContainer.appendChild(messageElement);
                    }
                });
                
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
        })
        .catch(error => console.error('Error:', error));
}
        
// Fungsi untuk menghapus riwayat chat
document.getElementById('clearChat').addEventListener('click', function(e) {
    e.preventDefault();
    dropdownMenu.classList.remove('show');
    menuBackdrop.classList.remove('show');
    
    if (confirm('Apakah Anda yakin ingin menghapus semua riwayat pesan?')) {
        fetch(window.location.pathname.replace('perangkat2.php', '') + 'api.php?action=clear&device=2')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('chatContainer').innerHTML = '';
                    showDecrypted = false;
                    passwordVerified = false;
                    document.getElementById('viewDecrypted').textContent = 'Lihat Pesan Asli';
                }
            })
            .catch(error => console.error('Error:', error));
    }
});
        
// Periksa pesan baru setiap 1 detik
setInterval(checkForNewMessages, 1000);
        
// Auto scroll ke bawah saat pertama kali load
window.onload = function() {
    document.getElementById('chatContainer').scrollTop = 
        document.getElementById('chatContainer').scrollHeight;
};