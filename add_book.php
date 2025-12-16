<?php
// Förbered databasanslutningen
$dsn = "mysql:host=127.0.0.1;dbname=bookstore;charset=utf8mb4";
$user = 'root';
$pass = ''; 

$pdo = null;
$error = '';
$success = '';
$authors = []; // Array för att lagra alla författare

try {
    // Steg 2: Skapa anslutning till databasen
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Steg 3: Hämta alla författare för dropdown-menyn
    // Vi behöver author_id och namnet för att visa i dropdown
    $sql = "SELECT author_id, name_first, name_last FROM authors ORDER BY name_last, name_first";
    $stmt = $pdo->query($sql);
    $authors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (\PDOException $e) {
    $error = "Databasanslutning misslyckades: " . $e->getMessage();
}

// Hantera formulär när användaren skickar in det
// När användaren trycker på "Spara Bok" görs ett anrop till samma sida igen,
// fast denna gången med POST-data. $_SERVER['REQUEST_METHOD'] === 'POST' kontrollerar
// om det är ett POST-anrop (formulär skickat) eller GET-anrop (vanlig sidladdning)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    
    // Hämta inmatning från formuläret
    // OBS: osäker metod, använd inte i riktiga projekt!
    $title =  filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $author_id =  filter_input(INPUT_POST, 'author_id', FILTER_SANITIZE_NUMBER_INT);
    $yearPub =  filter_input(INPUT_POST, 'year_pub', FILTER_SANITIZE_STRING);
    $isbn =  filter_input(INPUT_POST, 'isbn', FILTER_SANITIZE_STRING);

    // Validera att obligatoriska fält är ifyllda
    if (empty($title) || empty($author_id) || empty($isbn)) {
        $error = "Titel, författare och ISBN måste fyllas i.";
    } else {
        try {
            // Lägg till boken i databasen
            // OBS: osäker metod, använd inte i riktiga projekt!
           $sql = "INSERT INTO books (isbn, title, author_id, year_pub) VALUES (?, ?, ?, ?)";
           $stmt = $pdo->prepare($sql);
           $stmt->execute([$isbn, $title, $author_id, $yearPub]);
           $success = "Boken '{$title}' lades till i databasen!";

        } catch (\PDOException $e) {
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

    <!-- Formulär för att lägga till en ny bok -->
    <!-- method="POST" betyder att data skickas med POST-metoden när formuläret skickas -->
    <!-- action är inte specificerat, vilket betyder att formuläret skickas till samma sida (add_book.php) -->
    <!-- När användaren trycker på "Spara Bok" anropas add_book.php igen, men denna gång med POST-data -->
    <!-- Detta är anledningen till att vi kan kontrollera $_SERVER['REQUEST_METHOD'] === 'POST' ovan -->
    <form method="POST">
        <label for="isbn">ISBN:</label>
        <input type="text" id="isbn" name="isbn" required maxlength="20">

        <label for="title">Titel:</label>
        <input type="text" id="title" name="title" required maxlength="50">

        <label for="author_id">Författare:</label>
        <select id="author_id" name="author_id" required>
            <option value="">-- Välj författare --</option>
            <?php
            // Loopa igenom alla författare och skapa ett alternativ för varje
            foreach ($authors as $author) {
                $full_name = trim(htmlspecialchars($author['name_first']) . ' ' . htmlspecialchars($author['name_last']));
                echo '<option value="' . htmlspecialchars($author['author_id']) . '">' . $full_name . '</option>';
            }
            ?>
        </select>
        
        <label for="year_pub">Utgivningsår:</label>
        <input type="text" id="year_pub" name="year_pub" maxlength="4">

        <button type="submit">Spara Bok</button>
    </form>

</body>
</html>