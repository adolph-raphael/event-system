<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== "admin"){
    echo "<p class='danger-text'>Access denied.</p>";
    exit();
}

$event_id = isset($_GET['event_id']) ? (int) $_GET['event_id'] : 0;

if($event_id > 0){
    $stmt = $conn->prepare("
        SELECT users.fullname, users.email, events.title, registrations.ticket_number
        FROM registrations
        JOIN users ON users.id = registrations.user_id
        JOIN events ON events.id = registrations.event_id
        WHERE registrations.event_id = ?
        ORDER BY registrations.id DESC
    ");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $query = $stmt->get_result();
} else {
    $query = mysqli_query($conn, "
        SELECT users.fullname, users.email, events.title, registrations.ticket_number
        FROM registrations
        JOIN users ON users.id = registrations.user_id
        JOIN events ON events.id = registrations.event_id
        ORDER BY registrations.id DESC
    ");
}

if(mysqli_num_rows($query) > 0){
    echo "<table>";

    echo "<tr>
            <th>#</th>
            <th>Student Name</th>
            <th>Email</th>
            <th>Event</th>
            <th>Ticket</th>
          </tr>";

    $number = 1;

    while($row = mysqli_fetch_assoc($query)){
        echo "<tr>
                <td>".clean($number++)."</td>
                <td>".clean($row['fullname'])."</td>
                <td>".clean($row['email'])."</td>
                <td>".clean($row['title'])."</td>
                <td>".clean($row['ticket_number'])."</td>
              </tr>";
    }

    echo "</table>";
} else {
    echo "<p class='danger-text'>No students found.</p>";
}
?>
