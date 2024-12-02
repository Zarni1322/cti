<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        /* Reset some basic styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f7fc;
            color: #333;
            line-height: 1.6;
            height: 100%;
            overflow-x: hidden; /* Prevent horizontal overflow */
        }

        header {
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }

        .container {
            max-width: 100%;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            height: 90vh; /* 90% of the screen height */
            overflow-y: auto; /* Vertical scroll if content overflows */
            overflow-x: hidden; /* Prevent horizontal scroll */
        }

        h1 {
            text-align: center;
            font-size: 28px;
            margin-bottom: 20px;
        }

        .result-item {
            background-color: #ecf0f1;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            box-shadow: 0 1px 6px rgba(0, 0, 0, 0.1);
            font-size: 16px;
            line-height: 1.6;
            word-wrap: break-word;
            max-width: 100%;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 30px;
            text-align: center;
        }

        .back-link:hover {
            background-color: #2980b9;
        }

        /* Ensuring the body takes up full height, and handles scrolling */
        html, body {
            height: 100%;
        }

    </style>
</head>
<body>

    <header>
        <h1>Search Results</h1>
    </header>

    <div class="container">
        <?php
        // Database connection
        $servername = "localhost";
        $username = "root";
        $password = "";
        $database = "cti"; // Replace with your actual database name

        $conn = new mysqli($servername, $username, $password, $database);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Check if a search query is set
        $query = isset($_GET['query']) ? trim($_GET['query']) : '';

        if (!empty($query)) {
            // Use a prepared statement to prevent SQL injection
            $stmt = $conn->prepare("SELECT content FROM txt_data WHERE content LIKE ?");
            $searchTerm = "%" . $query . "%";
            $stmt->bind_param("s", $searchTerm);
            $stmt->execute();
            $result = $stmt->get_result();

            echo "<h1>Search Results</h1>";

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='result-item'>" . htmlspecialchars($row['content']) . "</div>";
                }
            } else {
                echo "<p>No results found for '<strong>" . htmlspecialchars($query) . "</strong>'.</p>";
            }

            echo "<a href='search.html' class='back-link'>Go back</a>";

            $stmt->close();
        } else {
            echo "<p>No search query provided.</p>";
        }

        $conn->close();
        ?>
    </div>

</body>
</html>
