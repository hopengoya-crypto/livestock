<?php
mysqli_report(MYSQLI_REPORT_OFF);

$conn = mysqli_connect('localhost', 'root', '', 'livestock_vaccination_system');

if ($conn === false) {
    error_log('Database connection failed: ' . mysqli_connect_error());
    http_response_code(500);
    exit('The database is temporarily unavailable.');
}

if (!mysqli_set_charset($conn, 'utf8mb4')) {
    error_log('Could not set the database connection charset: ' . mysqli_error($conn));
    http_response_code(500);
    exit('The database connection could not be initialized.');
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);