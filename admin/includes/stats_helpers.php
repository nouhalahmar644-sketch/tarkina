<?php
/**
 * Shared mini-stats + status filter helpers for admin pages.
 * Each helper is idempotent — calling it multiple times on the same page
 * only prints its CSS once.
 */

function admin_stats_css(): void
{
    static $printed = false;
    if ($printed) return;
    $printed = true;
    ?>
    <style>
      .mini-stat-row { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:28px; }
      @media(max-width:900px){ .mini-stat-row { grid-template-columns:repeat(2,1fr); } }
      .mini-stat-card { background:var(--white,#fff); border-radius:16px; border:1px solid var(--border,#e2ddd8); padding:20px 18px 16px; box-shadow:0 4px 14px rgba(0,0,0,0.04); transition:transform .2s,box-shadow .2s; }
      .mini-stat-card:hover { transform:translateY(-3px); box-shadow:0 8px 22px rgba(0,0,0,0.08); }
      .msi { width:34px; height:34px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:17px; margin-bottom:12px; }
      .msi.navy  { background:#eef0f6; color:var(--navy,#0b1c30); }
      .msi.orange{ background:#fff4eb; color:var(--coral,#f16e22); }
      .msi.green { background:#eef6ee; color:#2e7d32; }
      .msi.red   { background:#fdecea; color:#c0392b; }
      .msi.purple{ background:#f0eaf7; color:#6f42c1; }
      .msl { font-size:10px; font-weight:800; letter-spacing:1.5px; text-transform:uppercase; color:#8892a4; margin-bottom:5px; }
      .msv { font-size:26px; font-weight:800; color:var(--navy,#0b1c30); line-height:1; margin-bottom:10px; }
      .msd { border:none; border-top:1px solid var(--border,#e2ddd8); margin:0 0 8px; }
      .mss { font-size:11px; color:#8892a4; line-height:1.5; }
      .mss.pos { color:#2e7d32; } .mss.neg { color:#c0392b; }

      /* Status filter chip row */
      .status-filter { display:inline-flex; align-items:center; gap:6px; flex-wrap:wrap; }
      .status-filter a { padding:6px 14px; border:1.5px solid var(--border,#e2ddd8); border-radius:50px; text-decoration:none; color:var(--navy,#0b1c30); font-size:.82rem; font-weight:600; transition:all .2s; background:#fff; }
      .status-filter a:hover { border-color:var(--coral,#f16e22); color:var(--coral,#f16e22); }
      .status-filter a.active { background:var(--coral,#f16e22); color:#fff; border-color:var(--coral,#f16e22); }
    </style>
    <?php
}

/**
 * Render 4 mini-stat cards (TOTAL / PUBLISHED / DRAFTS / VALUE) for a service-like
 * table that has a `statut` column and a numeric value column (e.g. `prix`).
 *
 * @param mysqli|object $conn
 * @param array $cfg keys:
 *   - table          (required) the SQL table name
 *   - label          uppercase noun shown on the first card (e.g. 'HÉBERGEMENTS')
 *   - value_col      numeric col summed for the value card (default 'prix'); pass '' to skip
 *   - icon_total / icon_active / icon_inactive / icon_value  bootstrap-icon class
 *   - unit           (default 'TND')
 *   - value_label    (default 'VALEUR CATALOGUE')
 *   - active_values  (default ['publié','actif'])
 *   - active_word    (default 'PUBLIÉS')
 *   - inactive_word  (default 'BROUILLONS')
 */
function admin_render_service_stats($conn, array $cfg): void
{
    admin_stats_css();
    $cfg = array_merge([
        'table'           => '',
        'label'           => '',
        'value_col'       => 'prix',
        'icon_total'      => 'bi-grid-3x3-gap',
        'icon_active'     => 'bi-eye',
        'icon_inactive'   => 'bi-eye-slash',
        'icon_value'      => 'bi-cash-coin',
        'unit'            => 'TND',
        'value_label'     => 'VALEUR CATALOGUE',
        'active_values'   => ['publié', 'actif'],
        'active_word'     => 'PUBLIÉS',
        'inactive_word'   => 'BROUILLONS',
    ], $cfg);

    if ($cfg['table'] === '') return;
    $table = '`' . str_replace('`', '', $cfg['table']) . '`';
    $valueCol = $cfg['value_col'] !== ''
        ? '`' . str_replace('`', '', $cfg['value_col']) . '`'
        : '';
    $escActives = array_map(static fn($v) => mysqli_real_escape_string($conn, (string) $v), $cfg['active_values']);
    $inList = "'" . implode("','", $escActives) . "'";

    $total = 0; $active = 0; $value = 0.0;
    $sumPiece = $valueCol !== '' ? "SUM(CASE WHEN statut IN ($inList) THEN $valueCol ELSE 0 END)" : '0';
    $sql = "SELECT COUNT(*) AS total,
                   SUM(statut IN ($inList)) AS active,
                   $sumPiece AS value
            FROM $table";
    try {
        $res = mysqli_query($conn, $sql);
        if ($res && $row = mysqli_fetch_assoc($res)) {
            $total  = (int) $row['total'];
            $active = (int) $row['active'];
            $value  = (float) $row['value'];
        }
    } catch (\Throwable $e) { /* table missing — render zeros */ }
    $inactive = max(0, $total - $active);
    $pct = $total > 0 ? round($active / $total * 100) : 0;
    ?>
    <div class="mini-stat-row">
      <div class="mini-stat-card">
        <div class="msi navy"><i class="bi <?= htmlspecialchars($cfg['icon_total']) ?>"></i></div>
        <div class="msl">TOTAL <?= htmlspecialchars(strtoupper($cfg['label'])) ?></div>
        <div class="msv"><?= $total ?></div>
        <hr class="msd">
        <div class="mss"><?= $total ?> élément(s)</div>
      </div>
      <div class="mini-stat-card">
        <div class="msi orange"><i class="bi <?= htmlspecialchars($cfg['icon_active']) ?>"></i></div>
        <div class="msl"><?= htmlspecialchars($cfg['active_word']) ?></div>
        <div class="msv"><?= $active ?></div>
        <hr class="msd">
        <div class="mss pos"><?= $pct ?>% du total</div>
      </div>
      <div class="mini-stat-card">
        <div class="msi red"><i class="bi <?= htmlspecialchars($cfg['icon_inactive']) ?>"></i></div>
        <div class="msl"><?= htmlspecialchars($cfg['inactive_word']) ?></div>
        <div class="msv"><?= $inactive ?></div>
        <hr class="msd">
        <div class="mss neg">Non publiés</div>
      </div>
      <div class="mini-stat-card">
        <div class="msi green"><i class="bi <?= htmlspecialchars($cfg['icon_value']) ?>"></i></div>
        <div class="msl"><?= htmlspecialchars($cfg['value_label']) ?></div>
        <div class="msv" style="font-size:18px;"><?= number_format($value, 2, '.', ' ') ?> <?= htmlspecialchars($cfg['unit']) ?></div>
        <hr class="msd">
        <div class="mss pos">Prix cumulés des publiés</div>
      </div>
    </div>
    <?php
}

/**
 * Render a row of pill links that filter by ?statut=...
 *
 * @param string $current The currently selected 'statut' value ('' = Tous)
 * @param string $base    Base URL (e.g. 'content.php')
 * @param array  $extra   Extra GET params to preserve (e.g. ['q' => 'foo'])
 * @param array  $options Optional list of ['value'=>..,'label'=>..]; default = Tous/Publiés/Brouillons
 */
function admin_render_status_filter(string $current, string $base, array $extra = [], array $options = [], string $paramName = 'statut'): void
{
    admin_stats_css();
    if (!$options) {
        $options = [
            ['value' => '',          'label' => 'Tous'],
            ['value' => 'publié',    'label' => 'Publiés'],
            ['value' => 'brouillon', 'label' => 'Brouillons'],
        ];
    }
    echo '<div class="status-filter">';
    foreach ($options as $opt) {
        $params = array_filter($extra, static fn($v) => $v !== '' && $v !== null);
        if ($opt['value'] !== '') {
            $params[$paramName] = $opt['value'];
        }
        $url = $base . ($params ? '?' . http_build_query($params) : '');
        $isActive = $current === $opt['value'];
        echo '<a href="' . htmlspecialchars($url) . '" class="' . ($isActive ? 'active' : '') . '">'
           . htmlspecialchars($opt['label']) . '</a>';
    }
    echo '</div>';
}
