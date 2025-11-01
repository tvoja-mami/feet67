<?php
session_start();
require_once 'config.php';

$id_naloga = isset($_GET['id_naloga']) ? (int)$_GET['id_naloga'] : 0;
$id_ucitelj = $_SESSION['user_id'];

// Get submissions with student info
$oddaje_query = "
    SELECT o.*, u.ime, u.priimek, u.email, u.razred
    FROM oddaje o
    JOIN uporabniki u ON o.id_ucenec = u.id
    WHERE o.id_naloga = $id_naloga
    ORDER BY o.oddano_ob DESC
";
$oddaje_result = $conn->query($oddaje_query);

// Get assignment info
$naloga_query = "SELECT * FROM naloge WHERE id = $id_naloga";
$naloga_result = $conn->query($naloga_query);
$naloga = $naloga_result->fetch_assoc();
?>

<h2><?= htmlspecialchars($naloga['naslov']) ?> - Oddaje</h2>

<?php if ($oddaje_result && $oddaje_result->num_rows > 0): ?>
    <table class="submissions-table">
        <thead>
            <tr>
                <th>Učenec</th>
                <th>Razred</th>
                <th>Oddano</th>
                <th>Datoteka</th>
                <th>Ocena</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($oddaja = $oddaje_result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($oddaja['ime'] . ' ' . $oddaja['priimek']) ?></td>
                    <td><?= htmlspecialchars($oddaja['razred'] ?? '-') ?></td>
                    <td><?= date('d.m.Y H:i', strtotime($oddaja['oddano_ob'])) ?></td>
                    <td>
                        <a href="<?= htmlspecialchars($oddaja['pot_datoteke']) ?>" 
                           download="<?= htmlspecialchars($oddaja['izvirno_ime_datoteke']) ?>"
                           class="button">
                            Prenesi
                        </a>
                    </td>
                    <td>
                        <form method="POST" class="grade-form" data-submission-id="<?= $submission['id'] ?>">
                            <input type="hidden" name="id_oddaja" value="<?= $submission['id'] ?>">
                            <input type="number" name="ocena" value="<?= $submission['ocena'] ?>" 
                                   min="1" max="5" class="grade-input" required>
                            <textarea name="komentar" placeholder="Komentar"><?= htmlspecialchars($submission['komentar_ucitelja']) ?></textarea>
                            <button type="submit" name="grade_submission">Shrani</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p class="info-message">Trenutno ni oddaj za to nalogo.</p>
<?php endif; ?>

<style>
    .submissions-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    
    .submissions-table th,
    .submissions-table td {
        padding: 12px;
        border-bottom: 1px solid #eee;
        text-align: left;
    }
    
    .submissions-table th {
        background: #7494ec;
        color: white;
    }
    
    .grade-form {
        display: flex;
        gap: 10px;
        align-items: start;
    }
    
    .grade-input {
        width: 60px;
    }
    
    textarea[name="komentar"] {
        width: 200px;
        height: 60px;
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

<script>
document.querySelectorAll('.grade-form').forEach(form => {
    form.onsubmit = function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        fetch('grade_submission.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update UI or show success message
                alert('Ocena uspešno shranjena!');
            } else {
                alert('Napaka pri shranjevanju ocene: ' + data.error);
            }
        });
    };
});
</script>