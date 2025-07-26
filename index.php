<?php
include 'db.php';
session_start();

// Fetch all genres
$genres_query = "SELECT DISTINCT genre FROM movies WHERE genre IS NOT NULL";
$genres_result = mysqli_query($conn, $genres_query);
$genres = [];

while ($row = mysqli_fetch_assoc($genres_result)) {
    $genres[] = $row['genre'];
}

// Fetch trending movies (latest added)
$trending_query = "SELECT * FROM movies ORDER BY release_year DESC LIMIT 6";
$trending_result = mysqli_query($conn, $trending_query);

// Fetch top-rated movies
$top_rated_query = "SELECT * FROM movies ORDER BY rating DESC LIMIT 6";
$top_rated_result = mysqli_query($conn, $top_rated_query);

// Fetch personalized recommendations if logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $recommend_query = "SELECT * FROM movies WHERE genre IN 
                        (SELECT genre FROM user_watched WHERE user_id='$user_id') LIMIT 6";
    $recommend_result = mysqli_query($conn, $recommend_query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Recommendation</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <h1>🎬 Movie Recommendation</h1>
        <nav>
            <a href="index.php">Home</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="logout.php">Logout (<?php echo $_SESSION['username']; ?>)</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </nav>

        <!-- Centered Search Bar -->
        <div class="search-container">
            <form method="GET" action="search.php" class="search-form">
                <input type="text" name="query" placeholder="🔍 Search movies..." required>
                <button type="submit">Search</button>
            </form>
        </div>
    </header>

    <div class="container">

        <!-- Trending Movies -->
        <h2>🔥 Trending Now</h2>
        <div class="movies-row">
            <?php while ($movie = mysqli_fetch_assoc($trending_result)): ?>
                <div class="movie-card">
                    <img src="<?php echo !empty($movie['poster']) ? $movie['poster'] : 'fallback.jpg'; ?>" 
                         alt="<?php echo $movie['title']; ?>" 
                         onerror="this.onerror=null; this.src='fallback.jpg';">
                    <h3><?php echo $movie['title']; ?></h3>
                    <p><?php echo $movie['genre']; ?> | ⭐ <?php echo $movie['rating']; ?></p>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Top Rated Movies -->
        <h2>⭐ Top Rated</h2>
        <div class="movies-row">
            <?php while ($movie = mysqli_fetch_assoc($top_rated_result)): ?>
                <div class="movie-card">
                    <img src="<?php echo !empty($movie['poster']) ? $movie['poster'] : 'fallback.jpg'; ?>" 
                         alt="<?php echo $movie['title']; ?>" 
                         onerror="this.onerror=null; this.src='fallback.jpg';">
                    <h3><?php echo $movie['title']; ?></h3>
                    <p><?php echo $movie['genre']; ?> | ⭐ <?php echo $movie['rating']; ?></p>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Personalized Recommendations -->
        <?php if (isset($_SESSION['user_id']) && mysqli_num_rows($recommend_result) > 0): ?>
            <h2>🎯 Recommended for You</h2>
            <div class="movies-row">
                <?php while ($rec_movie = mysqli_fetch_assoc($recommend_result)): ?>
                    <div class="movie-card">
                        <img src="<?php echo !empty($rec_movie['poster']) ? $rec_movie['poster'] : 'fallback.jpg'; ?>" 
                             alt="<?php echo $rec_movie['title']; ?>" 
                             onerror="this.onerror=null; this.src='fallback.jpg';">
                        <h3><?php echo $rec_movie['title']; ?></h3>
                        <p><?php echo $rec_movie['genre']; ?> | ⭐ <?php echo $rec_movie['rating']; ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>

        <!-- Movies by Genre -->
        <?php if (!empty($genres)): ?>
            <?php foreach ($genres as $genre): ?>
                <h2>🎭 <?php echo ucfirst($genre); ?></h2>
                <div class="movies-row">
                    <?php
                    $movies_query = "SELECT * FROM movies WHERE genre='$genre' LIMIT 6";
                    $movies_result = mysqli_query($conn, $movies_query);
                    
                    if (mysqli_num_rows($movies_result) > 0):
                        while ($movie = mysqli_fetch_assoc($movies_result)):
                    ?>
                        <div class="movie-card">
                            <img src="<?php echo !empty($movie['poster']) ? $movie['poster'] : 'fallback.jpg'; ?>" 
                                 alt="<?php echo $movie['title']; ?>" 
                                 onerror="this.onerror=null; this.src='fallback.jpg';">
                            <h3><?php echo $movie['title']; ?></h3>
                            <p><?php echo $movie['genre']; ?> | ⭐ <?php echo $movie['rating']; ?></p>
                        </div>
                    <?php endwhile; ?>
                    <?php else: ?>
                        <p>No movies found in this category.</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</body>
</html>
