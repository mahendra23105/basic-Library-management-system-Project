<?php
include("db_connect.php");
session_start();


if (!isset($_SESSION['student_username'])) {
  // header("Location: student_login_register.php");
  // exit();
}
$message = "";


if (isset($_POST['borrow'])) {
  $book_id = $_POST['book_id'];


  $book_query = mysqli_query($conn, "SELECT * FROM books WHERE id='$book_id'");
  $book = mysqli_fetch_assoc($book_query);

  if ($book && $book['quantity'] > 0) {
    $new_qty = $book['quantity'] - 1;
    $available = ($new_qty > 0) ? 'Yes' : 'No';


    mysqli_query($conn, "UPDATE books SET quantity='$new_qty', available='$available' WHERE id='$book_id'");


    $student = $_SESSION['student_username'] ?? 'Guest';
    mysqli_query($conn, "INSERT INTO borrowed_books (student_username, book_title, borrow_date)
                             VALUES ('$student', '{$book['book_title']}', NOW())");

    $message = "✅ You successfully borrowed '{$book['book_title']}'!";
  } else {
    $message = "❌ Sorry, this book is not available.";
  }
}


$sql = "SELECT * FROM books ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Available Books</title>
  <link rel="stylesheet" href="index.css">
  <style>
    body {
      flex-direction: column;
      gap: 20px;
      overflow-y: auto;
      padding: 30px 10px;
      text-align: center;
      font-family: "Poppins", sans-serif;
    }

    h2 {
      text-align: center;
      margin-bottom: 10px;
      font-size: 1.8rem;
      color: #ffcc00;
    }

    .message {
      color: yellow;
      font-weight: 600;
      margin-bottom: 15px;
    }

    .book-list {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 25px;
    }

    .book-item {
      background: rgba(255, 255, 255, 0.1);
      border-radius: 15px;
      padding: 25px;
      width: 250px;
      text-align: center;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
      transition: transform 0.3s ease, background 0.3s ease;
    }

    .book-item:hover {
      transform: translateY(-5px);
    }

    .book-item img {
      width: 80px;
      margin-bottom: 10px;
    }

    .book-item h3 {
      color: #ffcc00;
      margin-bottom: 8px;
    }

    .book-item p {
      margin-bottom: 5px;
      font-size: 0.95rem;
    }

    .available {
      color: #00ff99;
      font-weight: bold;
    }

    .not-available {
      color: #ff4d4d;
      font-weight: bold;
    }


    .book-item.not-available-card {
      background: rgba(255, 50, 50, 0.2);
      box-shadow: 0 6px 20px rgba(255, 0, 0, 0.4);
    }

    .borrow-btn {
      background: #ffcc00;
      color: #1e3c72;
      border: none;
      padding: 8px 16px;
      border-radius: 8px;
      cursor: pointer;
      font-weight: bold;
      margin-top: 8px;
    }

    .borrow-btn:disabled {
      background: #777;
      color: white;
      cursor: not-allowed;
    }

    .back-link {
      display: block;
      text-align: center;
      margin-top: 25px;
      color: #ffcc00;
      text-decoration: none;
      font-weight: 500;
    }

    .back-link:hover {
      text-decoration: none;
    }
  </style>
</head>

<body>
  <h2>📚 Available Books in Library</h2>

  <?php if (!empty($message)) : ?>
    <p class="message"><?php echo $message; ?></p>
  <?php endif; ?>

  <div class="book-list">
    <?php while ($row = mysqli_fetch_assoc($result)) :
      $isAvailable = ($row['quantity'] > 0);
    ?>
      <div class="book-item <?php echo $isAvailable ? '' : 'not-available-card'; ?>">
        <img src="book_logo.png" alt="Book Logo">
        <h3><?php echo htmlspecialchars($row['book_title']); ?>
        </h3>
        <p><b>Author:</b> <?php echo htmlspecialchars($row['author']); ?></p>
        <p><b>ISBN:</b> <?php echo htmlspecialchars($row['isbn']); ?></p>
        <p><b>Quantity:</b> <?php echo $row['quantity']; ?></p>

        <?php if ($isAvailable): ?>
          <p class="available">✅ Available</p>
        <?php else: ?>
          <p class="not-available">❌ Not Available</p>
        <?php endif; ?>

        <form method="POST" action="">
          <input type="hidden" name="book_id" value="<?php echo $row['id']; ?>">
          <button type="submit" name="borrow" class="borrow-btn" <?php echo !$isAvailable ? 'disabled' : ''; ?>>
            <?php echo $isAvailable ? 'Borrow' : 'Unavailable'; ?>
          </button>
        </form>
      </div>
    <?php endwhile; ?>
  </div>

  <a href="selectuser.php" class="back-link">← Back to Student Portal</a>
</body>

</html>