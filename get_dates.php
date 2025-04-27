<?php
require_once 'includes/db.php';

header('Content-Type: application/json');

try {
    $query = "SELECT DISTINCT flight_date 
              FROM flights 
              WHERE flight_date >= CURDATE() 
              AND seats_available > 0 
              ORDER BY flight_date";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $dates = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo json_encode(['success' => true, 'dates' => $dates]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?> 