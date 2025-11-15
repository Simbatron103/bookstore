<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Läs Författare - Bokhandel</title>
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
            <li><a href="authors.php" class="active">Lista Författare</a></li>
            <li><a href="add_author.php">Ny Författare</a></li>
        </ul>
    </nav>

    <h1>✍️ Författare</h1>
    
    <?php
    // Steg 1: Förbered databasanslutningen
    $dsn = "mysql:host=127.0.0.1;dbname=bookstore;charset=utf8mb4";
    $user = 'root';
    $pass = '';

    try {
        // Steg 2: Skapa anslutning till databasen
        // new PDO skapar ett objekt som låter oss prata med databasen
        $pdo = new PDO($dsn, $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Steg 3: Skriv SQL-queryn
        // Vi vill hämta alla författare från authors-tabellen
        // ORDER BY sorterar resultatet: först efter efternamn, sedan efter förnamn
        $sql = "
            SELECT 
                author_id,
                name_first, 
                name_last
            FROM 
                authors
            ORDER BY
                name_last ASC, name_first ASC
        ";
        
        // Steg 4: Kör SQL-queryn mot databasen
        $stmt = $pdo->query($sql);

        // Steg 5: Visa resultatet på webbsidan
        echo '<ul class="author-list">';
        
        // Loopa igenom varje rad i resultatet
        // fetch() hämtar en rad i taget, FETCH_ASSOC ger oss data som en array med kolumnnamn som nycklar
        $author_count = 0;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $author_count++;
            // Sätt ihop förnamn och efternamn till ett fullständigt namn
            // trim() tar bort onödiga mellanslag, htmlspecialchars() skyddar mot XSS-attacker
            $full_name = trim(htmlspecialchars($row['name_first']) . ' ' . htmlspecialchars($row['name_last']));
            echo '<li class="author-item">';
            echo '<span class="author-name">' . $full_name . '</span>';
            echo '</li>';
        }
        
        echo '</ul>';

        // Om inga författare hittades, visa ett meddelande
        if ($author_count == 0) {
             echo '<p class="empty-message">Inga författare hittades i databasen.</p>';
        }

    } catch (\PDOException $e) {
        // Om något gick fel fångar catch-blocket felet och visar ett felmeddelande
        echo '<div class="error-message">';
        echo '<h2>Databasfel:</h2>';
        echo '<p>Kunde inte hämta författare. Kontrollera XAMPP eller SQL-queryn.</p>';
        echo 'Detaljer: <em>' . $e->getMessage() . '</em>';
        echo '</div>';
    }
    ?>

</body>
</html>

