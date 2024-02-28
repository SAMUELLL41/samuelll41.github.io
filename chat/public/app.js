document.addEventListener("DOMContentLoaded", function () {
    loadMessages();
});

function sendMessage() {
    // Your existing code...

    fetch('/send-message', {  // Update the URL to match your server
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
}

function loadMessages() {
    fetch('/get-messages')  // Update the URL to match your server
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
