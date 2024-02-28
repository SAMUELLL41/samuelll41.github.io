const express = require('express');
const bodyParser = require('body-parser');
const fs = require('fs');

const app = express();
const port = 3000;

app.use(bodyParser.json());
app.use(express.static('public'));

app.post('/send-message', (req, res) => {
    const { name, message } = req.body;

    if (name && message) {
        const data = `${name}: ${message}\n`;

        fs.appendFile('message.txt', data, (err) => {
            if (err) {
                console.error('Error writing to file:', err);
                res.json({ success: false });
            } else {
                res.json({ success: true });
            }
        });
    } else {
        res.json({ success: false });
    }
});

app.get('/get-messages', (req, res) => {
    fs.readFile('message.txt', 'utf8', (err, data) => {
        if (err) {
            console.error('Error reading file:', err);
            res.json({ messages: [] });
        } else {
            const messages = data.split('\n').filter(Boolean).map(line => {
                const [name, message] = line.split(':');
                return { name: name.trim(), message: message.trim() };
            });
            res.json({ messages });
        }
    });
});

app.use(express.static('public'));

app.listen(port, () => {
    console.log(`Server is running on http://localhost:${port}`);
});
