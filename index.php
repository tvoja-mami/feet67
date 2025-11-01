<?php
session_start();

$error = $_SESSION['login_error'] ?? '';
session_unset();
?>

<!DOCTYPE html>
<html lang="sl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prijava v sistem</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <div class="form-box active">
            <form action="login_register.php" method="post">
                <h2>Prijava v sistem</h2>
                <?php if (!empty($error)): ?>
                    <p class="error-message"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Geslo" required>
                <button type="submit" name="login">Prijava</button>
                <p class="info-text" style="text-align: center; color: #666; margin-top: 20px;">Za dostop do sistema kontaktirajte administratorja.</p>
            </form>
        </div>
    </div>

    </div>
    <script src="script.js"></script>
</body>

</html>
