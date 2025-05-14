function validerFormulaire(form) {
    // Récupération des valeurs
    const nom = form.querySelector('[name="nom_lieux"]').value.trim();
    const adresse = form.querySelector('[name="adresse"]').value.trim();
    const capacite = form.querySelector('[name="capacite"]').value;

    // Validation
    if (!nom || !adresse || !capacite) {
        alert("⚠️ Tous les champs sont obligatoires !");
        return false;
    }

    if (nom.length < 3 || adresse.length < 3) {
        alert("📛 Le nom et l'adresse doivent avoir au moins 3 caractères");
        return false;
    }

    if (isNaN(capacite) || capacite <= 0) {
        alert("🔢 La capacité doit être un nombre positif");
        return false;
    }

    return true;
}