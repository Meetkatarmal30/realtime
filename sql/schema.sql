CREATE TABLE Vehicle (
    vehicle_id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_number VARCHAR(20),
    type ENUM('bus', 'train')
);

CREATE TABLE Route (
    route_id INT AUTO_INCREMENT PRIMARY KEY,
    source VARCHAR(50),
    destination VARCHAR(50)
);

CREATE TABLE Trip (
    trip_id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_id INT,
    route_id INT,
    start_time DATETIME,
    FOREIGN KEY (vehicle_id) REFERENCES Vehicle(vehicle_id),
    FOREIGN KEY (route_id) REFERENCES Route(route_id)
);

CREATE TABLE Location (
    location_id INT AUTO_INCREMENT PRIMARY KEY,
    trip_id INT,
    latitude DECIMAL(10, 6),
    longitude DECIMAL(10, 6),
    timestamp DATETIME,
    FOREIGN KEY (trip_id) REFERENCES Trip(trip_id)
);

CREATE TABLE Feedback (
    feedback_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    trip_id INT,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    comments TEXT,
    timestamp DATETIME
);