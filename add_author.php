<?php
// Förbered databasanslutningen
$dsn = "mysql:host=127.0.0.1;dbname=bookstore;charset=utf8mb4";
$user = 'root';
$pass = '';

$pdo = null;
$error = '';
$success = '';

try {
    // Skapa anslutning till databasen
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (\PDOException $e) {
    $error = "Databasanslutning misslyckades: " . $e->getMessage();
}

// Hantera formulär när användaren skickar in det
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    
    // Hämta inmatning fråm formuläret
    // OBS: osäker metod, använd inte i riktiga projekt!
    $firstName = $_POST['name_first'];
    $lastName = $_POST['name_last'];

    // Validering: Kontrollera om obligatoriska fält är ifyllda
    if (empty($lastName)) {
        $error = "Efternamn måste fyllas i.";
    } else {
        try {
            // Lägg till författaren i databasen
            // TODO: Skriv SQL-queryn för att lägga till en ny författare i authors-tabellen
            $sql = "INSERT INTO authors (name_first, name_last) VALUES ('$firstName', '$lastName')";
        
            // Kör SQL-queryn mot databasen
            // OBS: osäker metod, använd inte i riktiga projekt!
            $pdo->query($sql);
            
            $fullName = trim($firstName . ' ' . $lastName);
            $success = "Författaren '{$fullName}' lades till i databasen!";

        } catch (\PDOException $e) {
            $error = "Kunde inte lägga till författaren: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Skapa Författare - Bokhandel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <h1>Bokbutiken</h1>
        <div class="subtitle">SELECT your book</div>
    </header>

    <nav>
        <ul>
            <li><a href="index.php">Lista Böcker</a></li>
            <li><a href="add_book.php">Ny Bok</a></li>
            <li><a href="authors.php">Lista Författare</a></li>
            <li><a href="add_author.php" class="active">Ny Författare</a></li>
        </ul>
    </nav>

    <h1>➕ Lägg till ny författare</h1>

    <?php 
    // Visa statusmeddelanden (SUCCESS/ERROR)
    if ($success): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="name_first">Förnamn:</label>
        <input type="text" id="name_first" name="name_first" maxlength="50">
        
        <label for="name_last">Efternamn (Obligatoriskt):</label>
        <input type="text" id="name_last" name="name_last" required maxlength="50">

        <button type="submit">Spara Författare</button>
    </form>

</body>
</html>