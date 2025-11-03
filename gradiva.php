<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$id_predmet = isset($_GET['predmet']) ? (int)$_GET['predmet'] : 0;
$id_user = $_SESSION['user_id'];

// Verify that a valid subject ID was provided
if ($id_predmet <= 0) {
    $_SESSION['error_message'] = "Prosim izberite predmet.";
    header("Location: " . ($_SESSION['role'] === 'ucitelj' ? 'profesor_page_new.php' : 'ucenec_page.php'));
    exit();
}

// Get subject info and verify access
$predmet_query = "";
if ($_SESSION['role'] === 'ucitelj') {
    $predmet_query = "SELECT p.* FROM predmeti p 
                     JOIN ucitelji_predmeti up ON p.id = up.id_predmet 
                     WHERE p.id = $id_predmet AND up.id_ucitelj = $id_user";
} else {
    $predmet_query = "SELECT p.* FROM predmeti p 
                     JOIN ucenci_predmeti ep ON p.id = ep.id_predmet 
                     WHERE p.id = $id_predmet AND ep.id_ucenec = $id_user";
}

$predmet_result = $conn->query($predmet_query);
$predmet = $predmet_result->fetch_assoc();

// Check if subject exists and user has access
if (!$predmet) {
    $_SESSION['error_message'] = "Predmet ne obstaja ali nimate dostopa do njega.";
    header("Location: " . ($_SESSION['role'] === 'ucitelj' ? 'profesor_page_new.php' : 'ucenec_page.php'));
    exit();
}

// Handle file upload
if ($_SESSION['role'] === 'ucitelj' && isset($_POST['upload'])) {
    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];
        $naslov = $conn->real_escape_string($_POST['naslov']);
        
        // Create uploads directory if it doesn't exist
        $upload_dir = "uploads/gradiva/$id_predmet";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $filename = uniqid() . '_' . basename($file['name']);
        // Pot za shranjevanje v datotečni sistem
        $file_system_path = $upload_dir . '/' . $filename;
        // Pot za shranjevanje v bazo in prikaz v brskalniku
        $web_path = '/' . $upload_dir . '/' . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $file_system_path)) {
            $sql = "INSERT INTO gradiva (id_predmet, naslov, pot_datoteke, izvirno_ime_datoteke) 
                    VALUES (" . $id_predmet . ", '" . $naslov . "', '" . $web_path . "', '" . $conn->real_escape_string($file['name']) . "')";
            if ($conn->query($sql)) {
                $success_message = "Gradivo uspešno naloženo!";
            } else {
                $error_message = "Napaka pri shranjevanju v bazo: " . $conn->error;
            }
        } else {
            $error_message = "Napaka pri nalaganju datoteke!";
        }
    }
}

// Get materials list
$gradiva_query = "";
if ($_SESSION['role'] === 'ucitelj') {
    $gradiva_query = "SELECT g.* FROM gradiva g
                      JOIN ucitelji_predmeti up ON g.id_predmet = up.id_predmet
                      WHERE g.id_predmet = $id_predmet 
                      AND up.id_ucitelj = $id_user
                      ORDER BY g.nalozen_ob DESC";
} else {
    $gradiva_query = "SELECT g.* FROM gradiva g
                      JOIN ucenci_predmeti ep ON g.id_predmet = ep.id_predmet
                      WHERE g.id_predmet = $id_predmet 
                      AND ep.id_ucenec = $id_user
                      ORDER BY g.nalozen_ob DESC";
}
$gradiva_result = $conn->query($gradiva_query);
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gradiva: <?= htmlspecialchars($predmet['ime']) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body style="background: #f5f7fb;">
    <div class="box">
        <h1><?= htmlspecialchars($predmet['ime']) ?> - Gradiva</h1>
        
        <?php if (isset($success_message)): ?>
            <div class="success-message"><?= $success_message ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?= $error_message ?></div>
        <?php endif; ?>

        <?php if ($_SESSION['role'] === 'ucitelj'): ?>
            <div class="form-box">
                <h2>Naloži novo gradivo</h2>
                <form method="POST" enctype="multipart/form-data">
                    <input type="text" name="naslov" placeholder="Naslov gradiva" required>
                    <input type="file" name="file" required style="margin-bottom: 20px;">
                    <button type="submit" name="upload">Naloži</button>
                </form>
            </div>
        <?php endif; ?>

        <?php if ($gradiva_result && $gradiva_result->num_rows > 0): ?>
            <div style="max-width: 800px; margin: 20px auto;">
                <table class="materials-table">
                    <thead>
                        <tr>
                            <th>Naslov</th>
                            <th>Datoteka</th>
                            <th>Naloženo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($gradivo = $gradiva_result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($gradivo['naslov']) ?></td>
                                <td>
                                    <a href="./uploads/gradiva/<?= htmlspecialchars($gradivo['pot_datoteke']) ?>" 
                                       download="<?= htmlspecialchars($gradivo['izvirno_ime_datoteke']) ?>"
                                       class="button">
                                        Prenesi
                                    </a>
                                </td>
                                <td><?= date('d.m.Y H:i', strtotime($gradivo['nalozen_ob'])) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="info-message">Trenutno ni naloženih gradiv.</p>
        <?php endif; ?>

        <button onclick="window.location.href='<?= $_SESSION['role'] === 'ucitelj' ? 'profesor_page_new.php' : 'ucenec_page.php' ?>'" 
                style="margin: 20px auto;">
            Nazaj na glavno stran
        </button>
    </div>

    <style>
        .materials-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .materials-table th {
            background: #7494ec;
            color: white;
            padding: 12px;
            text-align: left;
        }
        .materials-table td {
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
        }
        .button:hover {
            background: #6884d3;
        }
        input[type="file"] {
            width: 100%;
            padding: 8px;
            background: #eee;
            border-radius: 4px;
        }
    </style>
</body>
</html>
