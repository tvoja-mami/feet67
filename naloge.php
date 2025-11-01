<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
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
$subject_query = "";
if ($_SESSION['role'] === 'ucitelj') {
    $subject_query = "SELECT p.* FROM predmeti p 
                     JOIN ucitelji_predmeti up ON p.id = up.id_predmet 
                     WHERE p.id = $id_predmet AND up.id_ucitelj = $id_user";
} else {
    $subject_query = "SELECT p.* FROM predmeti p 
                     JOIN ucenci_predmeti ep ON p.id = ep.id_predmet 
                     WHERE p.id = $id_predmet AND ep.id_ucenec = $id_user";
}

$subject_result = $conn->query($subject_query);
$subject = $subject_result->fetch_assoc();

// Check if subject exists and user has access
if (!$subject) {
    $_SESSION['error_message'] = "Predmet ne obstaja ali nimate dostopa do njega.";
    header("Location: " . ($_SESSION['role'] === 'ucitelj' ? 'profesor_page_new.php' : 'ucenec_page.php'));
    exit();
}

// Handle new assignment creation (teacher only)
if ($_SESSION['role'] === 'ucitelj' && isset($_POST['create_assignment'])) {
    $naslov = $conn->real_escape_string($_POST['naslov']);
    $opis = $conn->real_escape_string($_POST['opis']);
    $rok_oddaje = $conn->real_escape_string($_POST['rok_oddaje']);
    
    $sql = "INSERT INTO naloge (id_predmet, naslov, opis, rok_oddaje) 
            VALUES ($id_predmet, '$naslov', '$opis', '$rok_oddaje')";
    
    if ($conn->query($sql)) {
        $success_message = "Nova naloga uspešno ustvarjena!";
    } else {
        $error_message = "Napaka pri ustvarjanju naloge: " . $conn->error;
    }
}

// Handle assignment submission (student only)
if ($_SESSION['role'] === 'ucenec' && isset($_POST['submit_assignment'])) {
    if (isset($_FILES['file'])) {
        $id_naloga = (int)$_POST['id_naloga'];
        $file = $_FILES['file'];
        
        // Create uploads directory if it doesn't exist
        $upload_dir = "uploads/oddaje/$id_predmet/$id_naloga/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $filename = $id_user . '_' . uniqid() . '_' . basename($file['name']);
        $target_path = $upload_dir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            // Check if student already submitted this assignment
            $check_query = "SELECT id FROM oddaje WHERE id_naloga = $id_naloga AND id_ucenec = $id_user";
            $check_result = $conn->query($check_query);
            
            if ($check_result->num_rows > 0) {
                // Update existing submission
                $existing_submission = $check_result->fetch_assoc();
                $sql = "UPDATE oddaje SET pot_datoteke = '$target_path', 
                        izvirno_ime_datoteke = '" . $conn->real_escape_string($file['name']) . "',
                        oddano_ob = CURRENT_TIMESTAMP
                        WHERE id = " . $existing_submission['id'];
            } else {
                // Create new submission
                $sql = "INSERT INTO oddaje (id_naloga, id_ucenec, pot_datoteke, izvirno_ime_datoteke) 
                        VALUES ($id_naloga, $id_user, '$target_path', '" . $conn->real_escape_string($file['name']) . "')";
            }
            
            if ($conn->query($sql)) {
                $success_message = "Naloga uspešno oddana!";
            } else {
                $error_message = "Napaka pri shranjevanju oddaje: " . $conn->error;
            }
        } else {
            $error_message = "Napaka pri nalaganju datoteke!";
        }
    }
}

// Handle grading (teacher only)
if ($_SESSION['role'] === 'ucitelj' && isset($_POST['grade_submission'])) {
    $id_oddaja = (int)$_POST['id_oddaja'];
    $ocena = (int)$_POST['ocena'];
    $komentar = $conn->real_escape_string($_POST['komentar']);
    
    $sql = "UPDATE oddaje SET ocena = $ocena, komentar_ucitelja = '$komentar' WHERE id = $id_oddaja";
    if ($conn->query($sql)) {
        $success_message = "Ocena uspešno dodana!";
    } else {
        $error_message = "Napaka pri dodajanju ocene: " . $conn->error;
    }
}

// Get assignments list with submission info
if ($_SESSION['role'] === 'ucitelj') {
    $assignments_query = "
        SELECT n.*, 
               COUNT(o.id) as st_oddaj,
               COUNT(CASE WHEN o.ocena IS NOT NULL THEN 1 END) as st_ocenjenih
        FROM naloge n
        LEFT JOIN oddaje o ON n.id = o.id_naloga
        WHERE n.id_predmet = $id_predmet
        GROUP BY n.id
        ORDER BY n.rok_oddaje DESC";
} else {
    $assignments_query = "
        SELECT n.*, o.id as oddaja_id, o.oddano_ob, o.ocena, o.komentar_ucitelja
        FROM naloge n
        LEFT JOIN oddaje o ON n.id = o.id_naloga AND o.id_ucenec = $id_user
        WHERE n.id_predmet = $id_predmet
        ORDER BY n.rok_oddaje DESC";
}

$assignments_result = $conn->query($assignments_query);
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Naloge: <?= htmlspecialchars($subject['ime']) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body style="background: #f5f7fb;">
    <div class="box">
        <h1><?= htmlspecialchars($subject['ime']) ?> - Naloge</h1>
        
        <?php if (isset($success_message)): ?>
            <div class="success-message"><?= $success_message ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?= $error_message ?></div>
        <?php endif; ?>

        <?php if ($_SESSION['role'] === 'ucitelj'): ?>
            <div class="form-box">
                <h2>Nova naloga</h2>
                <form method="POST">
                    <input type="text" name="naslov" placeholder="Naslov naloge" required>
                    <textarea name="opis" placeholder="Opis naloge" rows="4" required></textarea>
                    <input type="datetime-local" name="rok_oddaje" required>
                    <button type="submit" name="create_assignment">Ustvari nalogo</button>
                </form>
            </div>
        <?php endif; ?>

        <?php if ($assignments_result && $assignments_result->num_rows > 0): ?>
            <div style="max-width: 800px; margin: 20px auto;">
                <div class="assignments-list">
                    <?php while ($assignment = $assignments_result->fetch_assoc()): ?>
                        <div class="assignment-card">
                            <div class="assignment-header">
                                <h3><?= htmlspecialchars($assignment['naslov']) ?></h3>
                                <span class="deadline <?= strtotime($assignment['rok_oddaje']) < time() ? 'expired' : '' ?>">
                                    Rok: <?= date('d.m.Y H:i', strtotime($assignment['rok_oddaje'])) ?>
                                </span>
                            </div>
                            
                            <div class="assignment-body">
                                <p><?= nl2br(htmlspecialchars($assignment['opis'])) ?></p>
                                
                                <?php if ($_SESSION['role'] === 'ucitelj'): ?>
                                    <div class="submission-stats">
                                        <span>Oddaje: <?= $assignment['st_oddaj'] ?></span>
                                        <span>Ocenjeno: <?= $assignment['st_ocenjenih'] ?></span>
                                        <a href="#" class="button" onclick="viewSubmissions(<?= $assignment['id'] ?>)">
                                            Preglej oddaje
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div class="student-submission">
                                        <?php if (isset($assignment['oddaja_id'])): ?>
                                            <p>Oddano: <?= date('d.m.Y H:i', strtotime($assignment['oddano_ob'])) ?></p>
                                            <?php if ($assignment['ocena']): ?>
                                                <p>Ocena: <?= $assignment['ocena'] ?></p>
                                                <?php if ($assignment['komentar_ucitelja']): ?>
                                                    <p>Komentar: <?= nl2br(htmlspecialchars($assignment['komentar_ucitelja'])) ?></p>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <p>Čaka na oceno</p>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        
                                        <?php if (strtotime($assignment['rok_oddaje']) > time()): ?>
                                            <form method="POST" enctype="multipart/form-data" class="submit-form">
                                                <input type="hidden" name="id_naloga" value="<?= $assignment['id'] ?>">
                                                <input type="file" name="file" required>
                                                <button type="submit" name="submit_assignment">
                                                    <?= isset($assignment['oddaja_id']) ? 'Posodobi oddajo' : 'Oddaj nalogo' ?>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        <?php else: ?>
            <p class="info-message">Trenutno ni aktivnih nalog.</p>
        <?php endif; ?>

        <button onclick="window.location.href='<?= $_SESSION['role'] === 'ucitelj' ? 'profesor_page_new.php' : 'ucenec_page.php' ?>'" 
                style="margin: 20px auto;">
            Nazaj na glavno stran
        </button>
    </div>

    <?php if ($_SESSION['role'] === 'ucitelj'): ?>
        <!-- Modal for viewing submissions -->
        <div id="submissionsModal" class="modal" style="display: none;">
            <div class="modal-content">
                <span class="close">&times;</span>
                <div id="submissionsContent"></div>
            </div>
        </div>

        <script>
            function viewSubmissions(assignmentId) {
                // Load submissions for this assignment via AJAX
                fetch(`get_submissions.php?id_naloga=${assignmentId}`)
                    .then(response => response.text())
                    .then(html => {
                        document.getElementById('submissionsContent').innerHTML = html;
                        document.getElementById('submissionsModal').style.display = 'block';
                    });
            }

            // Close modal when clicking X or outside
            document.querySelector('.close').onclick = function() {
                document.getElementById('submissionsModal').style.display = 'none';
            }

            window.onclick = function(event) {
                if (event.target == document.getElementById('submissionsModal')) {
                    document.getElementById('submissionsModal').style.display = 'none';
                }
            }
        </script>
    <?php endif; ?>

    <style>
        .assignments-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .assignment-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .assignment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .deadline {
            color: #666;
            font-size: 0.9em;
        }
        
        .deadline.expired {
            color: #dc3545;
        }
        
        .assignment-body {
            color: #333;
        }
        
        textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 20px;
            resize: vertical;
        }
        
        .submission-stats {
            margin-top: 15px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .student-submission {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        
        .submit-form {
            margin-top: 15px;
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 800px;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .close {
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: #666;
        }
    </style>
</body>
</html>