<?php
// Inclusion du fichier de configuration
include 'C:\xampp\htdocs\projetwebCRUD - ranim\Config.php';

// Fonction pour ajouter un lieu
function ajouterLieu($nom_lieux, $adresse, $capacite) {
    // Connexion à la base de données
    $pdo = config::getConnexion();

    // Préparation de la requête d'insertion
    $stmt = $pdo->prepare("INSERT INTO lieux (nom_lieux, adresse, capacite) VALUES (?, ?, ?)");

    // Exécution de la requête avec les données
    $stmt->execute([$nom_lieux, $adresse, $capacite]);

    return true;  // Indiquer que l'ajout a réussi
}
function getLieux() {
    $conn = config::getConnexion();

    try {
        $stmt = $conn->prepare("SELECT * FROM lieux");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        return [];
    }
}
function supprimerLieu($id_lieux) {
    $conn = config::getConnexion();

    try {
        $stmt = $conn->prepare("DELETE FROM lieux WHERE id_lieux = ?");
        $stmt->execute([$id_lieux]);
        return true;
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        return false;
    }
}

function modifierLieu($id_lieux, $nom_lieux, $adresse, $capacite) {
    try {
        $pdo = config::getConnexion();

        $sql = "UPDATE lieux SET 
                    nom_lieux = :nom_lieux, 
                    adresse = :adresse, 
                    capacite = :capacite 
                WHERE id_lieux = :id_lieux";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            'nom_lieux' => $nom_lieux,
            'adresse' => $adresse,
            'capacite' => $capacite,
            'id_lieux' => $id_lieux
        ]);

        return $stmt->rowCount() > 0; // Retourne true si des lignes ont été modifiées
    } catch (PDOException $e) {
        error_log("Erreur lors de la modification du lieu : " . $e->getMessage());
        return false;
    }
}



?>
