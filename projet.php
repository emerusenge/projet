<?php  
$nom = $prenom = $age = $erreur = "";
$valider = isset($_POST["valider"]) ? $_POST["valider"] : '';
$update = isset($_POST["update"]) ? $_POST["update"] : '';
$pdo = null;

try {
    $pdo = new PDO("mysql:host=localhost;dbname=formulaire", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
    exit;
}

if ($valider) {
    $nom = $_POST["nom"];
    $prenom = $_POST["prenom"];
    $age = $_POST["age"];

    if (empty($nom))  $erreur .= "<li>Nom laissé vide</li>";
    if (empty($prenom)) $erreur .= "<li>Prénom laissé vide</li>";
    if (empty($age)) $erreur .= "<li>Âge invalide</li>";

    if (empty($erreur)) {
        try {
            $req = $pdo->prepare("INSERT INTO `table` (nom, prenom, age) VALUES (?, ?, ?)");
            $req->execute(array($nom, $prenom, $age));  
            echo "<p>Les données ont été insérées avec succès.</p>";
        } catch (PDOException $e) {
            echo "Erreur lors de l'insertion des données : " . $e->getMessage();
        }
    }
}

$patients = [];
$stmt = $pdo->prepare("SELECT * FROM `table`");
$stmt->execute();
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['delete_nom']) && isset($_POST['delete_prenom']) && isset($_POST['delete_age'])) {
    $delete_nom = $_POST['delete_nom'];    
    $delete_prenom = $_POST['delete_prenom']; 
    $delete_age = $_POST['delete_age'];      

    try {
        $req = $pdo->prepare("DELETE FROM `table` WHERE nom = ? AND prenom = ? AND age = ?");
        $req->execute(array($delete_nom, $delete_prenom, $delete_age)); 
        echo "<p>Le patient a été supprimé avec succès.</p>";
    } catch (PDOException $e) {
        echo "Erreur lors de la suppression des données : " . $e->getMessage();
    }
}

if ($update) {
    $update_nom = $_POST['update_nom'];
    $update_prenom = $_POST['update_prenom'];
    $update_age = $_POST['update_age'];

    if (empty($update_nom))  $erreur .= "<li>Nom laissé vide</li>";
    if (empty($update_prenom)) $erreur .= "<li>Prénom laissé vide</li>";
    if (empty($update_age)) $erreur .= "<li>Âge invalide</li>";

    if (empty($erreur)) {
        try {
            $req = $pdo->prepare("UPDATE `table` SET nom = ?, prenom = ?, age = ? WHERE nom = ? AND prenom = ?");
            $req->execute(array($update_nom, $update_prenom, $update_age, $_POST['original_nom'], $_POST['original_prenom']));
            echo "<p>Les informations du patient ont été mises à jour avec succès.</p>";
        } catch (PDOException $e) {
            echo "Erreur lors de la mise à jour des données : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire d'enregistrement des patients</title>
    <style>
        body {
            background-color: white;
            color: black;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        form {
            width: 300px;
            margin: 100px auto;
            background-color: cadetblue;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        header {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
            color: white;
        }

        label {
            display: block;
            margin: 10px 0;
            font-size: 16px;
            color:white;
        }

        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
            background-color: #fff;
            color: black;
        }

        input[type="submit"] {
            width: 100%;
            padding: 10px;
            font-size: 18px;
            background-color: #4CAF50;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        button[type="button"], .delete-btn, .update-form {
    background-color: green; 
    cursor: pointer;
    border-radius: 5px;
    transition: background-color 0.3s ease;
    padding: 10px 15px;
    border: none;
    color: white;
    font-size: 16px;
}


button[type="button"]:hover, .delete-btn:hover, .update-form:hover {
    background-color: #e53935; 
}


button[type="button"]:active, .delete-btn:active, .update-form:active {
    background-color: #d32f2f;  
}


        ul {
            color: red;
            font-size: 14px;
        }

        li {
            list-style: none;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        .actions {
            text-align: center;
        }

        .update-form {
            display: none;
        }

    </style>
    <script>
        function toggleUpdateForm(id) {
            var form = document.getElementById('update-form-' + id);
            if (form.style.display === 'none' || form.style.display === '') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        }
    </script>
</head>
<body>
    <form action="projet.php" method="post">
        <header>Enregistrement des Patients</header>
        <label for="nom">Nom <input type="text" name="nom" placeholder="Entrer votre nom" value="<?php echo htmlspecialchars($nom); ?>"></label><br><br>
        <label for="prenom">Prénom <input type="text" name="prenom" placeholder="Entrer votre prénom" value="<?php echo htmlspecialchars($prenom); ?>"></label><br><br>
        <label for="age">Âge <input type="number" name="age" placeholder="Entrer votre âge" value="<?php echo htmlspecialchars($age); ?>"></label><br><br>

        <input type="submit" name="valider" value="Valider">
        
        <?php if ($erreur): ?>
            <ul><?php echo $erreur; ?></ul>
        <?php endif; ?>
    </form>

    <h2 style="text-align:center;">Liste des patients enregistrés</h2>
    <?php if ($patients): ?>
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Âge</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($patients as $index => $patient): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($patient['nom']); ?></td>
                        <td><?php echo htmlspecialchars($patient['prenom']); ?></td>
                        <td><?php echo htmlspecialchars($patient['age']); ?></td>
                        <td class="actions">
                            <!-- Bouton pour afficher les champs de mise à jour -->
                            <button type="button" onclick="toggleUpdateForm(<?php echo $index; ?>)">Mettre à jour</button>

                            <form method="post" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce patient ?');">
                                <input type="hidden" name="delete_nom" value="<?php echo htmlspecialchars($patient['nom']); ?>">
                                <input type="hidden" name="delete_prenom" value="<?php echo htmlspecialchars($patient['prenom']); ?>">
                                <input type="hidden" name="delete_age" value="<?php echo htmlspecialchars($patient['age']); ?>">
                                <button type="submit" class="delete-btn">Supprimer</button>
                            </form>
                            <div class="update-form" id="update-form-<?php echo $index; ?>">
                                <form method="post">
                                    <input type="hidden" name="original_nom" value="<?php echo htmlspecialchars($patient['nom']); ?>">
                                    <input type="hidden" name="original_prenom" value="<?php echo htmlspecialchars($patient['prenom']); ?>">

                                    <label for="update_nom">Nom
                                        <input type="text" name="update_nom" value="<?php echo htmlspecialchars($patient['nom']); ?>">
                                    </label><br><br>
                                    <label for="update_prenom">Prénom
                                        <input type="text" name="update_prenom" value="<?php echo htmlspecialchars($patient['prenom']); ?>">
                                    </label><br><br>
                                    <label for="update_age">Âge
                                        <input type="number" name="update_age" value="<?php echo htmlspecialchars($patient['age']); ?>">
                                    </label><br><br>

                                    <input type="submit" name="update" value="Mettre à jour">
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align:center;">Aucun patient enregistré</p>
    <?php endif; ?>
</body>
</html>
