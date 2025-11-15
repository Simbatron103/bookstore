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
    // Databasanslutning
    $dsn = "mysql:host=127.0.0.1;dbname=bookstore;charset=utf8mb4";
    $user = 'root';
    $pass = '';

    try {
        $pdo = new PDO($dsn, $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // SQL-fråga för att hämta alla författare
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
        
        $stmt = $pdo->query($sql);

        // Utmatning
        echo '<ul class="author-list">';
        
        $author_count = 0;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $author_count++;
            $full_name = trim(htmlspecialchars($row['name_first']) . ' ' . htmlspecialchars($row['name_last']));
            
            echo '<li class="author-item">';
            echo '<span class="author-name">' . $full_name . '</span>';
            echo '</li>';
        }
        
        echo '</ul>';

        if ($author_count == 0) {
             echo '<p class="empty-message">Inga författare hittades i databasen.</p>';
        }

    } catch (\PDOException $e) {
        // Visa felmeddelande om anslutningen/frågan misslyckas
        echo '<div class="error-message">';
        echo '<h2>Databasfel:</h2>';
        echo '<p>Kunde inte hämta författare. Kontrollera XAMPP eller SQL-frågan.</p>';
        echo 'Detaljer: <em>' . $e->getMessage() . '</em>';
        echo '</div>';
    }
    ?>

</body>
</html>

