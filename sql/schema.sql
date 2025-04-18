
-- Create Vehicle Table
CREATE TABLE Vehicle (
    vehicle_id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_number VARCHAR(20),
    name VARCHAR(50) GENERATED ALWAYS AS (vehicle_number) VIRTUAL,
    type ENUM('bus', 'train', 'shuttle'),
    current_location VARCHAR(100),
    status ENUM('On Time', 'Delayed', 'Maintenance', 'Inactive') DEFAULT 'On Time',
    last_updated DATETIME
);

-- Create Route Table with separate Latitude and Longitude for Source and Destination
CREATE TABLE Route (
    route_id INT AUTO_INCREMENT PRIMARY KEY,
    source_lat DECIMAL(10, 6),
    source_lng DECIMAL(10, 6),
    destination_lat DECIMAL(10, 6),
    destination_lng DECIMAL(10, 6)
);

-- Create Trip Table
CREATE TABLE Trip (
    trip_id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_id INT,
    route_id INT,
    start_time DATETIME,
    end_time DATETIME,
    FOREIGN KEY (vehicle_id) REFERENCES Vehicle(vehicle_id),
    FOREIGN KEY (route_id) REFERENCES Route(route_id)
);

-- Create Location Table to store dynamic locations for trips
CREATE TABLE Location (
    location_id INT AUTO_INCREMENT PRIMARY KEY,
    trip_id INT,
    latitude DECIMAL(10, 6),
    longitude DECIMAL(10, 6),
    timestamp DATETIME,
    FOREIGN KEY (trip_id) REFERENCES Trip(trip_id)
);

-- Create Route_Point Table for storing specific route points during a trip
CREATE TABLE Route_Point (
    route_point_id INT AUTO_INCREMENT PRIMARY KEY,
    trip_id INT,
    latitude DECIMAL(10, 6),
    longitude DECIMAL(10, 6),
    point_order INT,
    FOREIGN KEY (trip_id) REFERENCES Trip(trip_id)
);

-- Create Feedback Table for storing feedback about trips
CREATE TABLE Feedback (
    feedback_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    trip_id INT,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    message TEXT,
    timestamp DATETIME,
    FOREIGN KEY (trip_id) REFERENCES Trip(trip_id)
);

