<?php
// app/models/FlightModel.php

require_once __DIR__ . '/../../config/database.php';

class FlightModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    // Retrieve all active flights
    public function getAllFlights() {
        $sql = "SELECT * FROM flights WHERE status = 'active' ORDER BY departure_date, departure_time ASC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Search flights based on criteria
    public function searchFlights($origin, $destination, $departure_date, $class = null, $trip_type = null) {
        $sql = "SELECT * FROM flights WHERE origin LIKE ? AND destination LIKE ? AND departure_date = ?";

        if ($class) {
            $sql .= " AND class = ?";
        }

        if ($trip_type) {
            $sql .= " AND trip_type = ?";
        }

        $stmt = $this->conn->prepare($sql);
        $types = "";
        $params = [$origin, $destination, $departure_date];

        if ($class) {
            $types .= "s";
            $params[] = $class;
        }

        if ($trip_type) {
            $types .= "s";
            $params[] = $trip_type;
        }

        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Retrieve a single flight by ID
    public function getFlightById($flight_id) {
        $sql = "SELECT * FROM flights WHERE id = ? AND status = 'active'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $flight_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Update available seats
    public function updateAvailableSeats($flight_id, $new_seat_count) {
        $sql = "UPDATE flights SET available_seats = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $new_seat_count, $flight_id);
        return $stmt->execute();
    }

    // Create a new booking
    public function createBooking($user_id, $flight_id, $departure_date, $class, $payment_method, $trip_type) {
        $sql = "INSERT INTO flight_bookings (user_id, flight_id, departure_date, class, payment_method, trip_type, status) 
                VALUES (?, ?, ?, ?, ?, ?, 'confirmed')";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iissss", $user_id, $flight_id, $departure_date, $class, $payment_method, $trip_type);
        return $stmt->execute();
    }

    // Get bookings by user
    public function getBookingsByUser($user_id) {
        $sql = "SELECT b.*, f.flight_number, f.origin, f.destination, f.departure_time, f.arrival_time, f.class, f.price, f.trip_type 
                FROM flight_bookings b 
                JOIN flights f ON b.flight_id = f.id 
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