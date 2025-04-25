<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">Civilité <span class="text-danger">*</span></label>
        <select name="civility" class="form-select" required>
            <option value="">–</option>
            <option value="Monsieur" <?= ($details['civility'] ?? '') === 'Monsieur' ? 'selected' : '' ?>>Monsieur</option>
            <option value="Madame" <?= ($details['civility'] ?? '') === 'Madame' ? 'selected' : '' ?>>Madame</option>
        </select>
    </div>

    <div class="col-md-8">
        <label class="form-label">Adresse <span class="text-danger">*</span></label>
        <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($details['address'] ?? '') ?>" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">Métier <span class="text-danger">*</span></label>
        <input type="text" name="job" class="form-control" value="<?= htmlspecialchars($details['job'] ?? '') ?>" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">Spécialité <span class="text-danger">*</span></label>
        <input type="text" name="specialty" class="form-control" value="<?= htmlspecialchars($details['specialty'] ?? '') ?>" required>
    </div>

    <div class="col-md-4">
        <label class="form-label">Date de naissance <span class="text-danger">*</span></label>
        <input type="date" name="birthdate" class="form-control" value="<?= $details['birthdate'] ?? '' ?>" required>
    </div>

    <div class="col-md-4">
        <label class="form-label">Lieu de naissance <span class="text-danger">*</span></label>
        <input type="text" name="birth_place" class="form-control" value="<?= htmlspecialchars($details['birth_place'] ?? '') ?>" required>
    </div>

    <div class="col-md-4">
        <label class="form-label">Nationalité <span class="text-danger">*</span></label>
        <input type="text" name="nationality" class="form-control" value="<?= htmlspecialchars($details['nationality'] ?? '') ?>" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">RPPS</label>
        <input type="text" name="rpps" class="form-control" value="<?= htmlspecialchars($details['rpps'] ?? '') ?>">
    </div>

    <div class="col-md-6">
        <label class="form-label">N° Sécurité Sociale <span class="text-danger">*</span></label>
        <input type="text" name="social_security_number" class="form-control" value="<?= htmlspecialchars($details['social_security_number'] ?? '') ?>" required>
    </div>

    <div class="col-12 text-end mt-3">
        <button type="submit" class="btn btn-primary">
            <i class="fa fa-save me-2"></i>Enregistrer les modifications
        </button>
    </div>
</div>
