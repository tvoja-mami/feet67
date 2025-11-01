<?php
session_start();
require_once 'config.php';

// Check if user is logged in and is a teacher
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'ucitelj') {
    header("Location: index.php");
    exit();
}

// Handle subject creation
if (isset($_POST['add_subject'])) {
    $ime = $conn->real_escape_string($_POST['ime']);
    $opis = $conn->real_escape_string($_POST['opis']);
    $kljuc_za_vpis = substr(md5(uniqid(rand(), true)), 0, 8); // Generate random 8-char key

    $sql = "INSERT INTO predmeti (ime, opis, kljuc_za_vpis) VALUES ('$ime', '$opis', '$kljuc_za_vpis')";
    if ($conn->query($sql)) {
        // Link the teacher to the subject
        $id_predmet = $conn->insert_id;
        $id_ucitelj = $_SESSION['user_id'];
        $conn->query("INSERT INTO ucitelji_predmeti (id_ucitelj, id_predmet) VALUES ($id_ucitelj, $id_predmet)");
        $success_message = "Predmet uspešno dodan!";
    } else {
        $error_message = "Napaka pri dodajanju predmeta: " . $conn->error;
    }
}

// Get teacher's subjects
$id_ucitelj = $_SESSION['user_id'];
$subjects_query = "SELECT p.* FROM predmeti p 
                  JOIN ucitelji_predmeti up ON p.id = up.id_predmet 
                  WHERE up.id_ucitelj = $id_ucitelj";
$subjects_result = $conn->query($subjects_query);
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upravljanje predmetov</title>
    <link rel="stylesheet" href="style.css">
</head>
<body style="background: #f5f7fb;">
    <div class="box">
        <h1>Predmeti</h1>
        
        <?php if (isset($success_message)): ?>
            <div class="success-message"><?= $success_message ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?= $error_message ?></div>
        <?php endif; ?>

        <div class="form-box">
            <h2>Dodaj nov predmet</h2>
            <form method="POST">
                <input type="text" name="ime" placeholder="Ime predmeta" required>
                <textarea name="opis" placeholder="Opis predmeta" rows="4" style="width: 100%; margin-bottom: 20px;"></textarea>
                <button type="submit" name="add_subject">Dodaj predmet</button>
            </form>
        </div>

        <?php if ($subjects_result && $subjects_result->num_rows > 0): ?>
            <div style="max-width: 800px; margin: 20px auto;">
                <h3>Vaši predmeti</h3>
                <table class="subjects-table">
                    <thead>
                        <tr>
                            <th>Ime predmeta</th>
                            <th>Opis</th>
                            <th>Koda za vpis</th>
                            <th>Akcije</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($subject = $subjects_result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($subject['ime']) ?></td>
                                <td><?= htmlspecialchars($subject['opis']) ?></td>
                                <td><?= htmlspecialchars($subject['kljuc_za_vpis']) ?></td>
                                <td>
                                    <a href="gradiva.php?predmet=<?= $subject['id'] ?>" class="button">Gradiva</a>
                                    <a href="naloge.php?predmet=<?= $subject['id'] ?>" class="button">Naloge</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <button onclick="window.location.href='profesor_page_new.php'" style="margin: 20px auto;">Nazaj na glavno stran</button>
    </div>

    <style>
        .subjects-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .subjects-table th {
            background: #7494ec;
            color: white;
            padding: 12px;
            text-align: left;
        }
        .subjects-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        .button {
            display: inline-block;
            padding: 8px 16px;
            background: #7494ec;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-right: 8px;
        }
        .button:hover {
            background: #6884d3;
        }
        textarea {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            resize: vertical;
        }
    </style>
</body>
</html>