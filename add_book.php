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
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $firstName = filter_input(INPUT_POST, 'name_first', FILTER_SANITIZE_STRING);
    $lastName = filter_input(INPUT_POST, 'name_last', FILTER_SANITIZE_STRING);
    $yearPub = filter_input(INPUT_POST, 'year_pub', FILTER_SANITIZE_STRING);
    $isbn = filter_input(INPUT_POST, 'isbn', FILTER_SANITIZE_STRING);

    if (empty($title) || empty($lastName) || empty($isbn)) {
        $error = "Titel, efternamn och ISBN måste fyllas i.";
    } else {
        try {
            // Starta en transaktion för att säkerställa att båda stegen lyckas
            $pdo->beginTransaction();

            // A. FÖRST: Kontrollera/lägg till författare
            // Detta är viktigt för att undvika dubbla författare i AUTHORS-tabellen
            
            // Försök hitta befintlig författare
            $stmt = $pdo->prepare("SELECT author_id FROM authors WHERE name_first = ? AND name_last = ?");
            $stmt->execute([$firstName, $lastName]);
            $author = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($author) {
                // Författaren finns, använd dess ID
                $author_id = $author['author_id'];
            } else {
                // Författaren finns INTE, skapa ny
                $stmt = $pdo->prepare("INSERT INTO authors (name_first, name_last) VALUES (?, ?)");
                $stmt->execute([$firstName, $lastName]);
                $author_id = $pdo->lastInsertId(); // Hämta ID för den nya författaren
            }

            // B. SEDAN: Lägg till boken med författar-ID
            $stmt = $pdo->prepare("INSERT INTO books (isbn, title, author_id, year_pub) VALUES (?, ?, ?, ?)");
            $stmt->execute([$isbn, $title, $author_id, $yearPub]);

            // Bekräfta transaktionen
            $pdo->commit();
            $success = "Boken '{$title}' lades till i databasen!";

        } catch (\PDOException $e) {
            // Ångra allt om något gick fel
            $pdo->rollBack();
            $error = "Kunde inte lägga till boken: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Skapa Bok - Bokhandel</title>
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
            <li><a href="add_book.php" class="active">Ny Bok</a></li>
            <li><a href="authors.php">Lista Författare</a></li>
            <li><a href="add_author.php">Ny Författare</a></li>
        </ul>
    </nav>

    <h1>➕ Lägg till ny bok</h1>

    <?php if ($success): ?>
        <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="isbn">ISBN (Viktigt!):</label>
        <input type="text" id="isbn" name="isbn" required maxlength="20">

        <label for="title">Titel:</label>
        <input type="text" id="title" name="title" required maxlength="50">

        <label for="name_first">Författare Förnamn:</label>
        <input type="text" id="name_first" name="name_first" maxlength="50">
        
        <label for="name_last">Författare Efternamn:</label>
        <input type="text" id="name_last" name="name_last" required maxlength="50">
        
        <label for="year_pub">Utgivningsår:</label>
        <input type="text" id="year_pub" name="year_pub" maxlength="4">

        <button type="submit">Spara Bok</button>
    </form>

</body>
</html>