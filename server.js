const express = require('express');
const http = require('http');
const socketIo = require('socket.io');
const mysql = require('mysql2/promise');

const app = express();
const server = http.createServer(app);
const io = socketIo(server);

// MySQL connection
const db = mysql.createPool({
    host: 'localhost',
    user: 'root', // Replace with your MySQL username
    password: '', // Replace with your MySQL password
    database: 'your_database_name' // Replace with your database name
});

// Middleware
app.use(express.json());

// API to get all doctors
app.get('/api/doctors', async (req, res) => {
    try {
        const [rows] = await db.query('SELECT docid, docname FROM doctor');
        res.json(rows);
    } catch (error) {
        console.error('Error fetching doctors:', error);
        res.status(500).json({ error: 'Internal server error' });
    }
});

// API to get all patients
app.get('/api/patients', async (req, res) => {
    try {
        const [rows] = await db.query('SELECT pid, pname FROM patient');
        res.json(rows);
    } catch (error) {
        console.error('Error fetching patients:', error);
        res.status(500).json({ error: 'Internal server error' });
    }
});

// API to get chat history between two users
app.get('/api/chat/:userId/:otherUserId', async (req, res) => {
    const { userId, otherUserId } = req.params;
    try {
        const [rows] = await db.query(
            'SELECT * FROM chat_messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY timestamp ASC',
            [userId, otherUserId, otherUserId, userId]
        );
        res.json(rows);
    } catch (error) {
        console.error('Error fetching chat history:', error);
        res.status(500).json({ error: 'Internal server error' });
    }
});

// Socket.IO connection
io.on('connection', (socket) => {
    console.log('A user connected:', socket.id);

    socket.on('join', (userId) => {
        socket.join(userId);
        console.log(`${userId} joined their room`);
    });

    socket.on('sendMessage', async (data) => {
        const { senderId, receiverId, message } = data;
        try {
            const [result] = await db.query(
                'INSERT INTO chat_messages (sender_id, receiver_id, message, timestamp) VALUES (?, ?, ?, NOW())',
                [senderId, receiverId, message]
            );
            const msg = { sender_id: senderId, receiver_id: receiverId, message, id: result.insertId };
            io.to(senderId).emit('receiveMessage', msg);
            io.to(receiverId).emit('receiveMessage', msg);
        } catch (error) {
            console.error('Error saving message:', error);
        }
    });

    socket.on('disconnect', () => {
        console.log('User disconnected:', socket.id);
    });
});

// Start server
const PORT = 3000;
server.listen(PORT, () => {
    console.log(`Server running on port ${PORT}`);
});