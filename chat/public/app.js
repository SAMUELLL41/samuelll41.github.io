document.addEventListener("DOMContentLoaded", function() {
    loadMessages();
});

function sendMessage() {
    const name = document.getElementById("name").value;
    const message = document.getElementById("message").value;

    if (name && message) {
        const data = { name, message };

        fetch('/send-message', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data),
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                loadMessages();
                document.getElementById("message").value = "";
            } else {
                alert("Failed to send message.");
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    } else {
        alert("Please enter your name and message.");
    }
}

function loadMessages() {
    fetch('/get-messages')
        .then(response => response.json())
        .then(data => {
            const messagesContainer = document.getElementById("messages");
            messagesContainer.innerHTML = "";

            data.messages.forEach(msg => {
                const messageElement = document.createElement("div");
                messageElement.innerHTML = `<strong>${msg.name}:</strong> ${msg.message}`;
                messagesContainer.appendChild(messageElement);
            });
        })
        .catch(error => {
            console.error('Error:', error);
        });
}
