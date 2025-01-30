<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "users-countries";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "CREATE TABLE IF NOT EXISTS countries (
    country_code VARCHAR(2) PRIMARY KEY,
    country_name VARCHAR(100) NOT NULL
)";
$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_firstname VARCHAR(255) NOT NULL,
    user_lastname VARCHAR(255) NOT NULL,
    country_code VARCHAR(2),
    FOREIGN KEY (country_code) REFERENCES countries(country_code) ON DELETE SET NULL
)";
$conn->query($sql);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $id = $_POST['id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $country_code = $_POST['country_code'];

    $sql = "UPDATE users SET user_firstname='$first_name', user_lastname='$last_name', country_code='$country_code' WHERE id='$id'";
    $conn->query($sql);
}

$sql = "SELECT users.id, users.user_firstname, users.user_lastname, countries.country_name, users.country_code 
        FROM users 
        LEFT JOIN countries ON users.country_code = countries.country_code";
$result = $conn->query($sql);

$sql_countries = "SELECT * FROM countries";
$result_countries = $conn->query($sql_countries);
$countries = [];
while ($row = $result_countries->fetch_assoc()) {
    $countries[$row['country_code']] = $row['country_name'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Lista korisnika</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>Lista korisnika</h2>
    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <th>Ime</th>
            <th>Prezime</th>
            <th>Država</th>
            <th>Akcija</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['user_firstname']) ?></td>
            <td><?= htmlspecialchars($row['user_lastname']) ?></td>
            <td><?= htmlspecialchars($row['country_name'] ?? 'N/A') ?></td>
            <td>
                <form method="POST" class="d-inline-block">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <input type="text" name="first_name" value="<?= htmlspecialchars($row['user_firstname']) ?>">
                    <input type="text" name="last_name" value="<?= htmlspecialchars($row['user_lastname']) ?>">
                    <select name="country_code">
                        <?php foreach ($countries as $code => $name): ?>
                            <option value="<?= $code ?>" <?= $row['country_code'] == $code ? 'selected' : '' ?>><?= $name ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" name="update" class="btn btn-primary btn-sm">Spremi</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>

<?php $conn->close(); ?>
