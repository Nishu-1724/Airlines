<?php
session_start();

// Check if user is logged in and has booking details
if (!isset($_SESSION['user_id']) || !isset($_SESSION['booking_details'])) {
    header('Location: index.html');
    exit;
}

$booking = $_SESSION['booking_details'];

// Create the content for the text file
$content = "INDIAN AIRWAYS - BOOKING CONFIRMATION\n";
$content .= "=====================================\n\n";
$content .= "Booking Details:\n";
$content .= "----------------\n";
$content .= "Name: " . $booking['name'] . "\n";
$content .= "Email: " . $booking['email'] . "\n";
$content .= "Seat Number: " . $booking['seat_number'] . "\n";
$content .= "Class: " . $booking['flight_class'] . "\n";
$content .= "Amount: ₹" . number_format($booking['price'], 2) . "\n\n";
$content .= "Booking Date: " . date('Y-m-d H:i:s') . "\n";
$content .= "Booking Reference: " . strtoupper(substr(md5(uniqid()), 0, 8)) . "\n\n";
$content .= "Thank you for choosing Indian Airways!\n";
$content .= "For any queries, please contact our customer support.\n";

// Set headers for file download
header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename="booking_confirmation.txt"');
header('Content-Length: ' . strlen($content));
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Output the content
echo $content;
exit;
?> 