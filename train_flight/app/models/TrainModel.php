<?php
// app/models/TrainModel.php

require_once __DIR__ . '/../../config/database.php';

class TrainModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    // Retrieve all active trains
    public function getAllTrains() {
        $sql = "SELECT * FROM trains WHERE status = 'active' ORDER BY departure_date, departure_time ASC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Search trains based on criteria
    public function searchTrains($origin, $destination, $departure_date, $class = null) {
        $sql = "SELECT * FROM trains WHERE origin LIKE ? AND destination LIKE ? AND departure_date = ?";

        if ($class) {
            $sql .= " AND class = ?";
        }

        $stmt = $this->conn->prepare($sql);
        if ($class) {
            $stmt->bind_param("ssss", $origin, $destination, $departure_date, $class);
        } else {
            $stmt->bind_param("sss", $origin, $destination, $departure_date);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Retrieve a single train by ID
    public function getTrainById($train_id) {
        $sql = "SELECT * FROM trains WHERE id = ? AND status = 'active'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $train_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Update available seats
    public function updateAvailableSeats($train_id, $new_seat_count) {
        $sql = "UPDATE trains SET available_seats = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $new_seat_count, $train_id);
        return $stmt->execute();
    }

    // Create a new booking
    public function createBooking($user_id, $train_id, $departure_date, $class, $payment_method) {
        $sql = "INSERT INTO train_bookings (user_id, train_id, departure_date, class, payment_method, status) 
                VALUES (?, ?, ?, ?, ?, 'confirmed')";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iisss", $user_id, $train_id, $departure_date, $class, $payment_method);
        return $stmt->execute();
    }

    // Get bookings by user
    public function getBookingsByUser($user_id) {
        $sql = "SELECT b.*, t.train_number, t.origin, t.destination, t.departure_time, t.arrival_time, t.class, t.price 
                FROM train_bookings b 
                JOIN trains t ON b.train_id = t.id 
                WHERE b.user_id = ? 
                ORDER BY b.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>