<?php
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function generate_random_string($length = 10) {
    return bin2hex(random_bytes($length));
}

function format_date($date, $format = 'Y-m-d') {
    return date($format, strtotime($date));
}

function is_valid_phone($phone) {
    return preg_match('/^[0-9]{10}$/', $phone);
}

function get_current_academic_year($conn, $school_id) {
    try {
        $query = "SELECT * FROM academic_years 
                 WHERE school_id = :school_id AND is_current = 1";
        $stmt = $conn->prepare($query);
        $stmt->execute([':school_id' => $school_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Error getting academic year: " . $e->getMessage());
        return null;
    }
}

function calculate_attendance_percentage($present, $total) {
    if ($total == 0) return 0;
    return round(($present / $total) * 100, 2);
}

function get_current_term($conn, $school_id) {
    try {
        $query = "SELECT t.* FROM terms t 
                 JOIN academic_years ay ON t.year_id = ay.year_id 
                 WHERE ay.school_id = :school_id 
                 AND ay.is_current = 1 
                 AND CURRENT_DATE BETWEEN t.start_date AND t.end_date";
        $stmt = $conn->prepare($query);
        $stmt->execute([':school_id' => $school_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Error getting current term: " . $e->getMessage());
        return null;
    }
}

function send_notification($type, $message, $recipient) {
    // Implement notification system (email, SMS, etc.)
    // This is a placeholder function
    error_log("Notification sent: Type: $type, Message: $message, Recipient: $recipient");
    return true;
}

function format_time($time) {
    return date('h:i A', strtotime($time));
}

function get_day_name($date) {
    return date('l', strtotime($date));
}

function is_weekend($date) {
    $day = date('N', strtotime($date));
    return ($day >= 6);
}

function get_school_settings($conn, $school_id) {
    try {
        $query = "SELECT * FROM schools WHERE school_id = :school_id";
        $stmt = $conn->prepare($query);
        $stmt->execute([':school_id' => $school_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Error getting school settings: " . $e->getMessage());
        return null;
    }
}
?>
