<?php
require_once 'includes/db.php';

$flight_id = $_GET['flight_id'] ?? 0;

// Get flight details with city names
$query = "SELECT f.*, 
          c1.name as origin_city, c1.airport_code as origin_code,
          c2.name as destination_city, c2.airport_code as destination_code
          FROM flights f 
          JOIN cities c1 ON f.origin_city_id = c1.id 
          JOIN cities c2 ON f.destination_city_id = c2.id 
          WHERE f.id = :flight_id";
$stmt = $pdo->prepare($query);
$stmt->execute(['flight_id' => $flight_id]);
$flight = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$flight) {
    header('Location: index.html');
    exit;
}

// Get booked seats for this flight
$query = "SELECT seat_number FROM reservations WHERE flight_id = :flight_id";
$stmt = $pdo->prepare($query);
$stmt->execute(['flight_id' => $flight_id]);
$booked_seats = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Seats - Indian Airways</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .seat {
            width: 40px;
            height: 40px;
            margin: 5px;
            display: inline-block;
            text-align: center;
            line-height: 40px;
            border: 1px solid #ccc;
            cursor: pointer;
        }
        .seat.selected {
            background-color: #007bff;
            color: white;
        }
        .seat.occupied {
            background-color: #dc3545;
            color: white;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="container">
            <div class="row align-items-center py-3">
                <div class="col-md-6">
                    <h1 class="logo-text">Indian <span>Airways</span></h1>
                </div>
                <div class="col-md-6 text-end">
                    <a href="login.php" class="btn btn-outline-primary me-2">Login</a>
                    <a href="register.php" class="btn btn-primary">Register</a>
                </div>
            </div>
        </div>
    </header>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Select Your Seat</h1>
        
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($flight['airline']); ?></h5>
                        <p class="card-text">
                            <strong>From:</strong> <?php echo htmlspecialchars($flight['origin_city']); ?> (<?php echo htmlspecialchars($flight['origin_code']); ?>)<br>
                            <strong>To:</strong> <?php echo htmlspecialchars($flight['destination_city']); ?> (<?php echo htmlspecialchars($flight['destination_code']); ?>)<br>
                            <strong>Date:</strong> <?php echo htmlspecialchars($flight['flight_date']); ?><br>
                            <strong>Time:</strong> <?php echo htmlspecialchars($flight['flight_time']); ?><br>
                            <strong>Class:</strong> <?php echo htmlspecialchars($flight['flight_class']); ?><br>
                            <strong>Price:</strong> ₹<?php echo number_format($flight['price'], 2); ?><br>
                            <strong>Available Seats:</strong> <?php echo htmlspecialchars($flight['seats_available']); ?>
                        </p>
                        
                        <div class="seat-map mb-4">
                            <?php for ($i = 1; $i <= $flight['seats_total']; $i++): ?>
                                <div class="seat <?php echo in_array($i, $booked_seats) ? 'occupied' : ''; ?>" 
                                     data-seat="<?php echo $i; ?>"><?php echo $i; ?></div>
                            <?php endfor; ?>
                        </div>
                        
                        <form action="reservation.php" method="POST">
                            <input type="hidden" name="flight_id" value="<?php echo $flight_id; ?>">
                            <input type="hidden" name="seat_number" id="seat_number">
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary" id="book-button" disabled>Book Seat</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.seat').click(function() {
                if (!$(this).hasClass('occupied')) {
                    $('.seat').removeClass('selected');
                    $(this).addClass('selected');
                    $('#seat_number').val($(this).data('seat'));
                    $('#book-button').prop('disabled', false);
                }
            });
        });
    </script>
</body>
</html> 