<?php
include "../include/fonction.php";
session_start();

if (!isset($_SESSION['IdUtilisateur'])) {
    header("location:index.php");
    exit();
}

$categorie_filter = isset($_GET['categorie']) ? $_GET['categorie'] : null;
$nom_filter = isset($_GET['nom_objet']) ? trim($_GET['nom_objet']) : '';
$disponible_filter = isset($_GET['disponible']) ? true : false;

$objets = get_objets_vue($categorie_filter, $nom_filter, $disponible_filter);

$categories = get_categories();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <title>Liste des Objets</title>
</head>
<body>
    <?php include "../include/header.php"; ?>
    
    <div class="container mt-4">
        <h1>Liste des Objets</h1>
        
        <div class="mb-3">
            <form method="GET" class="d-flex align-items-center flex-wrap">
                <label for="categorie" class="me-2">Catégorie:</label>
                <select name="categorie" class="form-select me-2" style="width: auto;">
                    <option value="">Toutes les catégories</option>
                    <?php while ($cat = mysqli_fetch_assoc($categories)) { ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo ($categorie_filter == $cat['id']) ? 'selected' : ''; ?>><?php echo $cat['nom']; ?></option>
                    <?php } ?>
                </select>
                <label for="nom_objet" class="ms-2 me-2">Nom de l’objet:</label>
                <input type="text" name="nom_objet" class="form-control me-2" style="width: 180px;" value="<?php echo htmlspecialchars($nom_filter); ?>">
                <div class="form-check ms-2">
                    <input class="form-check-input" type="checkbox" name="disponible" id="disponible" <?php echo $disponible_filter ? 'checked' : ''; ?> />
                    <label class="form-check-label" for="disponible">Disponible</label>
                </div>
                <button type="submit" class="btn btn-primary ms-2">Filtrer</button>
                <a href="accueil.php" class="btn btn-secondary ms-2">Réinitialiser</a>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>images</th>
                        <th>Nom</th>
                        <th>Categorie</th>
                        <th>Proprietaire</th>
                        <th>Statut</th>
                        <th>Emprunte par</th>
                        <th>Date de retour prevue</th>
                        <th>Add img</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($objet = mysqli_fetch_assoc($objets)) { ?>
                        <tr>
                            <td>
                                <img src="../assets/image/<?php echo get_main_image($objet['id_objet']); ?>"style="width:25px;height:auto;">
                            </td>
                            <td>
                                <a href="fiche_objet.php?id=<?php echo $objet['id_objet']; ?>">
                                    <?php echo $objet['nom_objet']; ?>
                                </a>
                            </td>
                            <td><?php echo $objet['nom_categorie']; ?></td>
                            <td>
                                <a href="fiche_membre.php?id=<?php echo $objet['proprietaire_id']; ?>">
                                    <?php echo $objet['proprietaire_nom']; ?>
                                </a>
                            </td>
                            <td>
                                <?php if ($objet['statut_emprunt'] == 'Emprunte') { ?>
                                    <span class="badge bg-warning">Emprunte</span>
                                <?php } else { ?>
                                    <span class="badge bg-success">Disponible</span>
                                <?php } ?>
                            </td>
                            <td>
                                <?php echo $objet['emprunteur_nom'] ? $objet['emprunteur_nom'] : '-'; ?>
                            </td>
                            <td>
                                <?php
                                if ($objet['date_retour_prevue']) {
                                    $date = new DateTime($objet['date_retour_prevue']);
                                    $today = new DateTime();
                                    $late = $date < $today;
                                    $class = $late ? 'text-danger fw-bold' : 'text-primary';
                                    echo '<span class="' . $class . '">' . $date->format('d/m/Y') . '</span>';
                                    if ($late) echo ' <small class="text-danger">(En retard)</small>';
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                            <td>
                                <a href="add_image.php?id_objet=<?php echo $objet['id_objet']; ?>" class="btn btn-sm btn-outline-primary" title="Ajouter une image">
                                    <i class="bi bi-plus">+</i>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>