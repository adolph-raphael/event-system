<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user'])){
    go_to("login.php");
}

$user = $_SESSION['user'];

if($user['role'] !== "student"){
    go_to("admin_dashboard.php");
}

$user_id = (int) $user['id'];
$page = isset($_GET['page']) ? $_GET['page'] : "home";

/* REGISTER FOR EVENT */
if(isset($_GET['register'])){
    $event_id = (int) $_GET['register'];

    $check = $conn->prepare("
        SELECT id FROM registrations
        WHERE user_id = ? AND event_id = ?
    ");
    $check->bind_param("ii", $user_id, $event_id);
    $check->execute();
    $result = $check->get_result();

    if($result->num_rows == 0){
        $ticket = "TKT-" . rand(10000, 99999);

        $stmt = $conn->prepare("
            INSERT INTO registrations(user_id, event_id, ticket_number)
            VALUES(?, ?, ?)
        ");
        $stmt->bind_param("iis", $user_id, $event_id, $ticket);
        $stmt->execute();

        set_message("You have registered for the event successfully.", "success");
    } else {
        set_message("You are already registered for this event.", "info");
    }

    go_to("user_dashboard.php?page=events");
}

/* CANCEL REGISTRATION */
if(isset($_GET['cancel'])){
    $event_id = (int) $_GET['cancel'];

    $stmt = $conn->prepare("
        DELETE FROM registrations
        WHERE user_id = ? AND event_id = ?
    ");
    $stmt->bind_param("ii", $user_id, $event_id);
    $stmt->execute();

    set_message("Registration cancelled successfully.", "success");
    go_to("user_dashboard.php?page=my");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>
<div class="layout">
    <div class="sidebar">
        <h3>Student Panel</h3>

        <a class="menu-link <?php if($page == 'home') echo 'active'; ?>" href="?page=home">
            <i class="bx bx-home"></i> Home
        </a>

        <a class="menu-link <?php if($page == 'events') echo 'active'; ?>" href="?page=events">
            <i class="bx bx-calendar-event"></i> Events
        </a>

        <a class="menu-link <?php if($page == 'my') echo 'active'; ?>" href="?page=my">
            <i class="bx bx-bookmark"></i> My Events
        </a>

        <a class="menu-link" href="logout.php">
            <i class="bx bx-log-out"></i> Logout
        </a>
    </div>

    <div class="content">
        <div class="topbar">
            <h1>Event Portal</h1>
            <strong><?php echo clean($user['fullname']); ?></strong>
        </div>

        <?php include 'notification.php'; ?>

        <?php if($page == "home"){ ?>
            <div class="panel">
                <h2>Welcome, <?php echo clean($user['fullname']); ?></h2>
                <p>Email: <?php echo clean($user['email']); ?></p>
                <p>Browse available events and manage your registrations from the sidebar.</p>
            </div>
        <?php } ?>

        <?php if($page == "events"){ ?>
            <div class="panel">
                <h2>Available Events</h2>

                <div class="events-grid">
                <?php
                $events = mysqli_query($conn, "SELECT * FROM events ORDER BY event_date ASC");
                $today = date("Y-m-d");

                while($event = mysqli_fetch_assoc($events)){
                    $event_id = (int) $event['id'];

                    $check = $conn->prepare("
                        SELECT id FROM registrations
                        WHERE user_id = ? AND event_id = ?
                    ");
                    $check->bind_param("ii", $user_id, $event_id);
                    $check->execute();
                    $check_result = $check->get_result();

                    $is_registered = ($check_result->num_rows > 0);
                    $is_finished = ($event['event_date'] < $today);
                ?>
                    <div class="event-box">
                        <h4><?php echo clean($event['title']); ?></h4>
                        <p><?php echo clean($event['description']); ?></p>
                        <p><b>Date:</b> <?php echo clean($event['event_date']); ?></p>

                        <?php if($is_finished){ ?>
                            <p>Status: <span class="status-finished">Finished</span></p>
                            <button class="button button-gray" disabled>Event Closed</button>
                        <?php } elseif($is_registered){ ?>
                            <p>Status: <span class="status-upcoming">Upcoming</span></p>
                            <button class="button button-gray" disabled>Already Registered</button>
                        <?php } else { ?>
                            <p>Status: <span class="status-upcoming">Upcoming</span></p>
                            <a class="button" href="?register=<?php echo $event_id; ?>&page=events">
                                <i class="bx bx-check-circle"></i>
                                Register
                            </a>
                        <?php } ?>
                    </div>
                <?php } ?>
                </div>
            </div>
        <?php } ?>

        <?php if($page == "my"){ ?>
            <div class="panel">
                <h2>My Registered Events</h2>

                <div class="events-grid">
                <?php
                $stmt = $conn->prepare("
                    SELECT events.title, events.event_date, registrations.ticket_number, events.id
                    FROM registrations
                    JOIN events ON events.id = registrations.event_id
                    WHERE registrations.user_id = ?
                    ORDER BY events.event_date ASC
                ");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $my_events = $stmt->get_result();

                if($my_events->num_rows > 0){
                    while($row = $my_events->fetch_assoc()){
                        $title = clean($row['title']);
                        $date = clean($row['event_date']);
                        $ticket = clean($row['ticket_number']);
                        $js_title = json_encode($row['title']);
                        $js_date = json_encode($row['event_date']);
                        $js_ticket = json_encode($row['ticket_number']);
                ?>
                    <div class="event-box">
                        <h4><?php echo $title; ?></h4>
                        <p><b>Date:</b> <?php echo $date; ?></p>
                        <p><b>Ticket:</b> <?php echo $ticket; ?></p>

                        <button class="button button-green" onclick='printTicket(<?php echo $js_title; ?>, <?php echo $js_date; ?>, <?php echo $js_ticket; ?>)'>
                            <i class="bx bx-printer"></i>
                            Print
                        </button>

                        <a class="button button-red"
                           href="?cancel=<?php echo (int) $row['id']; ?>&page=my"
                           onclick="return confirm('Cancel this registration?')">
                           <i class="bx bx-x-circle"></i>
                           Cancel
                        </a>
                    </div>
                <?php
                    }
                } else {
                    echo "<p class='muted-text'>You have not registered for any events yet.</p>";
                }
                ?>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<script>
function printTicket(title, date, ticket){
    var win = window.open("", "", "width=600,height=600");
    var heading = win.document.createElement("h2");
    var eventLine = win.document.createElement("p");
    var dateLine = win.document.createElement("p");
    var ticketLine = win.document.createElement("p");

    heading.textContent = "Event Ticket";
    eventLine.textContent = "Event: " + title;
    dateLine.textContent = "Date: " + date;
    ticketLine.textContent = "Ticket: " + ticket;

    win.document.body.appendChild(heading);
    win.document.body.appendChild(eventLine);
    win.document.body.appendChild(dateLine);
    win.document.body.appendChild(ticketLine);
    win.print();
}
</script>

<script src="script.js"></script>
</body>
</html>
