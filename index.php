<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script>
        // Fetch and display the Kanban board
        function fetchKanbanBoard(projectId) {
            if (!projectId) return;
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "fetch_kanban.php?project_id=" + projectId, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById("kanban-board").innerHTML = xhr.responseText;
                } else {
                    document.getElementById("kanban-board").innerHTML = "Error loading Kanban board.";
                }
            };
            xhr.send();
        }

        // Add a new task to a specific status
        function addTask(statusId) {
            const taskInput = document.getElementById("task-input-" + statusId);
            const taskName = taskInput ? taskInput.value.trim() : "";
            const projectId = document.getElementById("projectSelect").value;

            if (!taskName) {
                alert("Task name cannot be empty.");
                return;
            }

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "add_task.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onload = function() {
                if (xhr.status === 200) {
                    alert(xhr.responseText);
                    fetchKanbanBoard(projectId); // Reload the Kanban board
                    if (taskInput) taskInput.value = ""; // Clear the input field
                } else {
                    alert("Error adding task.");
                }
            };

            xhr.send(`status_id=${statusId}&task_name=${encodeURIComponent(taskName)}&project_id=${projectId}`);
        }

        // Add a new status to the project
        function addStatus() {
            const projectId = document.getElementById("projectSelect").value;
            const statusName = document.getElementById("statusName").value.trim();

            if (!projectId) {
                alert("Please select a project.");
                return;
            }

            if (!statusName) {
                alert("Please enter a status name.");
                return;
            }

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "add_status.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onload = function() {
                if (xhr.status === 200) {
                    alert(xhr.responseText);
                    document.getElementById("statusName").value = ""; // Clear input field
                    fetchKanbanBoard(projectId); // Reload the Kanban board
                } else {
                    alert("Error adding status.");
                }
            };

            xhr.send(`project_id=${projectId}&status_name=${encodeURIComponent(statusName)}`);
        }

        // Open the modal and fetch task details
        function openModal(taskId) {
            const modal = document.getElementById("task-modal");
            modal.style.display = "flex";

            const xhr = new XMLHttpRequest();
            xhr.open("GET", "fetch_task.php?task_id=" + taskId, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    const task = JSON.parse(xhr.responseText);

                    if (task.error) {
                        alert(task.error);
                        return;
                    }

                    document.getElementById("task-title").innerText = task.title;
                    document.getElementById("task-id").value = taskId;
                    document.getElementById("task-assignee").value = task.assignee || "Unassigned";
                    document.getElementById("task-description").value = task.description;

                    const userDropdown = document.getElementById("user-dropdown");
                    userDropdown.innerHTML = "";

                    task.users.forEach(user => {
                        const option = document.createElement("option");
                        option.value = user.id;
                        option.text = user.username;
                        userDropdown.appendChild(option);
                    });

                    const commentList = document.getElementById("comment-list");
                    commentList.innerHTML = "";
                    task.comments.forEach(comment => {
                        const div = document.createElement("div");
                        div.classList.add("comment");
                        div.innerText = comment.content;
                        commentList.appendChild(div);
                    });
                } else {
                    alert("Failed to load task details.");
                }
            };
            xhr.send();
        }

        function assignTask() {
            const taskId = document.getElementById("task-id").value;
            const assigneeId = document.getElementById("user-dropdown").value;

            if (!assigneeId) {
                alert("Please select a user to assign.");
                return;
            }

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "assign_task.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onload = function() {
                if (xhr.status === 200) {
                    alert(xhr.responseText);
                    closeModal(); // Close the modal after assigning
                } else {
                    alert("Failed to assign task.");
                }
            };

            xhr.send(`task_id=${taskId}&assignee_id=${assigneeId}`);
        }
        // Close the modal
        function closeModal() {
            const modal = document.getElementById("task-modal");
            modal.style.display = "none";
        }

        // Save task details
        function saveTask() {
            const taskId = document.getElementById("task-id").value;
            const description = document.getElementById("task-description").value;

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "save_task.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onload = function() {
                if (xhr.status === 200) {
                    alert(xhr.responseText);
                    closeModal();
                } else {
                    alert("Failed to save task.");
                }
            };

            xhr.send(`task_id=${taskId}&description=${encodeURIComponent(description)}`);
        }

        function addProject(event) {
            event.preventDefault(); // Prevent form submission

            const projectName = document.getElementById("projectName").value.trim();
            const projectDescription = document.getElementById("projectDescription").value.trim();

            if (!projectName) {
                alert("Project name cannot be empty.");
                return false;
            }

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "add_project.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onload = function() {
                if (xhr.status === 200) {
                    alert(xhr.responseText);

                    // Clear the form
                    document.getElementById("projectName").value = "";
                    document.getElementById("projectDescription").value = "";

                    // Reload the project list
                    location.reload();
                } else {
                    alert("Error adding project.");
                }
            };

            xhr.send(`project_name=${encodeURIComponent(projectName)}&project_description=${encodeURIComponent(projectDescription)}`);
            return false;
        }

        // Add a comment
        function addComment() {
            const taskId = document.getElementById("task-id").value;
            const comment = document.getElementById("new-comment").value.trim();

            if (!comment) {
                alert("Comment cannot be empty.");
                return;
            }

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "add_comment.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onload = function() {
                if (xhr.status === 200) {
                    alert(xhr.responseText);
                    document.getElementById("new-comment").value = "";
                    openModal(taskId); // Reload modal
                } else {
                    alert("Failed to add comment.");
                }
            };

            xhr.send(`task_id=${taskId}&comment=${encodeURIComponent(comment)}`);
        }
    </script>
</head>

<body>
    <?php
    $db_pass = "zaq1@WSX";
    $connection_string = "host=localhost dbname=kanban user=postgres password=$db_pass";
    $polaczenie = pg_connect($connection_string);

    if (!$polaczenie) {
        die("Database connection failed.");
    }

    // Handle logout functionality
    if (isset($_POST['logout'])) {
        session_destroy();
        header("Location: index.php");
        exit;
    }

    // Handle login functionality
    if (isset($_POST['login']) && isset($_POST['pass'])) {
        $login = $_POST['login'];
        $pass = $_POST['pass'];

        $query = pg_query($polaczenie, "SELECT username, id FROM users WHERE password = '$pass' AND username = '$login'");

        if (pg_num_rows($query) === 1) {
            $user = pg_fetch_assoc($query);
            $_SESSION['logowanie'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];
        } else {
            echo "<div class='error-message'>Invalid login or password.</div>";
        }
    }

    if (isset($_SESSION['logowanie'])) {
        $user_id = $_SESSION['user_id'];
    ?>
        <div class="navbar">
            <div class="greeting">Hello, <?php echo htmlspecialchars($_SESSION['logowanie']); ?>!</div>
            <form method="POST" action="index.php">
                <input type="hidden" name="logout" value="1">
                <button name="logout" type="submit">Log Out</button>
            </form>
        </div>
        <div class="container">
            <div class="sidebar">
                <h2>Your Projects</h2>
                <?php
                $projects_query = pg_query($polaczenie, "SELECT id, name FROM projects WHERE owner_id = $user_id");

                echo "<select id='projectSelect' onchange='fetchKanbanBoard(this.value)'>";
                echo "<option value='' disabled selected>Select a project</option>";
                while ($project = pg_fetch_assoc($projects_query)) {
                    echo "<option value='" . $project['id'] . "'>" . htmlspecialchars($project['name']) . "</option>";
                }
                echo "</select>";
                ?>

                <div class="add-status">
                    <h3>Add a Status</h3>
                    <input type="text" id="statusName" placeholder="Enter status name">
                    <button onclick="addStatus()">Add Status</button>
                    <div id="addStatusMessage"></div>
                </div>
                <div class="add-project">
                    <h3>Add a Project</h3>
                    <form id="addProjectForm" onsubmit="return addProject(event)">
                        <input type="text" id="projectName" placeholder="Enter project name" required>
                        <textarea id="projectDescription" placeholder="Enter project description"></textarea>
                        <button type="submit">Add Project</button>
                    </form>
                    <div id="addProjectMessage"></div>
                </div>
            </div>
            <div class="content">
                <h2>Kanban Board</h2>
                <div id="kanban-board">
                    <p>Select a project to view its Kanban board.</p>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div id="task-modal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 id="task-title">Task Title</h2>
                    <button class="close-button" onclick="closeModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="task-id">
                    <label for="task-assignee">Assignee:</label>
                    <select id="user-dropdown">
                        <option value="" disabled selected>Select a user</option>
                    </select>
                    <button onclick="assignTask()">Assign</button>

                    <label for="task-description">Description:</label>
                    <textarea id="task-description"></textarea>

                    <div class="comments">
                        <h3>Comments</h3>
                        <div id="comment-list"></div>
                        <textarea id="new-comment" placeholder="Add a comment"></textarea>
                        <button onclick="addComment()">Add Comment</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="save" onclick="saveTask()">Save</button>
                    <button class="cancel" onclick="closeModal()">Cancel</button>
                </div>
            </div>
        </div>

    <?php
    } else {
    ?>
        <div class="content">
            <form action="index.php" method="post">
                <input type="text" name="login" placeholder="Login" required>
                <input type="password" name="pass" placeholder="Password" required>
                <input type="submit" name="zaloguj" value="Log In">
            </form>
        </div>
    <?php
    } if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit;
    }
    ?>
</body>

</html>