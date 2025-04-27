<?php
require_once 'includes/db.php';
session_start();

// Check if user is logged in and has booking details
if (!isset($_SESSION['user_id']) || !isset($_SESSION['booking_details'])) {
    header('Location: index.html');
    exit;
}

$booking = $_SESSION['booking_details'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $card_number = $_POST['card_number'] ?? '';
    $card_holder = $_POST['card_holder'] ?? '';
    $expiry_date = $_POST['expiry_date'] ?? '';
    $cvv = $_POST['cvv'] ?? '';

    if ($card_number && $card_holder && $expiry_date && $cvv) {
        // In a real application, you would process the payment here
        // For this example, we'll just redirect to success page
        header('Location: payment_success.php');
        exit;
    } else {
        $error = "All payment details are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Indian Airways</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --accent-color: #3b82f6;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc;
        }

        .logo-text {
            font-weight: 600;
            color: var(--primary-color);
        }

        .logo-text span {
            color: var(--secondary-color);
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }

        .form-control {
            border-radius: 8px;
            padding: 12px;
            border: 1px solid #e2e8f0;
        }

        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
        }

        .booking-summary {
            background-color: #f1f5f9;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .news-footer {
            background-color: #1e293b;
            color: white;
            padding: 40px 0;
            margin-top: 60px;
        }

        .news-item {
            padding: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .news-item:last-child {
            border-bottom: none;
        }

        .news-item h5 {
            color: #e2e8f0;
            font-size: 1rem;
            margin-bottom: 8px;
        }

        .news-item p {
            color: #94a3b8;
            font-size: 0.9rem;
            margin-bottom: 0;
        }

        .social-links a {
            color: white;
            margin-right: 15px;
            font-size: 1.2rem;
        }

        .social-links a:hover {
            color: var(--accent-color);
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
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4">Payment Details</h2>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <div class="booking-summary mb-4">
                            <h4>Booking Summary</h4>
                            <p>
                                <strong>Name:</strong> <?php echo htmlspecialchars($booking['name']); ?><br>
                                <strong>Email:</strong> <?php echo htmlspecialchars($booking['email']); ?><br>
                                <strong>Seat Number:</strong> <?php echo htmlspecialchars($booking['seat_number']); ?><br>
                                <strong>Class:</strong> <?php echo htmlspecialchars($booking['flight_class']); ?><br>
                                <strong>Amount:</strong> ₹<?php echo number_format($booking['price'], 2); ?>
                            </p>
                        </div>

                        <form action="payment.php" method="POST">
                            <div class="mb-3">
                                <label for="card_holder" class="form-label">Card Holder Name</label>
                                <input type="text" class="form-control" id="card_holder" name="card_holder" required>
                            </div>

                            <div class="mb-3">
                                <label for="card_number" class="form-label">Card Number</label>
                                <input type="text" class="form-control" id="card_number" name="card_number" 
                                       placeholder="4242 4242 4242 4242" maxlength="19" required>
                                <div class="form-text">Enter 16 digits with spaces (e.g., 4242 4242 4242 4242)</div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="expiry_date" class="form-label">Expiry Date</label>
                                    <input type="text" class="form-control" id="expiry_date" name="expiry_date" 
                                           placeholder="MM/YY" pattern="(0[1-9]|1[0-2])\/([0-9]{2})" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="cvv" class="form-label">CVV</label>
                                    <input type="text" class="form-control" id="cvv" name="cvv" 
                                           pattern="[0-9]{3}" maxlength="3" required>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-lock me-2"></i>Pay Securely Now
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer with News -->
    <footer class="news-footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h4 class="mb-4">Latest News</h4>
                    <div class="news-item">
                        <h5>New Routes Announced</h5>
                        <p>Indian Airways expands its network with new international destinations.</p>
                    </div>
                    <div class="news-item">
                        <h5>Enhanced Safety Measures</h5>
                        <p>Introducing advanced safety protocols for all flights.</p>
                    </div>
                    <div class="news-item">
                        <h5>Eco-Friendly Initiatives</h5>
                        <p>Committed to reducing carbon footprint with new sustainable practices.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <h4 class="mb-4">Quick Links</h4>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white-50">About Us</a></li>
                        <li><a href="#" class="text-white-50">Contact</a></li>
                        <li><a href="#" class="text-white-50">Careers</a></li>
                        <li><a href="#" class="text-white-50">Terms & Conditions</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h4 class="mb-4">Connect With Us</h4>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Format card number with spaces
            $('#card_number').on('input', function() {
                let value = $(this).val().replace(/\s+/g, '');
                if (value.length > 0) {
                    // Only allow numbers
                    value = value.replace(/\D/g, '');
                    // Limit to 16 digits
                    value = value.substring(0, 16);
                    // Add spaces after every 4 digits
                    value = value.match(new RegExp('.{1,4}', 'g')).join(' ');
                }
                $(this).val(value);
            });

            // Validate card number before form submission
            $('form').on('submit', function(e) {
                const cardNumber = $('#card_number').val().replace(/\s+/g, '');
                if (cardNumber.length !== 16) {
                    e.preventDefault();
                    alert('Please enter a valid 16-digit card number');
                    return false;
                }
            });

            // Format expiry date
            $('#expiry_date').on('input', function() {
                let value = $(this).val().replace(/\D/g, '');
                if (value.length > 2) {
                    value = value.substring(0, 2) + '/' + value.substring(2, 4);
                }
                $(this).val(value);
            });
        });
    </script>
</body>
</html> 