<?php
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if user is logged in
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $flight_id = $_POST['flight_id'] ?? 0;
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $seat_number = $_POST['seat_number'] ?? '';

    if ($flight_id && $name && $email && $seat_number) {
        try {
            // Start transaction
            $pdo->beginTransaction();

            // Check if seat is already booked
            $check_query = "SELECT id FROM reservations WHERE flight_id = :flight_id AND seat_number = :seat_number";
            $check_stmt = $pdo->prepare($check_query);
            $check_stmt->execute(['flight_id' => $flight_id, 'seat_number' => $seat_number]);
            
            if ($check_stmt->rowCount() > 0) {
                throw new Exception("This seat is already booked. Please select another seat.");
            }

            // Get flight details
            $flight_query = "SELECT * FROM flights WHERE id = :flight_id AND seats_available > 0";
            $flight_stmt = $pdo->prepare($flight_query);
            $flight_stmt->execute(['flight_id' => $flight_id]);
            $flight = $flight_stmt->fetch(PDO::FETCH_ASSOC);

            if (!$flight) {
                throw new Exception("Flight is no longer available.");
            }

            // Insert reservation with user_id
            $query = "INSERT INTO reservations (user_id, flight_id, name, email, seat_number, flight_class, concession_type) 
                     VALUES (:user_id, :flight_id, :name, :email, :seat_number, :flight_class, 'None')";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                'user_id' => $user_id,
                'flight_id' => $flight_id,
                'name' => $name,
                'email' => $email,
                'seat_number' => $seat_number,
                'flight_class' => $flight['flight_class']
            ]);

            // Update available seats
            $update_query = "UPDATE flights SET seats_available = seats_available - 1 WHERE id = :flight_id";
            $update_stmt = $pdo->prepare($update_query);
            $update_stmt->execute(['flight_id' => $flight_id]);

            // Commit transaction
            $pdo->commit();
            $success = true;
            // Store booking details in session for payment page
            $_SESSION['booking_details'] = [
                'name' => $name,
                'email' => $email,
                'seat_number' => $seat_number,
                'flight_id' => $flight_id,
                'flight_class' => $flight['flight_class'],
                'price' => $flight['price']
            ];
            header('Location: payment.php');
            exit;
        } catch (Exception $e) {
            // Rollback transaction on error
            $pdo->rollBack();
            $error = $e->getMessage();
        }
    } else {
        $error = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation - Indian Airways</title>
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
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <?php if (isset($success)): ?>
                            <div class="alert alert-success">
                                <h4 class="alert-heading">Thank you for booking!</h4>
                                <p>Your reservation has been confirmed. You will receive a confirmation email shortly.</p>
                                <hr>
                                <p class="mb-0">
                                    <strong>Booking Details:</strong><br>
                                    Name: <?php echo htmlspecialchars($name); ?><br>
                                    Email: <?php echo htmlspecialchars($email); ?><br>
                                    Seat Number: <?php echo htmlspecialchars($seat_number); ?>
                                </p>
                            </div>
                        <?php elseif (isset($error)): ?>
                            <div class="alert alert-danger">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="text-center mt-4">
                            <a href="index.html" class="btn btn-primary">Back to Home</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 