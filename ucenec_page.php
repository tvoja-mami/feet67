<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ucenec') {
    header("Location: index.php");
    exit();
}

// Get student's subjects
$id_ucenec = $_SESSION['user_id'];
$predmeti_query = "SELECT p.* FROM predmeti p 
                  JOIN ucenci_predmeti up ON p.id = up.id_predmet 
                  WHERE up.id_ucenec = $id_ucenec";
$predmeti_result = $conn->query($predmeti_query);

// Handle subject enrollment
if (isset($_POST['enroll_subject'])) {
    $kljuc_za_vpis = $conn->real_escape_string($_POST['enrollment_key']);
    
    // Find subject by enrollment key
    $predmet_query = "SELECT id FROM predmeti WHERE kljuc_za_vpis = '$kljuc_za_vpis'";
    $predmet_result = $conn->query($predmet_query);
    
    if ($predmet_result->num_rows > 0) {
        $predmet = $predmet_result->fetch_assoc();
        $id_predmet = $predmet['id'];
        
        // Check if already enrolled
        $check_query = "SELECT * FROM ucenci_predmeti WHERE id_ucenec = $id_ucenec AND id_predmet = $id_predmet";
        if ($conn->query($check_query)->num_rows == 0) {
            $conn->query("INSERT INTO ucenci_predmeti (id_ucenec, id_predmet) VALUES ($id_ucenec, $id_predmet)");
            $success_message = "Uspešno ste se vpisali v predmet!";
        } else {
            $error_message = "V ta predmet ste že vpisani!";
        }
    } else {
        $error_message = "Neveljaven ključ za vpis!";
    }
}
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Učenec: Moji predmeti</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .enrollment-form {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .enrollment-form input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .enrollment-form button {
            background: #7494ec;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .enrollment-form button:hover {
            background: #6884d3;
        }
        .subjects-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .subjects-table th,
        .subjects-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .subjects-table th {
            background: #f8f9fa;
            font-weight: bold;
        }
        .button {
            display: inline-block;
            padding: 6px 12px;
            margin: 0 4px;
            background: #7494ec;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
        }
        .button:hover {
            background: #6884d3;
        }
    </style>
</head>

<body style="background: #f5f7fb;">
    <div class="box">
        <h1>Pozdravljeni, <span><?= htmlspecialchars($_SESSION['name']); ?></span></h1>
        
        <?php if (isset($success_message)): ?>
            <div class="success-message"><?= $success_message ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?= $error_message ?></div>
        <?php endif; ?>

        <div class="enrollment-form">
            <h2>Vpis v predmet</h2>
            <form method="POST">
                <div>
                    <input type="text" name="enrollment_key" placeholder="Vpišite kodo predmeta (npr. MAT3-2024)" required>
                </div>
                <button type="submit" name="enroll_subject">Vpiši se v predmet</button>
            </form>
            <p style="margin-top: 10px; color: #666; font-size: 14px;">
                Vnesite kodo predmeta, ki vam jo je posredoval učitelj.
            </p>
        </div>

        <?php if ($predmeti_result && $predmeti_result->num_rows > 0): ?>
            <div style="max-width: 800px; margin: 20px auto;">
                <h3>Moji predmeti</h3>
                <table class="subjects-table">
                    <thead>
                        <tr>
                            <th>Ime predmeta</th>
                            <th>Opis</th>
                            <th>Akcije</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($predmet = $predmeti_result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($predmet['ime']) ?></td>
                                <td><?= htmlspecialchars($predmet['opis']) ?></td>
                                <td>
                                    <a href="gradiva.php?predmet=<?= $predmet['id'] ?>" class="button">Gradiva</a>
                                    <a href="naloge.php?predmet=<?= $predmet['id'] ?>" class="button">Naloge</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="info-message">Trenutno niste vpisani v noben predmet. Uporabite kodo za vpis v predmet zgoraj.</p>
        <?php endif; ?>

        <button onclick="window.location.href='logout.php'" style="margin: 20px auto;">Odjava</button>
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
        .info-message {
            background: #e2e3e5;
            color: #383d41;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
            text-align: center;
        }
    </style>
</body>
</html>