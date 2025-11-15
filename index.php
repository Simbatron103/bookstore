<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Läs Böcker - Bokhandel</title>
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
            <li><a href="index.php" class="active">Lista Böcker</a></li>
            <li><a href="add_book.php">Ny Bok</a></li>
            <li><a href="authors.php">Lista Författare</a></li>
            <li><a href="add_author.php">Ny Författare</a></li>
        </ul>
    </nav>

    <h1>📚 Böcker</h1>
    
    <?php
    // Databasanslutning
    $dsn = "mysql:host=127.0.0.1;dbname=bookstore;charset=utf8mb4";
    $user = 'root';
    $pass = ''; // Tomt lösenord

    try {
        $pdo = new PDO($dsn, $user, $pass);
        
        // --- ENKLA SQL-FRÅGAN HÄR ---
        $sql = "
            SELECT 
                b.title, 
                a.name_first, 
                a.name_last
            FROM 
                books b 
            JOIN 
                authors a 
            ON 
                b.author_id = a.author_id
            ORDER BY
                b.title ASC
        ";
        
        $stmt = $pdo->query($sql);

        // --- UTMATNING ---
        echo '<ul class="book-list">';
        
        // Hämta och skriv ut varje rad
        $book_count = 0;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $book_count++;
            $full_author_name = htmlspecialchars($row['name_first']) . ' ' . htmlspecialchars($row['name_last']);
            
            echo '<li class="book-item">';
            echo '<span class="title">' . htmlspecialchars($row['title']) . '</span>';
            echo '<span class="author">Författare: ' . $full_author_name . '</span>';
            echo '</li>';
        }
        
        echo '</ul>';

        if ($book_count == 0) {
             echo '<p class="empty-message">Inga böcker hittades i databasen.</p>';
        }


    } catch (\PDOException $e) {
        // Visa ett snyggare felmeddelande om anslutningen/frågan misslyckas
        echo '<div class="error-message">';
        echo '<h2>Databasfel:</h2>';
        echo '<p>Kunde inte hämta böcker. Kontrollera XAMPP eller SQL-frågan.</p>';
        echo 'Detaljer: <em>' . $e->getMessage() . '</em>';
        echo '</div>';
    }
    ?>

</body>
</html>