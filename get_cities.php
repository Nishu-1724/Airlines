<?php
require_once 'includes/db.php';

header('Content-Type: application/json');

try {
    // First, get all countries
    $countries_query = "SELECT * FROM countries ORDER BY name";
    $countries_stmt = $pdo->prepare($countries_query);
    $countries_stmt->execute();
    $countries = $countries_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Then get all cities with their country information
    $cities_query = "SELECT c.id, c.name, c.airport_code, co.name as country_name, co.code as country_code 
                    FROM cities c 
                    JOIN countries co ON c.country_id = co.id 
                    ORDER BY co.name, c.name";
    $cities_stmt = $pdo->prepare($cities_query);
    $cities_stmt->execute();
    $cities = $cities_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Debug information
    error_log("Countries found: " . count($countries));
    error_log("Cities found: " . count($cities));
    
    // Ensure we have data
    if (empty($countries) || empty($cities)) {
        throw new Exception("No data found in the database");
    }
    
    $response = [
        'success' => true, 
        'countries' => $countries,
        'cities' => $cities
    ];
    
    echo json_encode($response);
} catch (Exception $e) {
    error_log("Error in get_cities.php: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage()
    ]);
}
?> 