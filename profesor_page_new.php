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
        
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error_message'] = "Napaka pri nalaganju: " . ($file['error'] == UPLOAD_ERR_INI_SIZE ? "Datoteka je prevelika." : "Prosim poskusite ponovno.");
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
        
        $file_name = $file['name'];
        $file_tmp = $file['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Check if uploads/gradiva directory exists, create if it doesn't
        $upload_dir = 'uploads/gradiva';
        if (!file_exists($upload_dir)) {
            if (!mkdir($upload_dir, 0777, true)) {
                $_SESSION['error_message'] = "Napaka pri ustvarjanju mape za nalaganje.";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        }
        
        // Generate unique filename
        $new_file_name = uniqid() . '_' . $file_name;
        $upload_path = $upload_dir . '/' . $new_file_name;
        
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

// Handle homework assignment creation
if (isset($_POST['add_naloga'])) {
    $id_predmet = $conn->real_escape_string($_POST['naloga_predmet']);
    
    // Check if teacher teaches this subject
    $check_query = "SELECT 1 FROM ucitelji_predmeti WHERE id_ucitelj = $id_ucitelj AND id_predmet = $id_predmet";
    $check_result = $conn->query($check_query);
    
    if ($check_result && $check_result->num_rows > 0) {
        $naslov = $conn->real_escape_string($_POST['naloga_naslov']);
        $opis = $conn->real_escape_string($_POST['naloga_opis']);
        $rok = $conn->real_escape_string($_POST['naloga_rok']);
        
        // Remove file upload handling since the database doesn't support it yet
        
        // Insert new homework assignment
        $sql = "INSERT INTO naloge (id_predmet, naslov, opis, rok_oddaje) 
                VALUES ($id_predmet, '$naslov', '$opis', '$rok')";
        
        if ($conn->query($sql)) {
            $_SESSION['success_message'] = "Nova naloga uspešno dodana.";
        } else {
            $_SESSION['error_message'] = "Napaka pri dodajanju naloge.";
        }
    } else {
        $_SESSION['error_message'] = "Nimate dovoljenja za dodajanje nalog za ta predmet.";
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle homework deletion
if (isset($_POST['delete_naloga'])) {
    $id_naloga = $conn->real_escape_string($_POST['id_naloga']);
    
    // Check if teacher owns this homework
    $check_query = "SELECT n.*, p.id as predmet_id 
                   FROM naloge n
                   JOIN predmeti p ON n.id_predmet = p.id
                   JOIN ucitelji_predmeti up ON p.id = up.id_predmet
                   WHERE n.id = $id_naloga AND up.id_ucitelj = $id_ucitelj";
    $check_result = $conn->query($check_query);
    
    if ($check_result && $check_result->num_rows > 0) {
        $naloga = $check_result->fetch_assoc();
        
        // Delete attached file if exists
        if ($naloga['pot_datoteke']) {
            $file_path = 'uploads/naloge/' . $naloga['pot_datoteke'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
        
        // Delete homework assignment
        $sql = "DELETE FROM naloge WHERE id = $id_naloga";
        if ($conn->query($sql)) {
            $_SESSION['success_message'] = "Naloga uspešno izbrisana.";
        } else {
            $_SESSION['error_message'] = "Napaka pri brisanju naloge.";
        }
    } else {
        $_SESSION['error_message'] = "Nimate dovoljenja za brisanje te naloge.";
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
    
</head>
<body>
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
        <div class="form-section">
            <form id="subjectForm" class="flex-form">
                <div class="form-group">
                    <label for="selectSubject" class="form-label">Izberite predmet:</label>
                    <select id="selectSubject">
                        <option value="">Izberite predmet</option>
                        <?php 
                        mysqli_data_seek($predmeti_result, 0);
                        while($predmet = $predmeti_result->fetch_assoc()): 
                        ?>
                            <option value="<?= $predmet['id'] ?>"><?= htmlspecialchars($predmet['ime']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="nav-buttons mt-0">
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
                        <?php 
                        mysqli_data_seek($predmeti_result, 0);
                        while($predmet = $predmeti_result->fetch_assoc()): 
                        ?>
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
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="id_gradivo" value="<?= $gradivo['id'] ?>">
                            <button type="submit" name="delete_gradivo" class="delete-button" onclick="return confirm('Ali ste prepričani, da želite izbrisati to gradivo?')">Izbriši</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
            <?php endif; ?>
        </div>

        <!-- Homework Management Section -->
        <div class="section">
            <h2>Upravljanje nalog</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="naloga_predmet">Izberite predmet:</label>
                    <select name="naloga_predmet" id="naloga_predmet" required>
                        <?php 
                        mysqli_data_seek($predmeti_result, 0);
                        while($predmet = $predmeti_result->fetch_assoc()): 
                        ?>
                            <option value="<?= $predmet['id'] ?>"><?= htmlspecialchars($predmet['ime']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="naloga_naslov">Naslov naloge:</label>
                    <input type="text" name="naloga_naslov" id="naloga_naslov" required>
                </div>
                <div class="form-group">
                    <label for="naloga_opis">Opis naloge:</label>
                    <textarea name="naloga_opis" id="naloga_opis" rows="4" required></textarea>
                </div>
                <div class="form-group">
                    <label for="naloga_rok">Rok za oddajo:</label>
                    <input type="datetime-local" name="naloga_rok" id="naloga_rok" required>
                </div>
                <button type="submit" name="add_naloga">Dodaj nalogo</button>
            </form>

            <!-- Display Active Assignments -->
            <?php
            $active_naloge_query = "SELECT n.*, p.ime as ime_predmeta, 
                                  (SELECT COUNT(*) FROM oddaje WHERE id_naloga = n.id) as stevilo_oddaj 
                                  FROM naloge n
                                  JOIN predmeti p ON n.id_predmet = p.id
                                  JOIN ucitelji_predmeti up ON p.id = up.id_predmet
                                  WHERE up.id_ucitelj = $id_ucitelj AND n.rok_oddaje >= NOW()
                                  ORDER BY n.rok_oddaje ASC";
            $active_naloge_result = $conn->query($active_naloge_query);
            
            if ($active_naloge_result && $active_naloge_result->num_rows > 0):
            ?>
            <h3>Aktivne naloge</h3>
            <table class="grades-table">
                <tr>
                    <th>Predmet</th>
                    <th>Naslov</th>
                    <th>Rok</th>
                    <th>Št. oddaj</th>
                    <th>Akcije</th>
                </tr>
                <?php while($naloga = $active_naloge_result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($naloga['ime_predmeta']) ?></td>
                    <td><?= htmlspecialchars($naloga['naslov']) ?></td>
                    <td><?= date('d.m.Y H:i', strtotime($naloga['rok_oddaje'])) ?></td>
                    <td><?= $naloga['stevilo_oddaj'] ?></td>
                    <td>
                        <a href="naloge.php?id=<?= $naloga['id'] ?>" class="button">Pregled</a>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="id_naloga" value="<?= $naloga['id'] ?>">
                            <button type="submit" name="delete_naloga" class="delete-button" 
                                    onclick="return confirm('Ali ste prepričani, da želite izbrisati to nalogo?')">
                                Izbriši
                            </button>
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
        <div class="content-width-limited">
            <h3 class="section-header">Zadnje oddaje</h3>
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

        <button onclick="window.location.href='logout.php'" class="logout-button">Odjava</button>
    </div>
</body>
</html>