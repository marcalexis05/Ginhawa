<?php
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
require dirname(__DIR__) . '/vendor/autoload.php';

class Chat implements MessageComponentInterface {
    private $clients;
    private $connections;
    private $database;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->connections = [];
        // Initialize database connection
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "edoc";
        $this->database = new mysqli($servername, $username, $password, $dbname);
        if ($this->database->connect_error) {
            die("Connection failed: " . $this->database->connect_error);
        }
        // Enable error reporting
        $this->database->report_mode = MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        $query = $conn->httpRequest->getUri()->getQuery();
        parse_str($query, $params);
        $userId = $params['userId'] ?? null;
        $userType = $params['userType'] ?? null;

        if ($userId && $userType) {
            $this->connections[$userType . '_' . $userId] = $conn;
            echo "New connection: {$userType}_{$userId} ({$conn->resourceId})\n";
        }
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
        if (!$data || !isset($data['senderId'], $data['receiverId'], $data['message'], $data['senderType'])) {
            echo "Invalid message format: $msg\n";
            return;
        }

        $senderId = (int)$data['senderId'];
        $receiverId = (int)$data['receiverId'];
        $message = $this->database->real_escape_string($data['message']);
        $senderType = $data['senderType'];

        // Log the message being processed
        echo "Processing message: senderId=$senderId, receiverId=$receiverId, message=$message, senderType=$senderType\n";

        // Store message in database
        try {
            $stmt = $this->database->prepare("INSERT INTO chat_messages (sender_id, receiver_id, message, sender_type) VALUES (?, ?, ?, ?)");
            if ($stmt === false) {
                echo "Prepare failed: " . $this->database->error . "\n";
                return;
            }
            $stmt->bind_param("iiss", $senderId, $receiverId, $message, $senderType);
            if (!$stmt->execute()) {
                echo "Execute failed: " . $stmt->error . "\n";
            } else {
                echo "Message saved successfully\n";
            }
            $stmt->close();
        } catch (Exception $e) {
            echo "Database error: " . $e->getMessage() . "\n";
        }

        $timestamp = date('Y-m-d H:i:s');
        $response = json_encode([
            'senderId' => $senderId,
            'receiverId' => $receiverId,
            'message' => $message,
            'timestamp' => $timestamp,
            'senderType' => $senderType
        ]);

        // Send to receiver
        $receiverKey = ($senderType === 'patient' ? 'doctor_' : 'patient_') . $receiverId;
        if (isset($this->connections[$receiverKey])) {
            $this->connections[$receiverKey]->send($response);
            echo "Message sent to $receiverKey\n";
        } else {
            echo "Receiver ($receiverKey) not connected\n";
        }

        // Echo back to sender
        $from->send($response);
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        foreach ($this->connections as $key => $connection) {
            if ($connection === $conn) {
                unset($this->connections[$key]);
                echo "Connection closed: $key ({$conn->resourceId})\n";
                break;
            }
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }
}

$server = \Ratchet\Server\IoServer::factory(
    new \Ratchet\Http\HttpServer(
        new \Ratchet\WebSocket\WsServer(
            new Chat()
        )
    ),
    8080
);

$server->run();