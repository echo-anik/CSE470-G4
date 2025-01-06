<?php
// app/controllers/HistoryController.php

require_once __DIR__ . '/../models/TrainModel.php';
require_once __DIR__ . '/../models/FlightModel.php';

class HistoryController {
    private $trainModel;
    private $flightModel;

    public function __construct() {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Ensure user is authenticated
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['message'] = 'Please log in to view your booking history.';
            $_SESSION['message_type'] = 'error';
            header('Location: index.php?controller=Home&action=index');
            exit();
        }

        $this->trainModel = new TrainModel();
        $this->flightModel = new FlightModel();
    }

    // Display booking history
    public function index() {
        $user_id = $_SESSION['user_id'];

        // Fetch Train and Flight bookings
        $train_bookings = $this->trainModel->getBookingsByUser($user_id);
        $flight_bookings = $this->flightModel->getBookingsByUser($user_id);

        include __DIR__ . '/../views/history/history.php';
    }
}
?>
