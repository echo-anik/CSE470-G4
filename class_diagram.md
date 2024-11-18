flowchart TD
    A[Christmas] -->|Get money| B(Go shopping)
classDiagram
    class User {
        +int userId
        +string username
        +string email
        +string password
        +createAccount()
        +manageAccount()
        +viewBookingHistory()
        +updateProfile()
    }

    class Booking {
        +int bookingId
        +date bookingDate
        +string status
        +double totalPrice
        +createBooking()
        +modifyBooking()
        +cancelBooking()
    }

    class Hotel {
        +int hotelId
        +string name
        +string location
        +double rating
        +listRooms()
        +checkAvailability()
    }

    class Room {
        +int roomId
        +string type
        +double price
        +int capacity
    }

    class Transport {
        +int transportId
        +string type
        +string source
        +string destination
        +date departureTime
        +double fare
        +checkAvailability()
    }

    class Tour {
        +int tourId
        +string name
        +string description
        +double price
        +date startDate
        +date endDate
        +listTourDetails()
    }

    class TourGuide {
        +int guideId
        +string name
        +string languages
        +bookGuide()
    }

    class Payment {
        +int paymentId
        +string method
        +double amount
        +date paymentDate
        +processPayment()
    }

    class Wishlist {
        +int wishlistId
        +addItem()
        +removeItem()
        +viewItems()
    }

    class Admin {
        +int adminId
        +manageUsers()
        +manageBookings()
        +updateListings()
        +viewStatistics()
    }

    User "1" -- "0..*" Booking : makes
    User "1" -- "0..1" Wishlist : has
    User "1" -- "0..*" Payment : performs
    
    Booking "1" -- "1" Hotel : includes
    Booking "1" -- "1" Transport : includes
    Booking "1" -- "0..1" Tour : optional
    
    Hotel "1" -- "0..*" Room : contains
    
    Transport "1" -- "0..1" Tour : associated
    
    Tour "0..1" -- "0..1" TourGuide : optional
    
    Admin "1" -- "0..*" Booking : manages
    Admin "1" -- "0..*" User : manages
