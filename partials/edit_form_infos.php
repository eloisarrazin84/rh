<form action="update_profile.php" method="POST" class="row g-3">
    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">

   <div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">Civilité</label>
        <select name="civility" class="form-select">
            <option value="">–</option>
            <option value="Monsieur" <?= ($details['civility'] ?? '') === 'Monsieur' ? 'selected' : '' ?>>Monsieur</option>
            <option value="Madame" <?= ($details['civility'] ?? '') === 'Madame' ? 'selected' : '' ?>>Madame</option>
        </select>
    </div>
    <div class="col-md-8">
        <label class="form-label">Adresse</label>
        <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($details['address'] ?? '') ?>">
    </div>

    <div class="col-md-4">
        <label class="form-label">Date de naissance</label>
        <input type="date" name="birthdate" class="form-control" value="<?= $details['birthdate'] ?? '' ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label">Lieu de naissance</label>
        <input type="text" name="birth_place" class="form-control" value="<?= htmlspecialchars($details['birth_place'] ?? '') ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label">Nationalité</label>
        <input type="text" name="nationality" class="form-control" value="<?= htmlspecialchars($details['nationality'] ?? '') ?>">
    </div>

    <div class="col-md-6">
        <label class="form-label">RPPS</label>
        <input type="text" name="rpps" class="form-control" value="<?= htmlspecialchars($details['rpps'] ?? '') ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label">N° Sécurité Sociale</label>
        <input type="text" name="social_security_number" class="form-control" value="<?= htmlspecialchars($details['social_security_number'] ?? '') ?>">
    </div>

    <div class="col-md-6">
        <label class="form-label">Langue préférée</label>
        <input type="text" name="preferred_language" class="form-control" value="<?= htmlspecialchars($details['preferred_language'] ?? '') ?>">
    </div>
</div>
    <div class="col-12">
        <button type="submit" class="btn btn-primary">💾 Enregistrer les modifications</button>
    </div>
</form>
