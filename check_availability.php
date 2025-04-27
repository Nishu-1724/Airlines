<?php
require_once 'includes/db.php';

header('Content-Type: application/json');

$date = $_GET['date'] ?? '';
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

try {
    if ($date && $from && $to) {
        // Search for flights with city names
        $query = "SELECT COUNT(*) as count 
                  FROM flights f 
                  JOIN cities c1 ON f.origin_city_id = c1.id 
                  JOIN cities c2 ON f.destination_city_id = c2.id 
                  WHERE f.flight_date = :date 
                  AND (LOWER(c1.name) LIKE LOWER(:from) OR LOWER(c1.airport_code) LIKE LOWER(:from))
                  AND (LOWER(c2.name) LIKE LOWER(:to) OR LOWER(c2.airport_code) LIKE LOWER(:to))
                  AND f.seats_available > 0";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'date' => $date,
            'from' => "%$from%",
            'to' => "%$to%"
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'available' => $result['count'] > 0
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Missing required parameters'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?> 