<?php
require_once 'includes/db.php';

try {
    // Get all cities
    $query = "SELECT id FROM cities";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $cities = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Generate flights for next 30 days
    $start_date = date('Y-m-d');
    $end_date = date('Y-m-d', strtotime('+30 days'));

    // Clear existing flights
    $pdo->exec("TRUNCATE TABLE flights");

    // Generate flights for each city pair
    foreach ($cities as $origin_id) {
        foreach ($cities as $destination_id) {
            if ($origin_id != $destination_id) {
                $current_date = new DateTime($start_date);
                while ($current_date <= new DateTime($end_date)) {
                    // Generate 3 flights per day (morning, afternoon, evening)
                    $times = ['08:00:00', '14:00:00', '20:00:00'];
                    foreach ($times as $time) {
                        // Generate flights for each class
                        $classes = ['Economy', 'Business', 'First Class'];
                        foreach ($classes as $class) {
                            // Set different prices for different classes
                            $base_price = 5000;
                            switch ($class) {
                                case 'Business':
                                    $price = $base_price * 2;
                                    break;
                                case 'First Class':
                                    $price = $base_price * 3;
                                    break;
                                default:
                                    $price = $base_price;
                            }

                            // Insert flight
                            $query = "INSERT INTO flights (airline, origin_city_id, destination_city_id, 
                                     flight_date, flight_time, seats_total, seats_available, 
                                     flight_class, price) 
                                     VALUES (:airline, :origin, :destination, :date, :time, 
                                     :seats, :seats, :class, :price)";
                            
                            $stmt = $pdo->prepare($query);
                            $stmt->execute([
                                'airline' => 'Indian Airways',
                                'origin' => $origin_id,
                                'destination' => $destination_id,
                                'date' => $current_date->format('Y-m-d'),
                                'time' => $time,
                                'seats' => 150,
                                'class' => $class,
                                'price' => $price
                            ]);
                        }
                    }
                    $current_date->modify('+1 day');
                }
            }
        }
    }

    echo "Flights generated successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 