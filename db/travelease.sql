SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `travelease`;
USE `travelease`;

-- Users table with profile picture
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,  -- Increased for password hashing
    role VARCHAR(20) NOT NULL,
    profile_picture VARCHAR(255),
    contact_number VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive') DEFAULT 'active'
);

-- Admin table with enhanced security
CREATE TABLE admin (
    admin_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,  -- Increased for password hashing
    role VARCHAR(20) NOT NULL,
    profile_picture VARCHAR(255),
    last_login TIMESTAMP,
    status ENUM('active', 'inactive') DEFAULT 'active'
);

-- Transport (Parent table)
CREATE TABLE transport (
    transport_id INT PRIMARY KEY AUTO_INCREMENT,
    transport_type ENUM('Bus', 'Cab', 'Flight', 'Train') NOT NULL,
    class VARCHAR(50),
    schedule VARCHAR(100),
    date DATE,
    fare DECIMAL(10,2),
    locations VARCHAR(255),
    image VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active'
);

-- Trains table
CREATE TABLE trains (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transport_id INT,
    origin VARCHAR(100) NOT NULL,
    destination VARCHAR(100) NOT NULL,
    departure_time TIME NOT NULL,
    arrival_time TIME NOT NULL,
    departure_date DATE NOT NULL,
    class ENUM('Economy', 'Business', 'First') NOT NULL DEFAULT 'Economy',
    price DECIMAL(10,2) NOT NULL,
    available_seats INT NOT NULL DEFAULT 100,
    train_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive') DEFAULT 'active',
    FOREIGN KEY (transport_id) REFERENCES transport(transport_id)
);

-- Flights table
CREATE TABLE flights (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transport_id INT,
    origin VARCHAR(100) NOT NULL,
    destination VARCHAR(100) NOT NULL,
    departure_time TIME NOT NULL,
    arrival_time TIME NOT NULL,
    departure_date DATE NOT NULL,
    class ENUM('Economy', 'Business', 'First') NOT NULL DEFAULT 'Economy',
    trip_type ENUM('One-way', 'Round-trip') NOT NULL DEFAULT 'One-way',
    age INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    available_seats INT NOT NULL DEFAULT 150,
    flight_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive') DEFAULT 'active',
    FOREIGN KEY (transport_id) REFERENCES transport(transport_id)
);

-- Bus table
CREATE TABLE bus (
    bus_id INT PRIMARY KEY AUTO_INCREMENT,
    transport_id INT,
    FromCity VARCHAR(100) NOT NULL,
    ToCity VARCHAR(100) NOT NULL,
    Departure DATE NOT NULL,
    DepartureTime TIME NOT NULL,
    BusName VARCHAR(100) NOT NULL,
    Seat INT NOT NULL,
    Availability VARCHAR(20) NOT NULL,
    bus_image VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    FOREIGN KEY (transport_id) REFERENCES transport(transport_id)
);

-- Cabs table
CREATE TABLE cabs (
    cab_id INT PRIMARY KEY AUTO_INCREMENT,
    transport_id INT,
    FromLocation VARCHAR(255) NOT NULL,
    ToLocation VARCHAR(255) NOT NULL,
    DepartureDate DATE NOT NULL,
    PickupTime TIME NOT NULL,
    DropTime TIME NOT NULL,
    CabName VARCHAR(255) NOT NULL,
    Availability ENUM('Yes','No') NOT NULL,
    cab_image VARCHAR(255),
    price_per_km DECIMAL(10,2) NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    FOREIGN KEY (transport_id) REFERENCES transport(transport_id)
);

-- Guide Registration table
CREATE TABLE guide_registration (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    language_proficiency VARCHAR(255) NOT NULL,
    living_country VARCHAR(255) NOT NULL,
    living_city VARCHAR(255) NOT NULL,
    age INT NOT NULL,
    experience INT NOT NULL,
    role VARCHAR(50) NOT NULL,
    profile_picture VARCHAR(255),
    contact_number VARCHAR(20),
    description TEXT,
    hourly_rate DECIMAL(10,2),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Guide Availability
CREATE TABLE guide_availability (
    id INT AUTO_INCREMENT PRIMARY KEY,
    guide_id INT,
    available_from DATE,
    available_to DATE,
    status ENUM('available', 'booked', 'unavailable') DEFAULT 'available',
    FOREIGN KEY (guide_id) REFERENCES guide_registration(id)
);

-- Guide Completed Journey table
CREATE TABLE completed_journey (
    id INT AUTO_INCREMENT PRIMARY KEY,
    guide_id INT,
    journey_name VARCHAR(255),
    journey_details TEXT,
    journey_images TEXT,  -- Store multiple image URLs as JSON
    completion_date DATE,
    FOREIGN KEY (guide_id) REFERENCES guide_registration(id)
);

-- Guide Tour Package table
CREATE TABLE tour_packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    guide_id INT,
    name VARCHAR(255),
    email VARCHAR(255),
    language_proficiency VARCHAR(255),
    explore_country VARCHAR(255),
    explore_city VARCHAR(255),
    age INT,
    experience INT,
    role VARCHAR(255),
    price DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    other_details TEXT,
    package_image VARCHAR(255),
    max_participants INT,
    tour_duration INT,
    tour_type ENUM('private', 'group', 'custom'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    end_date DATE,
    status ENUM('active', 'inactive') DEFAULT 'active',
    FOREIGN KEY (guide_id) REFERENCES guide_registration(id)
);

-- Guide Package Cart table
CREATE TABLE guide_package_cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,  
    package_id INT,  
    number_of_participants INT,
    total_price DECIMAL(10, 2),
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (package_id) REFERENCES tour_packages(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Guide Review table
CREATE TABLE guide_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,  
    guide_id INT,
    journey_id INT, 
    rating INT CHECK (rating BETWEEN 1 AND 5), 
    comment TEXT,
    review_images TEXT,  -- Store multiple image URLs as JSON
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    FOREIGN KEY (journey_id) REFERENCES guide_package_cart(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (guide_id) REFERENCES guide_registration(id) ON DELETE CASCADE
);

-- Guide Needed table
CREATE TABLE guide_needed (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    country VARCHAR(255) NOT NULL, 
    city VARCHAR(255) NOT NULL,         
    role ENUM('TalkMate', 'TravelMate') NOT NULL,
    language_proficiency VARCHAR(255),       
    journey_date DATE NOT NULL,           
    return_date DATE NOT NULL,             
    travelers_number INT NOT NULL,         
    payment_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('open', 'assigned', 'completed') DEFAULT 'open',
    other_details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Guide Payments table
CREATE TABLE guide_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT,
    amount DECIMAL(10, 2),
    payment_status ENUM('pending', 'completed', 'refunded', 'failed'),
    payment_date TIMESTAMP,
    payment_method VARCHAR(50),
    transaction_id VARCHAR(255),
    FOREIGN KEY (booking_id) REFERENCES guide_package_cart(id)
);

-- Hotels table
CREATE TABLE hotels (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL,
    description TEXT,
    rating DECIMAL(3,1),
    hotel_image VARCHAR(255),
    amenities TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active'
);

-- Hotel Rooms table
CREATE TABLE hotel_rooms (
    id INT PRIMARY KEY AUTO_INCREMENT,
    hotel_id INT,
    room_type VARCHAR(50),
    price_per_night DECIMAL(10,2),
    capacity INT,
    room_image VARCHAR(255),
    availability_status ENUM('available', 'booked') DEFAULT 'available',
    FOREIGN KEY (hotel_id) REFERENCES hotels(id)
);

-- Hotel Booking table
CREATE TABLE hotel_booking (
    booking_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    hotel_id INT,
    room_id INT,
    check_in_date DATE NOT NULL,
    check_out_date DATE NOT NULL,
    total_price DECIMAL(10,2),
    booking_status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    payment_status ENUM('pending', 'paid', 'refunded') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (hotel_id) REFERENCES hotels(id),
    FOREIGN KEY (room_id) REFERENCES hotel_rooms(id)
);

-- Booking History table
CREATE TABLE booking_history (
    history_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    booking_type VARCHAR(50) NOT NULL,
    booking_id INT NOT NULL,
    total_amount DECIMAL(10,2),
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(20) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Travel Packages table
CREATE TABLE travel_packages (
    package_id INT PRIMARY KEY AUTO_INCREMENT,
    package_name VARCHAR(255) NOT NULL,
    description TEXT,
    package_image VARCHAR(255),
    total_price DECIMAL(10,2) NOT NULL,
    duration_days INT,
    start_date DATE,
    end_date DATE,
    max_participants INT,
    available_spots INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive') DEFAULT 'active'
);

-- Package Details table
CREATE TABLE package_details (
    detail_id INT PRIMARY KEY AUTO_INCREMENT,
    package_id INT,
    service_type ENUM('flight', 'train', 'bus', 'hotel', 'guide', 'cab') NOT NULL,
    service_id INT NOT NULL,
    service_date DATE,
    price DECIMAL(10,2),
    FOREIGN KEY (package_id) REFERENCES travel_packages(package_id) ON DELETE CASCADE
);

-- Package Bookings table
CREATE TABLE package_bookings (
    booking_id INT PRIMARY KEY AUTO_INCREMENT,
    package_id INT,
    user_id INT,
    number_of_participants INT,
    total_amount DECIMAL(10,2),
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    payment_status ENUM('pending', 'paid', 'refunded') DEFAULT 'pending',
    booking_status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    FOREIGN KEY (package_id) REFERENCES travel_packages(package_id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Admin Logs table
CREATE TABLE admin_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT,
    action VARCHAR(255),
    table_name VARCHAR(255),
    record_id INT,
    action_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admin(admin_id)
);

CREATE INDEX idx_user_email ON users(email);
CREATE INDEX idx_user_status ON users(status);
CREATE INDEX idx_user_name ON users(name);

CREATE INDEX idx_admin_email ON admin(email);
CREATE INDEX idx_admin_status ON admin(status);

CREATE INDEX idx_transport_type ON transport(transport_type);
CREATE INDEX idx_transport_status ON transport(status);

CREATE INDEX idx_train_route ON trains(origin, destination);
CREATE INDEX idx_train_date ON trains(departure_date);
CREATE INDEX idx_train_status ON trains(status);

CREATE INDEX idx_flight_route ON flights(origin, destination);
CREATE INDEX idx_flight_date ON flights(departure_date);
CREATE INDEX idx_flight_class ON flights(class);
CREATE INDEX idx_flight_status ON flights(status);

CREATE INDEX idx_bus_route ON bus(FromCity, ToCity);
CREATE INDEX idx_bus_departure ON bus(Departure);
CREATE INDEX idx_bus_status ON bus(status);

CREATE INDEX idx_cab_route ON cabs(FromLocation, ToLocation);
CREATE INDEX idx_cab_date ON cabs(DepartureDate);
CREATE INDEX idx_cab_status ON cabs(status);

CREATE INDEX idx_guide_email ON guide_registration(email);
CREATE INDEX idx_guide_language ON guide_registration(language_proficiency);
CREATE INDEX idx_guide_country ON guide_registration(living_country);
CREATE INDEX idx_guide_status ON guide_registration(status);

CREATE INDEX idx_guide_availability_status ON guide_availability(status);

CREATE INDEX idx_tour_package_guide ON tour_packages(guide_id);
CREATE INDEX idx_tour_package_country ON tour_packages(explore_country);
CREATE INDEX idx_tour_package_status ON tour_packages(status);

CREATE INDEX idx_package_cart_user ON guide_package_cart(user_id);
CREATE INDEX idx_package_cart_package ON guide_package_cart(package_id);
CREATE INDEX idx_package_cart_status ON guide_package_cart(status);

CREATE INDEX idx_guide_review_user ON guide_reviews(user_id);
CREATE INDEX idx_guide_review_guide ON guide_reviews(guide_id);
CREATE INDEX idx_guide_review_rating ON guide_reviews(rating);

CREATE INDEX idx_guide_needed_user ON guide_needed(user_id);
CREATE INDEX idx_guide_needed_country ON guide_needed(country);
CREATE INDEX idx_guide_needed_status ON guide_needed(status);

CREATE INDEX idx_hotel_location ON hotels(location);
CREATE INDEX idx_hotel_rating ON hotels(rating);
CREATE INDEX idx_hotel_status ON hotels(status);

CREATE INDEX idx_hotel_room_type ON hotel_rooms(room_type);
CREATE INDEX idx_hotel_room_availability ON hotel_rooms(availability_status);

CREATE INDEX idx_hotel_booking_user ON hotel_booking(user_id);
CREATE INDEX idx_hotel_booking_hotel ON hotel_booking(hotel_id);
CREATE INDEX idx_hotel_booking_status ON hotel_booking(booking_status);
CREATE INDEX idx_hotel_booking_dates ON hotel_booking(check_in_date, check_out_date);

CREATE INDEX idx_booking_history_user ON booking_history(user_id);
CREATE INDEX idx_booking_history_type ON booking_history(booking_type);
CREATE INDEX idx_booking_history_date ON booking_history(booking_date);

CREATE INDEX idx_travel_package_name ON travel_packages(package_name);
CREATE INDEX idx_travel_package_dates ON travel_packages(start_date, end_date);
CREATE INDEX idx_travel_package_status ON travel_packages(status);

CREATE INDEX idx_package_details_package ON package_details(package_id);
CREATE INDEX idx_package_details_service ON package_details(service_type);

CREATE INDEX idx_package_booking_user ON package_bookings(user_id);
CREATE INDEX idx_package_booking_package ON package_bookings(package_id);
CREATE INDEX idx_package_booking_status ON package_bookings(booking_status);

CREATE INDEX idx_admin_log_admin ON admin_logs(admin_id);
CREATE INDEX idx_admin_log_table ON admin_logs(table_name);
CREATE INDEX idx_admin_log_timestamp ON admin_logs(action_timestamp);


COMMIT;
