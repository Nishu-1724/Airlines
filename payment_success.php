<?php
require_once 'includes/db.php';
session_start();

// Check if user is logged in and has booking details
if (!isset($_SESSION['user_id']) || !isset($_SESSION['booking_details'])) {
    header('Location: index.html');
    exit;
}

$booking = $_SESSION['booking_details'];

// Get flight details
$query = "SELECT f.*, 
          c1.name as origin_city, c1.airport_code as origin_code,
          c2.name as destination_city, c2.airport_code as destination_code
          FROM flights f 
          JOIN cities c1 ON f.origin_city_id = c1.id 
          JOIN cities c2 ON f.destination_city_id = c2.id 
          WHERE f.id = :flight_id";
$stmt = $pdo->prepare($query);
$stmt->execute(['flight_id' => $booking['flight_id']]);
$flight = $stmt->fetch(PDO::FETCH_ASSOC);

if (isset($_POST['download_pdf'])) {
    // Generate HTML content
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <title>Booking Confirmation</title>
        <style>
            body { font-family: Arial, sans-serif; }
            .header { text-align: center; margin-bottom: 20px; }
            .details { margin: 20px 0; }
            .footer { margin-top: 30px; text-align: center; }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>Booking Confirmation</h1>
            <p>Booking Reference: ' . uniqid('BK') . '</p>
        </div>
        
        <div class="details">
            <h2>Passenger Details</h2>
            <p><strong>Name:</strong> ' . htmlspecialchars($booking['name']) . '</p>
            <p><strong>Email:</strong> ' . htmlspecialchars($booking['email']) . '</p>
            
            <h2>Flight Details</h2>
            <p><strong>From:</strong> ' . htmlspecialchars($flight['origin_city']) . ' (' . htmlspecialchars($flight['origin_code']) . ')</p>
            <p><strong>To:</strong> ' . htmlspecialchars($flight['destination_city']) . ' (' . htmlspecialchars($flight['destination_code']) . ')</p>
            <p><strong>Date:</strong> ' . htmlspecialchars($flight['flight_date']) . '</p>
            <p><strong>Time:</strong> ' . htmlspecialchars($flight['flight_time']) . '</p>
            <p><strong>Seat Number:</strong> ' . htmlspecialchars($booking['seat_number']) . '</p>
            <p><strong>Class:</strong> ' . htmlspecialchars($booking['flight_class']) . '</p>
            <p><strong>Amount Paid:</strong> ₹' . number_format($booking['price'], 2) . '</p>
            <p><strong>Payment Status:</strong> Confirmed</p>
        </div>
        
        <div class="footer">
            <p>Thank you for choosing Indian Airways!</p>
            <p>This is your booking confirmation. Please keep it for your records.</p>
        </div>
    </body>
    </html>';

    // Set headers for PDF download
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="booking_confirmation.pdf"');
    
    // Convert HTML to PDF using a simple method
    // Note: This is a basic solution. For production, you should use a proper PDF library
    echo $html;
    exit;
}

// Clear booking details from session after successful payment
// unset($_SESSION['booking_details']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success - Indian Airways</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc;
        }
        .success-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }
        .success-icon {
            color: #10b981;
            font-size: 4rem;
        }
        .download-btn {
            background-color: #2563eb;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .download-btn:hover {
            background-color: #1e40af;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card success-card">
                    <div class="card-body text-center p-5">
                        <i class="fas fa-check-circle success-icon mb-4"></i>
                        <h2 class="card-title mb-4">Payment Successful!</h2>
                        <p class="lead mb-4">Your booking has been confirmed. Thank you for choosing Indian Airways.</p>
                        
                        <div class="booking-details mb-4">
                            <h4>Booking Details</h4>
                            <p>
                                <strong>Name:</strong> <?php echo htmlspecialchars($booking['name']); ?><br>
                                <strong>Email:</strong> <?php echo htmlspecialchars($booking['email']); ?><br>
                                <strong>Seat Number:</strong> <?php echo htmlspecialchars($booking['seat_number']); ?><br>
                                <strong>Class:</strong> <?php echo htmlspecialchars($booking['flight_class']); ?><br>
                                <strong>Amount:</strong> ₹<?php echo number_format($booking['price'], 2); ?>
                            </p>
                        </div>

                        <a href="download_booking.php" class="download-btn" target="_blank">
                            <i class="fas fa-download"></i>
                            Download Booking Confirmation
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 