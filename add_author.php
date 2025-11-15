<?php
// --- 1. DATABASANSLUTNING ---
$dsn = "mysql:host=127.0.0.1;dbname=bookstore;charset=utf8mb4";
$user = 'root';
$pass = ''; 

$pdo = null;
$error = '';
$success = '';

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (\PDOException $e) {
    $error = "Databasanslutning misslyckades: " . $e->getMessage();
}

// --- 2. HANTERA FORMULÄR POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    
    // Hämta och rensa inmatning (Säkerhet!)
    $firstName = filter_input(INPUT_POST, 'name_first', FILTER_SANITIZE_STRING);
    $lastName = filter_input(INPUT_POST, 'name_last', FILTER_SANITIZE_STRING);

    if (empty($lastName)) {
        $error = "Efternamn måste fyllas i.";
    } else {
        try {
            // Kontrollera om författaren redan finns
            $stmt = $pdo->prepare("SELECT author_id FROM authors WHERE name_first = ? AND name_last = ?");
            $stmt->execute([$firstName, $lastName]);
            $existingAuthor = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existingAuthor) {
                $error = "Författaren finns redan i databasen.";
            } else {
                // Lägg till ny författare
                $stmt = $pdo->prepare("INSERT INTO authors (name_first, name_last) VALUES (?, ?)");
                $stmt->execute([$firstName, $lastName]);
                
                $fullName = trim($firstName . ' ' . $lastName);
                $success = "Författaren '{$fullName}' lades till i databasen!";
            }

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

    <?php if ($success): ?>
        <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
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

