<?php
// Variables esperadas en scope: $veterinarias (array), $veterinaria_id (int)
$_esAdmin = ($_SESSION['usuario_rol'] ?? '') === 'admin';
$_nomVet  = '';
foreach ($veterinarias as $_v) {
    if ((int)$_v['id'] === (int)$veterinaria_id) { $_nomVet = $_v['nombre']; break; }
}
?>
<?php if ($_esAdmin): ?>
<div class="input-group input-group-sm" style="width:210px;">
    <span class="input-group-text bg-white border-end-0">
        <i class="bi bi-building text-primary"></i>
    </span>
    <select id="vet-selector" class="form-select border-start-0 ps-0 fw-semibold"
            style="font-size:.82rem;" data-current="<?= $veterinaria_id ?>">
        <?php foreach ($veterinarias as $_v): ?>
        <option value="<?= $_v['id'] ?>" <?= (int)$veterinaria_id === (int)$_v['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($_v['nombre']) ?>
        </option>
        <?php endforeach; ?>
    </select>
</div>
<?php else: ?>
<div class="d-flex align-items-center gap-1 px-3 py-1 rounded-pill border fw-semibold text-truncate"
     style="font-size:.82rem; background:#f0f4ff; border-color:#c7d4f5 !important; max-width:220px; height:31px; cursor:default;"
     title="Sucursal asignada — no modificable">
    <i class="bi bi-building text-primary" style="font-size:.85rem; flex-shrink:0;"></i>
    <span class="text-truncate" style="color:#3730a3;"><?= htmlspecialchars($_nomVet) ?></span>
    <i class="bi bi-lock-fill ms-1" style="font-size:.7rem; color:#6b7280; flex-shrink:0;"></i>
</div>
<?php endif; ?>
