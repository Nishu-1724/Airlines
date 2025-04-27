<?php
require_once 'includes/db.php';

// Get all available flights
$query = "SELECT f.*, c1.name as origin_city, c2.name as destination_city 
          FROM flights f 
          JOIN cities c1 ON f.origin_city_id = c1.id 
          JOIN cities c2 ON f.destination_city_id = c2.id 
          WHERE f.seats_available > 0 
          ORDER BY f.flight_date, f.flight_time";
$stmt = $pdo->prepare($query);
$stmt->execute();
$flights = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Flights - Air India</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="container">
            <div class="row align-items-center py-3">
                <div class="col-md-6">
                    <img src="assets/images/air-india-logo.png" alt="Air India Logo" height="50">
                </div>
                <div class="col-md-6 text-end">
                    <a href="login.php" class="btn btn-outline-primary me-2">Login</a>
                    <a href="register.php" class="btn btn-primary">Register</a>
                </div>
            </div>
        </div>
    </header>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Available Flights</h1>
        
        <?php if (empty($flights)): ?>
            <div class="alert alert-info">No flights available at the moment.</div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($flights as $flight): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($flight['airline']); ?></h5>
                                <p class="card-text">
                                    <strong>From:</strong> <?php echo htmlspecialchars($flight['origin_city']); ?><br>
                                    <strong>To:</strong> <?php echo htmlspecialchars($flight['destination_city']); ?><br>
                                    <strong>Date:</strong> <?php echo htmlspecialchars($flight['flight_date']); ?><br>
                                    <strong>Time:</strong> <?php echo htmlspecialchars($flight['flight_time']); ?><br>
                                    <strong>Class:</strong> <?php echo htmlspecialchars($flight['flight_class']); ?><br>
                                    <strong>Available Seats:</strong> <?php echo htmlspecialchars($flight['seats_available']); ?><br>
                                    <strong>Price:</strong> ₹<?php echo number_format($flight['price'], 2); ?>
                                </p>
                                <a href="seat_selection.php?flight_id=<?php echo $flight['id']; ?>" class="btn btn-primary">Select Seats</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div class="text-center mt-4">
            <a href="index.html" class="btn btn-secondary">Back to Search</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 