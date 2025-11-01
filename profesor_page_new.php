<?php
session_start();
require_once 'config.php';

// Check if user is logged in and is a teacher
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'ucitelj') {
    header("Location: index.php");
    exit();
}

$id_ucitelj = $_SESSION['user_id'];

// Handle material upload
if (isset($_POST['upload_gradivo'])) {
    $id_predmet = $conn->real_escape_string($_POST['id_predmet']);
    
    // Check if teacher teaches this subject
    $check_query = "SELECT 1 FROM ucitelji_predmeti WHERE id_ucitelj = $id_ucitelj AND id_predmet = $id_predmet";
    $check_result = $conn->query($check_query);
    
    if ($check_result && $check_result->num_rows > 0) {
        $naslov = $conn->real_escape_string($_POST['naslov']);
        
        // Handle file upload
        $file = $_FILES['datoteka'];
        $file_name = $file['name'];
        $file_tmp = $file['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Generate unique filename
        $new_file_name = uniqid() . '_' . $file_name;
        $upload_path = 'uploads/gradiva/' . $new_file_name;
        
        if (move_uploaded_file($file_tmp, $upload_path)) {
            $sql = "INSERT INTO gradiva (id_predmet, naslov, pot_datoteke, izvirno_ime_datoteke) 
                    VALUES ($id_predmet, '$naslov', '$new_file_name', '$file_name')";
            
            if ($conn->query($sql)) {
                $_SESSION['success_message'] = "Gradivo uspešno naloženo.";
            } else {
                $_SESSION['error_message'] = "Napaka pri shranjevanju v bazo.";
            }
        } else {
            $_SESSION['error_message'] = "Napaka pri nalaganju datoteke.";
        }
    } else {
        $_SESSION['error_message'] = "Nimate dovoljenja za nalaganje gradiv za ta predmet.";
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle material deletion
if (isset($_POST['delete_gradivo'])) {
    $id_gradivo = $conn->real_escape_string($_POST['id_gradivo']);
    
    // Check if teacher owns this material
    $check_query = "SELECT g.*, p.id as predmet_id 
                   FROM gradiva g
                   JOIN predmeti p ON g.id_predmet = p.id
                   JOIN ucitelji_predmeti up ON p.id = up.id_predmet
                   WHERE g.id = $id_gradivo AND up.id_ucitelj = $id_ucitelj";
    $check_result = $conn->query($check_query);
    
    if ($check_result && $check_result->num_rows > 0) {
        $gradivo = $check_result->fetch_assoc();
        $file_path = 'uploads/gradiva/' . $gradivo['pot_do_datoteke'];
        
        // Delete file and database record
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        
        $sql = "DELETE FROM gradiva WHERE id = $id_gradivo";
        if ($conn->query($sql)) {
            $_SESSION['success_message'] = "Gradivo uspešno izbrisano.";
        } else {
            $_SESSION['error_message'] = "Napaka pri brisanju gradiva.";
        }
    } else {
        $_SESSION['error_message'] = "Nimate dovoljenja za brisanje tega gradiva.";
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Get teacher's subjects
$predmeti_query = "SELECT p.* FROM predmeti p 
                   JOIN ucitelji_predmeti up ON p.id = up.id_predmet 
                   WHERE up.id_ucitelj = $id_ucitelj";
$predmeti_result = $conn->query($predmeti_query);

// Get list of students in teacher's subjects
$students_query = "SELECT DISTINCT u.* 
                  FROM uporabniki u
                  JOIN ucenci_predmeti up ON u.id = up.id_ucenec
                  JOIN ucitelji_predmeti tp ON up.id_predmet = tp.id_predmet
                  WHERE tp.id_ucitelj = $id_ucitelj AND u.vloga = 'ucenec'";
$students_result = $conn->query($students_query);

// Get available assignments
$naloge_query = "SELECT n.* 
                 FROM naloge n
                 JOIN ucitelji_predmeti up ON n.id_predmet = up.id_predmet
                 WHERE up.id_ucitelj = $id_ucitelj";
$naloge_result = $conn->query($naloge_query);
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profesor stran</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .nav-buttons {
            display: flex;
            gap: 10px;
            margin: 20px 0;
        }
        .nav-buttons a,
        .nav-buttons button.nav-button {
            padding: 10px 20px;
            background: #7494ec;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 1em;
        }
        .nav-buttons a:hover,
        .nav-buttons button.nav-button:hover {
            background: #6884d3;
        }
        .section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .subjects-dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .subject-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .subject-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .subject-card h3 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 1.2em;
        }
        .subject-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin: 15px 0;
            font-size: 0.9em;
            color: #666;
        }
        .subject-actions {
            display: flex;
            gap: 8px;
            margin-top: 15px;
        }
        .subject-actions a {
            flex: 1;
            text-align: center;
            padding: 8px;
            background: #f8f9fa;
            color: #333;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.9em;
        }
        .subject-actions a:hover {
            background: #e9ecef;
        }
        .subject-code {
            display: inline-block;
            padding: 4px 8px;
            background: #e9ecef;
            border-radius: 4px;
            font-family: monospace;
            font-size: 0.9em;
            color: #666;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input[type="text"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .grades-table th {
            background: #7494ec;
            color: white;
            padding: 12px;
            text-align: left;
        }
        .grades-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
    </style>
</head>
<body style="background: #f5f7fb;">
    <div class="box">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="success-message">
                <?= htmlspecialchars($_SESSION['success_message']) ?>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="error-message">
                <?= htmlspecialchars($_SESSION['error_message']) ?>
                <?php unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <h1>Pozdravljeni, <span><?= htmlspecialchars($_SESSION['name']); ?></span></h1>
        
        <!-- Subject Selection and Navigation -->
        <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <form id="subjectForm" style="display: flex; gap: 15px; align-items: center;">
                <div style="flex-grow: 1;">
                    <label for="selectSubject" style="display: block; margin-bottom: 5px; color: #666;">Izberite predmet:</label>
                    <select id="selectSubject" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="">Izberite predmet</option>
                        <?php 
                        mysqli_data_seek($predmeti_result, 0);
                        while($predmet = $predmeti_result->fetch_assoc()): 
                        ?>
                            <option value="<?= $predmet['id'] ?>"><?= htmlspecialchars($predmet['ime']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="nav-buttons" style="margin: 0;">
                    <button type="button" onclick="goToMaterials()" class="nav-button">Gradiva</button>
                    <button type="button" onclick="goToAssignments()" class="nav-button">Naloge</button>
                    <a href="predmeti.php" class="nav-button">Upravljanje predmetov</a>
                    <a href="logout.php" class="nav-button">Odjava</a>
                </div>
            </form>
        </div>

        <script>
            function goToMaterials() {
                const subjectId = document.getElementById('selectSubject').value;
                if (!subjectId) {
                    alert('Prosim izberite predmet');
                    return;
                }
                window.location.href = 'gradiva.php?predmet=' + subjectId;
            }

            function goToAssignments() {
                const subjectId = document.getElementById('selectSubject').value;
                if (!subjectId) {
                    alert('Prosim izberite predmet');
                    return;
                }
                window.location.href = 'naloge.php?predmet=' + subjectId;
            }
        </script>

        <!-- Subjects Dashboard -->
        <div class="subjects-dashboard">
            <?php 
            // Reset the pointer of predmeti_result since we used it earlier
            mysqli_data_seek($predmeti_result, 0);
            
            while($predmet = $predmeti_result->fetch_assoc()): 
                // Get number of students for this subject
                $student_count_query = "SELECT COUNT(*) as count FROM ucenci_predmeti WHERE id_predmet = {$predmet['id']}";
                $student_count = $conn->query($student_count_query)->fetch_assoc()['count'];
                
                // Get number of materials for this subject
                $material_count_query = "SELECT COUNT(*) as count FROM gradiva WHERE id_predmet = {$predmet['id']}";
                $material_count = $conn->query($material_count_query)->fetch_assoc()['count'];
                
                // Get number of assignments for this subject
                $assignment_count_query = "SELECT COUNT(*) as count FROM naloge WHERE id_predmet = {$predmet['id']}";
                $assignment_count = $conn->query($assignment_count_query)->fetch_assoc()['count'];
            ?>
                <div class="subject-card">
                    <h3><?= htmlspecialchars($predmet['ime']) ?></h3>
                    <div class="subject-code"><?= htmlspecialchars($predmet['kljuc_za_vpis']) ?></div>
                    <div class="subject-stats">
                        <div>
                            <div>👥 <?= $student_count ?> učencev</div>
                            <div>📚 <?= $material_count ?> gradiv</div>
                        </div>
                        <div>
                            <div>✍️ <?= $assignment_count ?> nalog</div>
                        </div>
                    </div>
                    <div class="subject-actions">
                        <a href="gradiva.php?predmet=<?= $predmet['id'] ?>">Gradiva</a>
                        <a href="naloge.php?predmet=<?= $predmet['id'] ?>">Naloge</a>
                        <a href="predmeti.php?id=<?= $predmet['id'] ?>">Pregled</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Material Upload Section -->
        <div class="section">
            <h2>Upravljanje gradiv</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="id_predmet">Izberite predmet:</label>
                    <select name="id_predmet" id="id_predmet" required>
                        <?php while($predmet = $predmeti_result->fetch_assoc()): ?>
                            <option value="<?= $predmet['id'] ?>"><?= htmlspecialchars($predmet['ime']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="naslov">Naslov gradiva:</label>
                    <input type="text" name="naslov" id="naslov" required>
                </div>
                <div class="form-group">
                    <label for="datoteka">Izberite datoteko:</label>
                    <input type="file" name="datoteka" id="datoteka" required>
                </div>
                <button type="submit" name="upload_gradivo">Naloži gradivo</button>
            </form>

            <!-- Display Materials List -->
            <?php
            $gradiva_query = "SELECT g.*, p.ime as ime_predmeta 
                            FROM gradiva g
                            JOIN predmeti p ON g.id_predmet = p.id
                            JOIN ucitelji_predmeti up ON p.id = up.id_predmet
                            WHERE up.id_ucitelj = $id_ucitelj
                            ORDER BY g.nalozen_ob DESC";
            $gradiva_result = $conn->query($gradiva_query);
            
            if ($gradiva_result && $gradiva_result->num_rows > 0):
            ?>
            <h3>Vaša gradiva</h3>
            <table class="grades-table">
                <tr>
                    <th>Predmet</th>
                    <th>Naslov</th>
                    <th>Datoteka</th>
                    <th>Naloženo</th>
                    <th>Akcije</th>
                </tr>
                <?php while($gradivo = $gradiva_result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($gradivo['ime_predmeta']) ?></td>
                    <td><?= htmlspecialchars($gradivo['naslov']) ?></td>
                    <td><?= htmlspecialchars($gradivo['izvirno_ime_datoteke']) ?></td>
                    <td><?= date('d.m.Y H:i', strtotime($gradivo['nalozen_ob'])) ?></td>
                    <td>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="id_gradivo" value="<?= $gradivo['id'] ?>">
                            <button type="submit" name="delete_gradivo" onclick="return confirm('Ali ste prepričani, da želite izbrisati to gradivo?')">Izbriši</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
            <?php endif; ?>
        </div>

        <!-- Display recent submissions and grades -->
        <?php
        $oddaje_query = "SELECT o.*, u.ime, u.priimek, n.naslov as naslov_naloge, p.ime as ime_predmeta
                        FROM oddaje o 
                        JOIN uporabniki u ON o.id_ucenec = u.id 
                        JOIN naloge n ON o.id_naloga = n.id
                        JOIN predmeti p ON n.id_predmet = p.id
                        JOIN ucitelji_predmeti up ON p.id = up.id_predmet
                        WHERE up.id_ucitelj = $id_ucitelj
                        ORDER BY o.oddano_ob DESC 
                        LIMIT 10";
        $oddaje_result = $conn->query($oddaje_query);
        
        if ($oddaje_result && $oddaje_result->num_rows > 0):
        ?>
        <div style="max-width: 800px; margin: 20px auto;">
            <h3>Zadnje oddaje</h3>
            <table class="grades-table">
                <thead>
                    <tr>
                        <th>Učenec</th>
                        <th>Predmet</th>
                        <th>Naloga</th>
                        <th>Ocena</th>
                        <th>Oddano</th>
                        <th>Akcije</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($oddaja = $oddaje_result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($oddaja['ime'] . ' ' . $oddaja['priimek']) ?></td>
                            <td><?= htmlspecialchars($oddaja['ime_predmeta']) ?></td>
                            <td><?= htmlspecialchars($oddaja['naslov_naloge']) ?></td>
                            <td><?= $oddaja['ocena'] ? htmlspecialchars($oddaja['ocena']) : 'Ni ocenjeno' ?></td>
                            <td><?= date('d.m.Y H:i', strtotime($oddaja['oddano_ob'])) ?></td>
                            <td>
                                <a href="oceni_oddajo.php?id=<?= $oddaja['id'] ?>" class="button">Oceni</a>
                                <a href="<?= htmlspecialchars($oddaja['pot_datoteke']) ?>" 
                                   download="<?= htmlspecialchars($oddaja['izvirno_ime_datoteke']) ?>" 
                                   class="button">Prenesi</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <button onclick="window.location.href='logout.php'" style="margin: 20px auto; display: block;">Odjava</button>
    </div>
</body>
</html>