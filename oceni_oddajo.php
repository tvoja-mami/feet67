<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ucitelj') {
    header("Location: index.php");
    exit();
}

$id_oddaja = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$id_ucitelj = $_SESSION['user_id'];

// Get submission details
$oddaja_query = "SELECT o.*, u.ime, u.priimek, n.naslov as naslov_naloge, p.ime as ime_predmeta
                 FROM oddaje o 
                 JOIN uporabniki u ON o.id_ucenec = u.id 
                 JOIN naloge n ON o.id_naloga = n.id
                 JOIN predmeti p ON n.id_predmet = p.id
                 JOIN ucitelji_predmeti up ON p.id = up.id_predmet
                 WHERE o.id = $id_oddaja AND up.id_ucitelj = $id_ucitelj";
$oddaja_result = $conn->query($oddaja_query);
$oddaja = $oddaja_result->fetch_assoc();

if (!$oddaja) {
    header("Location: profesor_page_new.php");
    exit();
}

if (isset($_POST['submit_grade'])) {
    $ocena = (int)$_POST['ocena'];
    $komentar = $conn->real_escape_string($_POST['komentar']);
    
    $sql = "UPDATE oddaje SET ocena = $ocena, komentar_ucitelja = '$komentar' WHERE id = $id_oddaja";
    if ($conn->query($sql)) {
        $success_message = "Ocena je bila uspešno dodana!";
    } else {
        $error_message = "Napaka pri dodajanju ocene: " . $conn->error;
    }
    
    // Refresh oddaja data
    $oddaja_result = $conn->query($oddaja_query);
    $oddaja = $oddaja_result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oceni oddajo</title>
    <link rel="stylesheet" href="style.css">
</head>
<body style="background: #f5f7fb;">
    <div class="box">
        <h1>Oceni oddajo</h1>
        
        <?php if (isset($success_message)): ?>
            <div class="success-message"><?= $success_message ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?= $error_message ?></div>
        <?php endif; ?>

        <div class="submission-details" style="margin-bottom: 20px;">
            <p><strong>Učenec:</strong> <?= htmlspecialchars($oddaja['ime'] . ' ' . $oddaja['priimek']) ?></p>
            <p><strong>Predmet:</strong> <?= htmlspecialchars($oddaja['ime_predmeta']) ?></p>
            <p><strong>Naloga:</strong> <?= htmlspecialchars($oddaja['naslov_naloge']) ?></p>
            <p><strong>Oddano:</strong> <?= date('d.m.Y H:i', strtotime($oddaja['oddano_ob'])) ?></p>
            <p>
                <a href="<?= htmlspecialchars($oddaja['pot_datoteke']) ?>" 
                   download="<?= htmlspecialchars($oddaja['izvirno_ime_datoteke']) ?>"
                   class="button">Prenesi oddajo</a>
            </p>
        </div>

        <div class="grade-form">
            <form method="POST">
                <div class="form-group">
                    <label for="ocena">Ocena:</label>
                    <input type="number" name="ocena" id="ocena" min="1" max="5" 
                           value="<?= htmlspecialchars($oddaja['ocena'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="komentar">Komentar:</label>
                    <textarea name="komentar" id="komentar" rows="4" style="width: 100%;"><?= htmlspecialchars($oddaja['komentar_ucitelja'] ?? '') ?></textarea>
                </div>

                <button type="submit" name="submit_grade">Shrani oceno</button>
            </form>
        </div>

        <button onclick="window.location.href='profesor_page_new.php'" style="margin: 20px auto;">Nazaj</button>
    </div>

    <style>
        .grade-form {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .submission-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .submission-details p {
            margin: 10px 0;
        }
        .button {
            display: inline-block;
            padding: 8px 16px;
            background: #7494ec;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .button:hover {
            background: #6884d3;
        }
    </style>
</body>
</html>