<?php
include 'db.php';
session_start();

if (!isset($_GET['query']) || empty($_GET['query'])) {
    header("Location: index.php");
    exit();
}

$search_query = $_GET['query'];
$query = "SELECT * FROM movies WHERE title LIKE '%$search_query%' OR genre LIKE '%$search_query%'";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <h1>Movie Recommendation</h1>
        <a href="index.php">← Back to Home</a>
    </header>

    <div class="container">
        <h2>Search Results for "<?php echo htmlspecialchars($search_query); ?>"</h2>
        <div class="movies-row">
            <?php while ($movie = mysqli_fetch_assoc($result)): ?>
                <div class="movie-card">
                    <img src="https://en.wikipedia.org/wiki/Special:FilePath/<?php echo urlencode($movie['title']); ?>_poster.jpg" alt="<?php echo $movie['title']; ?>">
                    <h3><?php echo $movie['title']; ?></h3>
                    <p><?php echo $movie['genre']; ?> | Rating: <?php echo $movie['rating']; ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

</body>
</html>
