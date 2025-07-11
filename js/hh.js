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
        body: `device=1&text=${encodeURIComponent(messageInput.value)}&shift=${shiftValue}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            addMessageToChat(data.message, 'sent');
            messageInput.value = '';
        }
    });
});
        
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
        
function checkForNewMessages() {
    fetch('api.php?action=get&device=1')
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
                        messageElement.setAttribute('data-original', message.original || message.text);
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
        fetch('api.php?action=clear&device=1')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('chatContainer').innerHTML = '';
                }
            });
    }
});
        
setInterval(checkForNewMessages, 1000);
        
window.onload = function() {
    document.getElementById('chatContainer').scrollTop = 
        document.getElementById('chatContainer').scrollHeight;
};