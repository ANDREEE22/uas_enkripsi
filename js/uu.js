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
        
document.addEventListener('click', function(e) {
    if (!dropdownMenu.contains(e.target) && e.target !== menuBtn) {
        dropdownMenu.classList.remove('show');
        menuBackdrop.classList.remove('show');
    }
});

// Password protection
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
    
    const messages = document.querySelectorAll('.message');
    
    messages.forEach(message => {
        const originalText = message.getAttribute('data-original');
        const currentText = message.querySelector('div:first-child').textContent;
        const messageDiv = message.querySelector('div:first-child');
        
        if (showDecrypted) {
            // Jika originalText ada, gunakan itu, jika tidak gunakan currentText
            messageDiv.textContent = originalText || currentText;
        } else {
            // Kembalikan ke teks yang ada di database (terenkripsi untuk received)
            if (message.classList.contains('received')) {
                const encryptedText = message.getAttribute('data-encrypted') || currentText;
                messageDiv.textContent = encryptedText;
            } else {
                messageDiv.textContent = currentText;
            }
            passwordVerified = false;
        }
    });
}

document.getElementById('messageForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const messageInput = document.getElementById('messageInput');
    const shiftValue = document.getElementById('shiftValue').value;
    
    if (messageInput.value.trim() === '') return;
    
    fetch('api.php?action=send', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `device=2&text=${encodeURIComponent(messageInput.value)}&shift=${shiftValue}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            addMessageToChat(data.message, 'sent', data.original);
            messageInput.value = '';
        }
    });
});
        
function addMessageToChat(message, type, original) {
    const chatContainer = document.getElementById('chatContainer');
    const now = new Date();
    const timeString = now.getHours().toString().padStart(2, '0') + ':' + 
                      now.getMinutes().toString().padStart(2, '0');
    
    const messageElement = document.createElement('div');
    messageElement.className = `message ${type}`;
    messageElement.setAttribute('data-original', original || message);
    if (type === 'received') {
        messageElement.setAttribute('data-encrypted', message);
    }
    messageElement.innerHTML = `
        <div>${message}</div>
        <div class="time">${timeString}</div>
    `;
    
    chatContainer.appendChild(messageElement);
    chatContainer.scrollTop = chatContainer.scrollHeight;
}
        
function checkForNewMessages() {
    fetch('api.php?action=get&device=2')
        .then(response => response.json())
        .then(data => {
            if (data.messages && data.messages.length > 0) {
                const chatContainer = document.getElementById('chatContainer');
                const existingMessages = chatContainer.querySelectorAll('.message[data-id]');
                const lastId = existingMessages.length > 0 ? 
                    parseInt(existingMessages[existingMessages.length - 1].getAttribute('data-id')) : -1;
                
                data.messages.forEach((message, index) => {
                    if (index > lastId) {
                        const messageElement = document.createElement('div');
                        messageElement.className = `message ${message.type}`;
                        messageElement.setAttribute('data-id', index);
                        messageElement.setAttribute('data-original', message.original);
                        if (message.type === 'received') {
                            messageElement.setAttribute('data-encrypted', message.text);
                        }
                        messageElement.innerHTML = `
                            <div>${message.text}</div>
                            <div class="time">${message.time}</div>
                        `;
                        chatContainer.appendChild(messageElement);
                    }
                });
                
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
        });
}
        
document.getElementById('clearChat').addEventListener('click', function(e) {
    e.preventDefault();
    dropdownMenu.classList.remove('show');
    menuBackdrop.classList.remove('show');
    
    if (confirm('Apakah Anda yakin ingin menghapus semua riwayat pesan?')) {
        fetch('api.php?action=clear&device=2')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('chatContainer').innerHTML = '';
                    showDecrypted = false;
                    passwordVerified = false;
                    document.getElementById('viewDecrypted').textContent = 'Lihat Pesan Asli';
                }
            });
    }
});
        
setInterval(checkForNewMessages, 1000);
        
window.onload = function() {
    document.getElementById('chatContainer').scrollTop = 
        document.getElementById('chatContainer').scrollHeight;
};