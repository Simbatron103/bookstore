<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <title>Boklista (Enkel SQL)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 40px;
            background-color: #f4f7f6;
            color: #333;
        }
        h1 {
            color: #00796b;
            border-bottom: 2px solid #00796b;
            padding-bottom: 10px;
        }
        .book-list {
            list-style: none;
            padding: 0;
        }
        .book-item {
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            margin-bottom: 12px;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .title {
            font-size: 1.2em;
            font-weight: bold;
            color: #388e3c;
            display: block;
            margin-bottom: 5px;
        }
        .author {
            font-style: italic;
            color: #555;
            font-size: 0.9em;
        }
        .error-message {
            color: #d32f2f;
            background-color: #ffebee;
            padding: 10px;
            border: 1px solid #d32f2f;
            border-radius: 4px;
        }
    </style>
</head>
<body>

    <h1>📚 Boklista (Hämtad med enkel JOIN)</h1>

    <p><a href="add_book.php" style="
        display: inline-block;
        padding: 10px 15px;
        background-color: #f44336; /* Röd knapp */
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        margin-bottom: 20px;
    ">➕ Lägg till ny bok</a></p>
    
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
             echo '<p>Inga böcker hittades i databasen.</p>';
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