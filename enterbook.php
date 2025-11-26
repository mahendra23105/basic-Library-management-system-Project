<?php
include("db_connect.php");
$message = "";


if (isset($_POST['add_book'])) {
    $book_title = $_POST['book_title'];
    $author = $_POST['author'];
    $quantity = $_POST['quantity'];
    $isbn = $_POST['isbn'];
    $available = $_POST['available'];

  
    $check = "SELECT * FROM books WHERE isbn='$isbn'";
    $check_result = mysqli_query($conn, $check);

    if (mysqli_num_rows($check_result) > 0) {
        $message = "This book already exists in the database!";
    } else {
        
        $sql = "INSERT INTO books (book_title, author, quantity, isbn, available)
                VALUES ('$book_title', '$author', '$quantity', '$isbn', '$available')";
        if (mysqli_query($conn, $sql)) {
            $message = "✅ Book added successfully!";
        } else {
            $message = "Error: " . mysqli_error($conn);
        }
    }
}


if (isset($_POST['update_qty'])) {
    $id = $_POST['book_id'];
    $action = $_POST['action'];

    $get_book = mysqli_query($conn, "SELECT * FROM books WHERE id='$id'");
    $book = mysqli_fetch_assoc($get_book);
    $qty = $book['quantity'];

    if ($action == 'increase') {
        $qty++;
    } elseif ($action == 'decrease' && $qty > 0) {
        $qty--;
    }

    
    $available = ($qty > 0) ? 'Yes' : 'No';

    mysqli_query($conn, "UPDATE books SET quantity='$qty', available='$available' WHERE id='$id'");
}


$books = mysqli_query($conn, "SELECT * FROM books ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Books</title>
  <link rel="stylesheet" href="index.css">
  <style>
  body {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 40px;
    padding: 20px;
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #1e1e2f, #252540);
    color: white;
    min-height: 100vh;
  }

  .book-card {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    padding: 30px 40px;
    text-align: center;
    width: 420px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    animation: fadeIn 1s ease forwards;
  }

  .book-card img {
    width: 90px;
    margin-bottom: 15px;
    animation: pop 1s ease;
  }

  @keyframes pop {
    0% { transform: scale(0); opacity: 0; }
    80% { transform: scale(1.1); }
    100% { transform: scale(1); opacity: 1; }
  }

  h2 {
    margin-bottom: 15px;
    color: #ffcc00;
  }

  .book-card form {
    display: flex;
    flex-direction: column;
    gap: 15px;
    align-items: stretch;
  }

  .book-card input,
  .book-card select {
    padding: 10px 15px;
    border-radius: 10px;
    border: none;
    outline: none;
    font-size: 15px;
    width: 100%;
    background: rgba(255, 255, 255, 0.2);
    color: white;
  }

  .book-card input::placeholder {
    color: rgba(255, 255, 255, 0.7);
  }

  .book-card select {
    background: rgba(255, 255, 255, 0.2);
    color: white;
  }

  .book-card button {
    padding: 10px;
    background-color: #ffcc00;
    color: #000;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-weight: bold;
    font-size: 16px;
    transition: all 0.2s ease;
  }

  .book-card button:hover {
    background-color: #ffd633;
    transform: scale(1.05);
  }

  .message {
    color: #ffeb3b;
    font-weight: 500;
    margin-bottom: 10px;
  }

  .book-list {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 25px;
    margin-top: 20px;
  }

  .book-item {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 15px;
    padding: 20px;
    width: 250px;
    text-align: center;
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    transition: transform 0.2s ease;
  }

  .book-item:hover {
    transform: translateY(-5px);
  }

  .book-item h3 {
    color: #ffcc00;
    margin-bottom: 10px;
  }

  .qty-controls {
    margin-top: 10px;
  }

  .qty-controls form {
    display: inline-block;
    margin: 0 5px;
  }

  .qty-controls button {
    background-color: #ffcc00;
    border: none;
    padding: 5px 10px;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
  }

  @media (max-width: 480px) {
    .book-card {
      width: 90%;
      padding: 20px;
    }

    .book-item {
      width: 90%;
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
  }
</style>

</head>
<body>
  <div class="book-card">
    <img src="book_logo.png" alt="Book Logo">
    <h2>Add a New Book</h2>

    <?php if (!empty($message)) : ?>
      <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
      <input type="text" name="book_title" placeholder="Book Title" required>
      <input type="text" name="author" placeholder="Author Name" required>
      <input type="number" name="quantity" placeholder="Quantity" required>
      <input type="text" name="isbn" placeholder="ISBN Number" required>
      <select name="available" required>
        <option value="">-- Availability --</option>
        <option value="Yes">Available</option>
        <option value="No">Not Available</option>
      </select>
      <button type="submit" name="add_book">Add Book</button>
    </form>
  </div>

 
  <div class="book-list">
    <?php while ($row = mysqli_fetch_assoc($books)) : ?>
      <div class="book-item">
        <h3><?php echo htmlspecialchars($row['book_title']); ?></h3>
        <p><b>Author:</b> <?php echo htmlspecialchars($row['author']); ?></p>
        <p><b>ISBN:</b> <?php echo htmlspecialchars($row['isbn']); ?></p>
        <p><b>Quantity:</b> <?php echo $row['quantity']; ?></p>
        <p><b>Available:</b> <?php echo $row['available']; ?></p>

        <div class="qty-controls">
          <form method="POST" action="">
            <input type="hidden" name="book_id" value="<?php echo $row['id']; ?>">
            <input type="hidden" name="action" value="increase">
            <button type="submit" name="update_qty">+</button>
          </form>

          <form method="POST" action="">
            <input type="hidden" name="book_id" value="<?php echo $row['id']; ?>">
            <input type="hidden" name="action" value="decrease">
            <button type="submit" name="update_qty">−</button>
          </form>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
  <a href="selectuser.php" class="back-link">← Back to Portal</a>
</body>
</html>
