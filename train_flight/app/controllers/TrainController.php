<?php
// app/controllers/TrainController.php

require_once __DIR__ . '/../models/TrainModel.php';

class TrainController {
    private $trainModel;

    public function __construct() {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Ensure user is authenticated
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['message'] = 'Please log in to access Train services.';
            $_SESSION['message_type'] = 'error';
            header('Location: index.php?controller=Home&action=index');
            exit();
        }

        $this->trainModel = new TrainModel();
    }

    /**
     * Display the train search form.
     */
    public function index() {
        include __DIR__ . '/../views/trains/search_form.php';
    }

    /**
     * Handle the train search request.
     */
    public function search() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $_SESSION['message'] = 'Invalid request method.';
            $_SESSION['message_type'] = 'error';
            header('Location: index.php?controller=Train&action=index');
            exit();
        }

        // Validate CSRF Token
        if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['message'] = 'Invalid CSRF token.';
            $_SESSION['message_type'] = 'error';
            header('Location: index.php?controller=Train&action=index');
            exit();
        }

        // Check if required fields are set
        if (!isset($_GET['origin'], $_GET['destination'], $_GET['departure_date'], $_GET['departure_time'], $_GET['class'], $_GET['age'])) {
            $_SESSION['message'] = 'Please fill in all search fields.';
            $_SESSION['message_type'] = 'error';
            header('Location: index.php?controller=Train&action=index');
            exit();
        }

        // Sanitize and retrieve inputs
        $origin = htmlspecialchars(trim($_GET['origin']));
        $destination = htmlspecialchars(trim($_GET['destination']));
        $departure_date = htmlspecialchars(trim($_GET['departure_date']));
        $departure_time = htmlspecialchars(trim($_GET['departure_time']));
        $class = htmlspecialchars(trim($_GET['class']));
        $age = htmlspecialchars(trim($_GET['age']));

        // Optionally, you can add more validation here (e.g., date format, time format)

        // Perform the search
        $trains = $this->trainModel->searchTrains($origin, $destination, $departure_date, $departure_time, $class, $age);

        // Load the search results view
        include __DIR__ . '/../views/trains/search_results.php';
    }

    /**
     * Display the booking form for a selected train.
     */
    public function book() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $_SESSION['message'] = 'Invalid request method.';
            $_SESSION['message_type'] = 'error';
            header('Location: index.php?controller=Train&action=index');
            exit();
        }

        // Validate CSRF Token
        if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['message'] = 'Invalid CSRF token.';
            $_SESSION['message_type'] = 'error';
            header('Location: index.php?controller=Train&action=index');
            exit();
        }

        // Check if train ID is provided
        if (!isset($_GET['id'])) {
            $_SESSION['message'] = 'Invalid train selection.';
            $_SESSION['message_type'] = 'error';
            header('Location: index.php?controller=Train&action=index');
            exit();
        }

        $train_id = intval($_GET['id']);
        $train = $this->trainModel->getTrainById($train_id);

        if (!$train) {
            $_SESSION['message'] = 'Train not found.';
            $_SESSION['message_type'] = 'error';
            header('Location: index.php?controller=Train&action=index');
            exit();
        }

        include __DIR__ . '/../views/trains/booking.php';
    }

    /**
     * Handle the booking confirmation and redirect to payment.
     */
    public function confirmBooking() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['message'] = 'Invalid request method.';
            $_SESSION['message_type'] = 'error';
            header('Location: index.php?controller=Train&action=index');
            exit();
        }

        // Validate CSRF Token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['message'] = 'Invalid CSRF token.';
            $_SESSION['message_type'] = 'error';
            header('Location: index.php?controller=Train&action=index');
            exit();
        }

        // Check if all required POST data is set
        $required_fields = ['train_id', 'departure_date', 'class', 'payment_method', 'age'];
        foreach ($required_fields as $field) {
            if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
                $_SESSION['message'] = 'Please fill in all booking fields.';
                $_SESSION['message_type'] = 'error';
                header('Location: index.php?controller=Train&action=book&id=' . intval($_POST['train_id']));
                exit();
            }
        }

        // Sanitize and retrieve inputs
        $train_id = intval($_POST['train_id']);
        $departure_date = htmlspecialchars(trim($_POST['departure_date']));
        $class = htmlspecialchars(trim($_POST['class']));
        $payment_method = htmlspecialchars(trim($_POST['payment_method']));
        $age = htmlspecialchars(trim($_POST['age']));

        // Additional validation can be performed here (e.g., check date is not past)

        // Fetch train details
        $train = $this->trainModel->getTrainById($train_id);
        if (!$train) {
            $_SESSION['message'] = 'Selected train does not exist.';
            $_SESSION['message_type'] = 'error';
            header('Location: index.php?controller=Train&action=index');
            exit();
        }

        // Check seat availability
        if ($train['available_seats'] <= 0) {
            $_SESSION['message'] = 'No available seats on the selected train.';
            $_SESSION['message_type'] = 'error';
            header('Location: index.php?controller=Train&action=index');
            exit();
        }

        // TODO: Integrate with actual payment processing here
        // For now, we'll simulate a successful payment

        $payment_success = true; // Replace with real payment status

        if ($payment_success) {
            // Create the booking
            $user_id = $_SESSION['user_id'];
            $booking_created = $this->trainModel->createBooking($user_id, $train_id, $departure_date, $class, $payment_method, $age);

            if ($booking_created) {
                // Update available seats
                $this->trainModel->updateAvailableSeats($train_id, $train['available_seats'] - 1);

                $_SESSION['message'] = 'Booking confirmed! Redirecting to payment page.';
                $_SESSION['message_type'] = 'success';

                // Redirect to payment page (replace 'payment.php' with actual payment processing page)
                header('Location: payment.php');
                exit();
            } else {
                $_SESSION['message'] = 'Failed to create booking. Please try again.';
                $_SESSION['message_type'] = 'error';
                header('Location: index.php?controller=Train&action=book&id=' . $train_id);
                exit();
            }
        } else {
            $_SESSION['message'] = 'Payment failed. Please try again.';
            $_SESSION['message_type'] = 'error';
            header('Location: index.php?controller=Train&action=book&id=' . $train_id);
            exit();
        }
    }

    /**
     * Display the user's booking history for trains.
     */
    public function bookingHistory() {
        $user_id = $_SESSION['user_id'];
        $bookings = $this->trainModel->getBookingsByUser($user_id);
        include __DIR__ . '/../views/trains/booking_history.php';
    }

    /**
     * (Optional) Add a train booking to the wishlist.
     */
    public function addToWishlist() {
        // Implement wishlist functionality as needed
        // This can involve interacting with a WishlistModel and updating the database
        // For now, we'll simulate adding to wishlist

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $_SESSION['message'] = 'Invalid request method.';
            $_SESSION['message_type'] = 'error';
            header('Location: index.php?controller=Train&action=index');
            exit();
        }

        // Validate CSRF Token
        if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['message'] = 'Invalid CSRF token.';
            $_SESSION['message_type'] = 'error';
            header('Location: index.php?controller=Train&action=index');
            exit();
        }

        // Check if train ID is provided
        if (!isset($_GET['id'])) {
            $_SESSION['message'] = 'Invalid train selection.';
            $_SESSION['message_type'] = 'error';
            header('Location: index.php?controller=Train&action=index');
            exit();
        }

        $train_id = intval($_GET['id']);
        $user_id = $_SESSION['user_id'];

        // TODO: Implement actual wishlist logic here
        // For demonstration, we'll assume it's successful

        $_SESSION['message'] = 'Train added to your wishlist successfully!';
        $_SESSION['message_type'] = 'success';
        header('Location: index.php?controller=Train&action=index');
        exit();
    }
}
?>
