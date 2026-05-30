<?php
/**
 * Region-photo helper.
 *
 * Why this exists:
 *   Admin uploads change the DATABASE (region.photo_principale), but a
 *   plain `git push` only carries the code + the uploaded files in
 *   uploads/regions/. The DB rows that say "Chenini's photo is reg_xxx.jpg"
 *   never reach the collaborator's machine. Result: same image (or default)
 *   on every region card.
 *
 *   Fix: a small JSON file `data/region_photos.json` mapping
 *   region_id -> image_path. The file IS code (committed via git), so when
 *   the collaborator pulls, they automatically get the mapping. Display
 *   pages fall back to this when their DB has no photo_principale, and the
 *   admin form auto-updates the file on save.
 */

if (!function_exists('region_photo_manifest_path')) {
    function region_photo_manifest_path(): string {
        return __DIR__ . '/../data/region_photos.json';
    }
}

if (!function_exists('region_photo_manifest_load')) {
    function region_photo_manifest_load(): array {
        $p = region_photo_manifest_path();
        if (!is_file($p)) return [];
        $raw = @file_get_contents($p);
        if ($raw === false || $raw === '') return [];
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }
}

if (!function_exists('region_photo_fallback')) {
    /**
     * Returns the image path stored in the manifest for $region_id, or ''.
     * Use as a fallback when DB.region.photo_principale is empty/invalid.
     */
    function region_photo_fallback(int $region_id): string {
        $data = region_photo_manifest_load();
        $key  = (string) $region_id;
        return isset($data[$key]) && is_string($data[$key]) ? $data[$key] : '';
    }
}

if (!function_exists('region_photo_manifest_set')) {
    /**
     * Update the manifest for $region_id. Pass an empty $photo_path to remove
     * the entry. Called by admin/region.php after saving a region photo, so
     * the mapping propagates via git.
     */
    function region_photo_manifest_set(int $region_id, string $photo_path): bool {
        $p    = region_photo_manifest_path();
        $data = region_photo_manifest_load();
        $key  = (string) $region_id;
        if ($photo_path === '') { unset($data[$key]); }
        else { $data[$key] = $photo_path; }
        $dir = dirname($p);
        if (!is_dir($dir)) { @mkdir($dir, 0755, true); }
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return @file_put_contents($p, $json) !== false;
    }
}
