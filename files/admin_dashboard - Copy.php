<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user'])){
    go_to("login.php");
}

$user = $_SESSION['user'];

if($user['role'] !== "admin"){
    go_to("user_dashboard.php");
}

$page = isset($_GET['page']) ? $_GET['page'] : "home";

/* ADD EVENT */
if(isset($_POST['add_event'])){
    $title = $_POST['title'];
    $description = $_POST['description'];
    $event_date = $_POST['event_date'];

    $stmt = $conn->prepare("INSERT INTO events(title, description, event_date) VALUES(?, ?, ?)");
    $stmt->bind_param("sss", $title, $description, $event_date);

    if($stmt->execute()){
        set_message("Event added successfully.", "success");
    } else {
        set_message("Failed to add event.", "error");
    }

    go_to("admin_dashboard.php?page=add_event");
}

/* DELETE EVENT */
if(isset($_GET['delete'])){
    $event_id = (int) $_GET['delete'];

    $delete_registrations = $conn->prepare("DELETE FROM registrations WHERE event_id = ?");
    $delete_registrations->bind_param("i", $event_id);
    $delete_registrations->execute();

    $delete_event = $conn->prepare("DELETE FROM events WHERE id = ?");
    $delete_event->bind_param("i", $event_id);
    $delete_event->execute();

    set_message("Event deleted successfully.", "success");
    go_to("admin_dashboard.php?page=events");
}

/* ADD USER */
if(isset($_POST['add_user'])){
    $name = $_POST['fullname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users(fullname, email, password, role) VALUES(?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $password, $role);

    if($stmt->execute()){
        set_message("User added successfully.", "success");
    } else {
        set_message("Failed to add user. Email may already exist.", "error");
    }

    go_to("admin_dashboard.php?page=add_user");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>
<div class="layout">
    <div class="sidebar">
        <h2>Admin Panel</h2>

        <a class="menu-link <?php if($page == 'home') echo 'active'; ?>" href="?page=home">
            <i class="bx bx-home"></i> Home
        </a>

        <a class="menu-link <?php if($page == 'add_event') echo 'active'; ?>" href="?page=add_event">
            <i class="bx bx-calendar-plus"></i> Add Event
        </a>

        <a class="menu-link <?php if($page == 'events') echo 'active'; ?>" href="?page=events">
            <i class="bx bx-calendar-event"></i> Events
        </a>

        <a class="menu-link <?php if($page == 'students') echo 'active'; ?>" href="?page=students">
            <i class="bx bx-group"></i> Students
        </a>

        <a class="menu-link <?php if($page == 'add_user') echo 'active'; ?>" href="?page=add_user">
            <i class="bx bx-user-plus"></i> Add User
        </a>

        <a class="menu-link" href="logout.php">
            <i class="bx bx-log-out"></i> Logout
        </a>
    </div>

    <div class="content">
        <div class="topbar">
            <h1>Event Management</h1>
            <strong><?php echo clean($user['fullname']); ?></strong>
        </div>

        <?php include 'notification.php'; ?>

        <?php if($page == "home"){ ?>
            <div class="panel">
                <h2>Welcome, <?php echo clean($user['fullname']); ?></h2>
                <p>Use the sidebar to manage events, users, and event registrations.</p>
            </div>
        <?php } ?>

        <?php if($page == "add_event"){ ?>
            <div class="panel">
                <h2>Add Event</h2>

                <form method="POST">
                    <input type="text" name="title" placeholder="Event title" required>
                    <textarea name="description" placeholder="Event description"></textarea>
                    <input type="date" name="event_date" required>
                    <button name="add_event">
                        <i class="bx bx-save"></i>
                        Save Event
                    </button>
                </form>
            </div>
        <?php } ?>

        <?php if($page == "events"){ ?>
            <div class="panel">
                <h2>All Events</h2>

                <div class="events-grid">
                <?php
                $events = mysqli_query($conn, "SELECT * FROM events ORDER BY id DESC");

                while($event = mysqli_fetch_assoc($events)){
                ?>
                    <div class="event-item">
                        <h3><?php echo clean($event['title']); ?></h3>
                        <p><?php echo clean($event['description']); ?></p>
                        <p><b>Date:</b> <?php echo clean($event['event_date']); ?></p>

                        <a class="button button-danger"
                           href="?delete=<?php echo (int) $event['id']; ?>&page=events"
                           onclick="return confirm('Delete this event and its registrations?')">
                           <i class="bx bx-trash"></i>
                           Delete
                        </a>

                        <button class="button-success" onclick="viewStudents(<?php echo (int) $event['id']; ?>)">
                            <i class="bx bx-show"></i>
                            View Students
                        </button>
                    </div>
                <?php } ?>
                </div>
            </div>

            <div class="panel">
                <h3>Event Students</h3>
                <div id="studentsList">
                    <p class="muted-text">Click "View Students" to see registrations.</p>
                </div>
            </div>
        <?php } ?>

        <?php if($page == "students"){ ?>
            <div class="panel">
                <h2>Registered Students</h2>

                <?php
                $query = mysqli_query($conn, "
                    SELECT users.fullname, users.email, events.title, registrations.ticket_number
                    FROM registrations
                    JOIN users ON users.id = registrations.user_id
                    JOIN events ON events.id = registrations.event_id
                    ORDER BY registrations.id DESC
                ");

                if(mysqli_num_rows($query) > 0){
                    echo "<table>";
                    echo "<tr>
                            <th>#</th>
                            <th>Student</th>
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
                    echo "<p class='danger-text'>No students registered yet.</p>";
                }
                ?>
            </div>
        <?php } ?>

        <?php if($page == "add_user"){ ?>
            <div class="panel">
                <h2>Add User</h2>

                <form method="POST">
                    <input type="text" name="fullname" placeholder="Full name" required>
                    <input type="email" name="email" placeholder="Email address" required>

                    <div class="field password-field">
                        <i class="bx bx-lock-alt field-icon"></i>
                        <input type="password" id="admin_user_password" name="password" placeholder="Password" required>

                        <button type="button" class="password-toggle" onclick="togglePassword('admin_user_password', this)">
                            <i class="bx bx-show"></i>
                        </button>
                    </div>

                    <select name="role">
                        <option value="student">Student</option>
                        <option value="admin">Admin</option>
                    </select>

                    <button name="add_user">
                        <i class="bx bx-user-plus"></i>
                        Save User
                    </button>
                </form>
            </div>

            <div class="panel">
                <h2>System Users</h2>

                <?php
                $users = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");

                if(mysqli_num_rows($users) > 0){
                    echo "<table>";
                    echo "<tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                          </tr>";

                    $number = 1;

                    while($system_user = mysqli_fetch_assoc($users)){
                        echo "<tr>
                                <td>".clean($number++)."</td>
                                <td>".clean($system_user['fullname'])."</td>
                                <td>".clean($system_user['email'])."</td>
                                <td>".clean($system_user['role'])."</td>
                              </tr>";
                    }

                    echo "</table>";
                } else {
                    echo "<p class='danger-text'>No users found.</p>";
                }
                ?>
            </div>
        <?php } ?>
    </div>
</div>

<script>
function viewStudents(event_id){
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "fetch_students.php?event_id=" + event_id, true);

    xhr.onload = function(){
        document.getElementById("studentsList").innerHTML = this.responseText;
    };

    xhr.send();
}
</script>

<script src="script.js"></script>
</body>
</html>
