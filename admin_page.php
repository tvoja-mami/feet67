<?php
session_start();
require_once 'config.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Get statistics
$stats = [
    'users' => $conn->query("SELECT COUNT(*) as count, vloga FROM uporabniki GROUP BY vloga")->fetch_all(MYSQLI_ASSOC),
    'subjects' => $conn->query("SELECT COUNT(*) as count FROM predmeti")->fetch_assoc()['count'],
    'materials' => $conn->query("SELECT COUNT(*) as count FROM gradiva")->fetch_assoc()['count'],
    'assignments' => $conn->query("SELECT COUNT(*) as count FROM naloge")->fetch_assoc()['count']
];

// Handle subject creation
if (isset($_POST['add_subject'])) {
    $subject_name = $conn->real_escape_string($_POST['subject_name']);
    $subject_description = $conn->real_escape_string($_POST['subject_description']);
    $enrollment_code = $conn->real_escape_string($_POST['enrollment_code']);
    
    $sql = "INSERT INTO predmeti (ime, opis, kljuc_za_vpis) VALUES ('$subject_name', '$subject_description', '$enrollment_code')";
    if ($conn->query($sql)) {
        $success_message = "Nov predmet uspešno dodan.";
    } else {
        $error_message = "Napaka pri dodajanju predmeta.";
    }
}

// Handle teacher subject rights
if (isset($_POST['toggle_subject_right'])) {
    $teacher_id = $conn->real_escape_string($_POST['teacher_id']);
    $subject_id = $conn->real_escape_string($_POST['subject_id']);
    $action = $_POST['action']; // 'add' or 'remove'
    
    if ($action === 'add') {
        $sql = "INSERT INTO ucitelji_predmeti (id_ucitelj, id_predmet) VALUES ($teacher_id, $subject_id)";
    } else {
        $sql = "DELETE FROM ucitelji_predmeti WHERE id_ucitelj = $teacher_id AND id_predmet = $subject_id";
    }
    
    if ($conn->query($sql)) {
        $success_message = $action === 'add' ? "Pravice za predmet dodane." : "Pravice za predmet odvzete.";
    } else {
        $error_message = "Napaka pri spreminjanju pravic.";
    }
}

// Handle user addition
if (isset($_POST['add_user'])) {
    $ime = $conn->real_escape_string($_POST['ime']);
    $priimek = $conn->real_escape_string($_POST['priimek']);
    $email = $conn->real_escape_string($_POST['email']);
    $uporabnisko_ime = $conn->real_escape_string($_POST['uporabnisko_ime']);
    $geslo = password_hash($_POST['geslo'], PASSWORD_DEFAULT);
    $vloga = $conn->real_escape_string($_POST['vloga']);
    $razred = $vloga === 'ucenec' ? $conn->real_escape_string($_POST['razred']) : null;

    // Check if email or username already exists
    $check_query = "SELECT id FROM uporabniki WHERE email = '$email' OR uporabnisko_ime = '$uporabnisko_ime'";
    $check_result = $conn->query($check_query);
    
    if ($check_result->num_rows > 0) {
        $error_message = "Uporabnik s tem emailom ali uporabniškim imenom že obstaja.";
    } else {
        $sql = "INSERT INTO uporabniki (uporabnisko_ime, geslo, ime, priimek, email, vloga, razred) 
                VALUES ('$uporabnisko_ime', '$geslo', '$ime', '$priimek', '$email', '$vloga', " . 
                ($razred ? "'$razred'" : "NULL") . ")";
        
        if ($conn->query($sql)) {
            $success_message = "Nov uporabnik uspešno dodan. ID: " . $conn->insert_id;
            
            // Double check the inserted user
            $check_inserted = $conn->query("SELECT * FROM uporabniki WHERE id = " . $conn->insert_id);
            if ($check_inserted && $check_inserted->num_rows > 0) {
                $inserted_user = $check_inserted->fetch_assoc();
                $success_message .= "<br>Podatki: " . 
                    "Ime: " . htmlspecialchars($inserted_user['ime']) . 
                    ", Email: " . htmlspecialchars($inserted_user['email']) . 
                    ", Vloga: " . htmlspecialchars($inserted_user['vloga']);
                
                // Add JavaScript to refresh the page after showing the message
                echo "<script>
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000); // Refresh after 2 seconds
                </script>";
            }
            
            // Refresh users list after successful addition
            $users_query = "SELECT * FROM uporabniki ORDER BY vloga, ime, priimek";
            $users_result = $conn->query($users_query);
        } else {
            $error_message = "Napaka pri dodajanju uporabnika: " . $conn->error;
        }
    }
}

// Handle user deletion if requested
if (isset($_POST['delete_user'])) {
    $user_id = (int)$_POST['user_id'];
    if ($conn->query("DELETE FROM uporabniki WHERE id = $user_id")) {
        $success_message = "Uporabnik je bil uspešno izbrisan.";
    } else {
        $error_message = "Napaka pri brisanju uporabnika: " . $conn->error;
    }
}

// Get users by role with specific sorting
$admin_query = "SELECT * FROM uporabniki WHERE vloga = 'admin' ORDER BY ime, priimek";
$teacher_query = "SELECT * FROM uporabniki WHERE vloga = 'ucitelj' ORDER BY ime, priimek";
$student_query = "SELECT * FROM uporabniki WHERE vloga = 'ucenec' 
                 ORDER BY CAST(SUBSTRING_INDEX(razred, '.', 1) AS SIGNED),
                 SUBSTRING_INDEX(razred, '.', -1),
                 ime, priimek";

$admin_result = $conn->query($admin_query);
$teacher_result = $conn->query($teacher_query);
$student_result = $conn->query($student_query);
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administratorska nadzorna plošča</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .subject-list {
            display: flex;
            flex-wrap: wrap;
            gap: 3px;
            margin-bottom: 5px;
        }
        .subject-tag {
            background: #e9ecef;
            padding: 2px 6px;
            border-radius: 3px;
            display: inline-flex;
            align-items: center;
            gap: 3px;
            font-size: 0.85em;
        }
        .remove-subject {
            background: none;
            border: none;
            color: #dc3545;
            cursor: pointer;
            padding: 0 2px;
            font-size: 14px;
            line-height: 1;
        }
        .remove-subject:hover {
            color: #bd2130;
        }
        .add-subject {
            margin-top: 3px;
        }
        .add-subject select {
            padding: 2px 4px;
            border: 1px solid #ddd;
            border-radius: 3px;
            margin-right: 3px;
            font-size: 0.9em;
        }
        .add-subject button {
            padding: 2px 6px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 0.9em;
        }
        .add-subject button:hover {
            background: #218838;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-card h3 {
            margin: 0 0 10px 0;
            color: #666;
        }
        .stat-card .number {
            font-size: 24px;
            font-weight: bold;
            color: #7494ec;
        }
        .users-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .users-table th,
        .users-table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #eee;
            font-size: 0.95em;
            line-height: 1.3;
        }
        .users-table th {
            background: #f8f9fa;
            font-weight: 600;
            padding: 8px;
        }
        .users-table tr:hover {
            background-color: #f8f9fa;
        }
        .delete-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 4px 8px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 0.9em;
        }
        .delete-btn:hover {
            background: #c82333;
        }
    </style>
</head>
<body style="background: #f5f7fb;">
    <div class="box">
        <h1>Administratorska nadzorna plošča</h1>
        
        <?php if (isset($success_message)): ?>
            <div class="success-message"><?= $success_message ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?= $error_message ?></div>
        <?php endif; ?>

        <!-- Statistics -->
        <div class="stats-grid">
            <?php foreach ($stats['users'] as $stat): ?>
                <div class="stat-card">
                    <h3><?= ucfirst($stat['vloga']) ?>i</h3>
                    <div class="number"><?= $stat['count'] ?></div>
                </div>
            <?php endforeach; ?>
            <div class="stat-card">
                <h3>Predmeti</h3>
                <div class="number"><?= $stats['subjects'] ?></div>
            </div>
            <div class="stat-card">
                <h3>Gradiva</h3>
                <div class="number"><?= $stats['materials'] ?></div>
            </div>
            <div class="stat-card">
                <h3>Naloge</h3>
                <div class="number"><?= $stats['assignments'] ?></div>
            </div>
        </div>

        <!-- Subject Dashboard -->
        <div class="add-user-section" style="max-width: 1000px; margin: 30px auto; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2 style="margin-top: 0; color: #333; margin-bottom: 20px;">Pregled predmetov</h2>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <thead>
                    <tr>
                        <th style="text-align: left; padding: 12px; border-bottom: 2px solid #ddd;">Ime predmeta</th>
                        <th style="text-align: left; padding: 12px; border-bottom: 2px solid #ddd;">Opis</th>
                        <th style="text-align: left; padding: 12px; border-bottom: 2px solid #ddd;">Koda za vpis</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $subjects_query = "SELECT * FROM predmeti ORDER BY ime";
                    $subjects_result = $conn->query($subjects_query);
                    while ($subject = $subjects_result->fetch_assoc()):
                    ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 12px;"><?= htmlspecialchars($subject['ime']) ?></td>
                        <td style="padding: 12px;"><?= htmlspecialchars($subject['opis']) ?></td>
                        <td style="padding: 12px;">
                            <span style="font-family: monospace; background: #e9ecef; padding: 4px 8px; border-radius: 4px;">
                                <?= htmlspecialchars($subject['kljuc_za_vpis']) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Add New Subject Form -->
        <div class="add-user-section" style="max-width: 600px; margin: 30px auto; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2 style="margin-top: 0; color: #333; margin-bottom: 20px;">Dodaj nov predmet</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Ime predmeta:</label>
                    <input type="text" name="subject_name" required style="width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <div class="form-group">
                    <label>Opis predmeta:</label>
                    <textarea name="subject_description" rows="3" style="width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px;"></textarea>
                </div>
                <div class="form-group">
                    <label>Koda za vpis (8 znakov):</label>
                    <input type="text" name="enrollment_code" required maxlength="8" pattern=".{8,8}" title="Koda mora biti dolga točno 8 znakov" style="width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px; font-family: monospace;">
                </div>
                <button type="submit" name="add_subject" style="width: 100%; padding: 10px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px;">
                    Dodaj predmet
                </button>
            </form>
        </div>

        <!-- Add New User Form -->
        <div class="add-user-section" style="max-width: 600px; margin: 30px auto; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2 style="margin-top: 0; color: #333; margin-bottom: 20px;">Dodaj novega uporabnika</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Ime:</label>
                    <input type="text" name="ime" required style="width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <div class="form-group">
                    <label>Priimek:</label>
                    <input type="text" name="priimek" required style="width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" required style="width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <div class="form-group">
                    <label>Uporabniško ime:</label>
                    <input type="text" name="uporabnisko_ime" required style="width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <div class="form-group">
                    <label>Geslo:</label>
                    <input type="password" name="geslo" required style="width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <div class="form-group">
                    <label>Vloga:</label>
                    <select name="vloga" required onchange="toggleRazred(this.value)" style="width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="">Izberi vlogo</option>
                        <option value="ucitelj">Učitelj</option>
                        <option value="ucenec">Učenec</option>
                    </select>
                </div>
                <div class="form-group" id="razred-group" style="display: none;">
                    <label>Razred:</label>
                    <input type="text" name="razred" maxlength="3" placeholder="npr. 3.A" style="width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <button type="submit" name="add_user" style="width: 100%; padding: 10px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px;">
                    Dodaj uporabnika
                </button>
            </form>
        </div>

        <!-- Subject Management -->
        <div class="form-box" style="max-width: 600px; margin: 0 auto 30px auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2>Dodaj nov predmet</h2>
            <form method="POST" style="display: grid; gap: 15px;">
                <div class="form-group">
                    <label>Ime predmeta:</label>
                    <input type="text" name="subject_name" required>
                </div>
                <div class="form-group">
                    <label>Opis predmeta:</label>
                    <textarea name="subject_description" rows="3" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;"></textarea>
                </div>
                <button type="submit" name="add_subject" style="background: #28a745; color: white; border: none; padding: 10px; border-radius: 4px; cursor: pointer;">
                    Dodaj predmet
                </button>
            </form>
        </div>

        <!-- Users List -->
        <!-- Add User Form -->
        <div class="form-box" style="max-width: 600px; margin: 0 auto 30px auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2>Dodaj novega uporabnika</h2>
            <form method="POST" style="display: grid; gap: 15px;">
                <div class="form-group">
                    <label>Ime:</label>
                    <input type="text" name="ime" required>
                </div>
                <div class="form-group">
                    <label>Priimek:</label>
                    <input type="text" name="priimek" required>
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Uporabniško ime:</label>
                    <input type="text" name="uporabnisko_ime" required>
                </div>
                <div class="form-group">
                    <label>Geslo:</label>
                    <input type="password" name="geslo" required>
                </div>
                <div class="form-group">
                    <label>Vloga:</label>
                    <select name="vloga" required onchange="toggleRazred(this.value)">
                        <option value="">Izberi vlogo</option>
                        <option value="ucitelj">Učitelj</option>
                        <option value="ucenec">Učenec</option>
                    </select>
                </div>
                <div class="form-group" id="razred-group" style="display: none;">
                    <label>Razred:</label>
                    <input type="text" name="razred" maxlength="3" placeholder="npr. 3.A">
                </div>
                <button type="submit" name="add_user" style="background: #28a745; color: white; border: none; padding: 10px; border-radius: 4px; cursor: pointer;">
                    Dodaj uporabnika
                </button>
            </form>
        </div>

        <h2>Seznam uporabnikov</h2>
        <div style="max-width: 1000px; margin: 0 auto;">
            <h3>Administratorji</h3>
            <table class="users-table">
                <thead>
                    <tr>
                        <th>Ime in priimek</th>
                        <th>Email</th>
                        <th>Uporabniško ime</th>
                        <th>Vloga</th>
                        <th>Akcije</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $admin_result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['ime'] . ' ' . $user['priimek']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['uporabnisko_ime']) ?></td>
                            <td><?= htmlspecialchars($user['vloga']) ?></td>
                            <td>
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Ali ste prepričani, da želite izbrisati tega uporabnika?');">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <button type="submit" name="delete_user" class="delete-btn">Izbriši</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <h3>Učitelji</h3>
            <table class="users-table">
                <thead>
                    <tr>
                        <th>Ime in priimek</th>
                        <th>Email</th>
                        <th>Uporabniško ime</th>
                        <th>Predmeti</th>
                        <th>Akcije</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Get all subjects
                    $all_subjects = $conn->query("SELECT * FROM predmeti ORDER BY ime");
                    $subjects_list = $all_subjects->fetch_all(MYSQLI_ASSOC);
                    
                    while ($user = $teacher_result->fetch_assoc()): 
                        // Get teacher's subjects
                        $teacher_subjects_query = "SELECT p.id, p.ime 
                                                FROM predmeti p 
                                                JOIN ucitelji_predmeti up ON p.id = up.id_predmet 
                                                WHERE up.id_ucitelj = {$user['id']}";
                        $teacher_subjects = $conn->query($teacher_subjects_query)->fetch_all(MYSQLI_ASSOC);
                        $teacher_subject_ids = array_column($teacher_subjects, 'id');
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($user['ime'] . ' ' . $user['priimek']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['uporabnisko_ime']) ?></td>
                            <td>
                                <div class="subject-list">
                                    <?php foreach($teacher_subjects as $subject): ?>
                                        <span class="subject-tag">
                                            <?= htmlspecialchars($subject['ime']) ?>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="teacher_id" value="<?= $user['id'] ?>">
                                                <input type="hidden" name="subject_id" value="<?= $subject['id'] ?>">
                                                <input type="hidden" name="action" value="remove">
                                                <button type="submit" name="toggle_subject_right" class="remove-subject" title="Odstrani predmet">&times;</button>
                                            </form>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                                <div class="add-subject">
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="teacher_id" value="<?= $user['id'] ?>">
                                        <input type="hidden" name="action" value="add">
                                        <select name="subject_id" required>
                                            <option value="">Izberi predmet</option>
                                            <?php foreach($subjects_list as $subject): 
                                                if (!in_array($subject['id'], $teacher_subject_ids)): ?>
                                                    <option value="<?= $subject['id'] ?>"><?= htmlspecialchars($subject['ime']) ?></option>
                                                <?php endif;
                                            endforeach; ?>
                                        </select>
                                        <button type="submit" name="toggle_subject_right">Dodaj</button>
                                    </form>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($user['vloga']) ?></td>
                            <td>
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Ali ste prepričani, da želite izbrisati tega uporabnika?');">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <button type="submit" name="delete_user" class="delete-btn">Izbriši</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <h3>Učenci</h3>
            <div style="max-height: 400px; overflow-y: auto; border: 1px solid #eee; border-radius: 8px;">
                <table class="users-table" style="margin-top: 0;">
                    <thead style="position: sticky; top: 0; background: white; z-index: 1;">
                        <tr>
                            <th>Ime in priimek</th>
                            <th>Email</th>
                            <th>Uporabniško ime</th>
                            <th>Razred</th>
                            <th>Akcije</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $student_result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['ime'] . ' ' . $user['priimek']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= htmlspecialchars($user['uporabnisko_ime']) ?></td>
                                <td><?= htmlspecialchars($user['razred'] ?? '-') ?></td>
                                <td>
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Ali ste prepričani, da želite izbrisati tega uporabnika?');">
                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                            <button type="submit" name="delete_user" class="delete-btn">Izbriši</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <button onclick="window.location.href='logout.php'" style="margin: 20px auto; display: block;">Odjava</button>
    </div>

    <script>
        function toggleRazred(vloga) {
            const razredGroup = document.getElementById('razred-group');
            const razredInput = razredGroup.querySelector('input');
            if (vloga === 'ucenec') {
                razredGroup.style.display = 'block';
                razredInput.required = true;
            } else {
                razredGroup.style.display = 'none';
                razredInput.required = false;
            }
        }
    </script>

    <style>
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #666;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button[name="add_user"]:hover {
            background: #218838;
        }
    </style>
</body>
</html>