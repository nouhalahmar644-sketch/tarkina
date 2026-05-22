<?php
if (!isset($pageTitle)) {
    $pageTitle = 'Tableau de bord Admin';
}

if (!isset($pageHeading)) {
    $pageHeading = 'Tableau de bord';
}

$adminName = isset($_SESSION['user_name']) ? (string) $_SESSION['user_name'] : 'Admin';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($pageTitle); ?> - Tarkina Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --navy: #1B2A4A;
    --coral: #E8603C;
    --cream: #FAF7F2;
    --charcoal: #2D2D2D;
    --grey: #8A8A8A;
    --border: #E2DDD6;
    --white: #ffffff;
    --panel: #f7f4ef;
  }

  body {
    font-family: 'DM Sans', sans-serif;
    background: var(--charcoal);
    color: #222;
    min-height: 100vh;
  }

  .admin-shell {
    display: flex;
    min-height: 100vh;
  }

  .admin-sidebar {
    width: 250px;
    background: var(--navy);
    color: var(--white);
    padding: 28px 20px;
    display: flex;
    flex-direction: column;
    border-right: 1px solid rgba(255, 255, 255, 0.08);
  }

  .brand {
    font-family: 'Playfair Display', serif;
    font-size: 30px;
    font-weight: 700;
    margin-bottom: 28px;
    letter-spacing: -0.5px;
  }

  .brand span {
    color: var(--coral);
  }

  .sidebar-nav {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .sidebar-link {
    display: block;
    border-radius: 10px;
    padding: 11px 14px;
    text-decoration: none;
    color: rgba(255, 255, 255, 0.85);
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s ease;
  }

  .sidebar-link:hover {
    background: rgba(255, 255, 255, 0.11);
    color: #fff;
  }

  .sidebar-link.active {
    background: var(--coral);
    color: #fff;
  }

  .admin-content {
    flex: 1;
    padding: 24px;
  }

  .content-wrap {
    background: var(--white);
    border-radius: 20px;
    min-height: calc(100vh - 48px);
    box-shadow: 0 26px 50px rgba(0,0,0,0.25);
    overflow: hidden;
  }

  .topbar {
    padding: 24px 30px;
    border-bottom: 1px solid var(--border);
    background: linear-gradient(180deg, #fff 0%, #fbf9f5 100%);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
  }

  .topbar h1 {
    font-family: 'Playfair Display', serif;
    color: var(--navy);
    font-size: 28px;
    font-weight: 700;
  }

  .admin-chip {
    background: var(--panel);
    border: 1px solid var(--border);
    border-radius: 999px;
    padding: 8px 14px;
    color: #444;
    font-size: 13px;
  }

  .page-body {
    padding: 24px 30px 32px;
  }

  .card-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
  }

  .stat-card {
    border: 1px solid var(--border);
    border-radius: 14px;
    background: var(--panel);
    padding: 20px;
  }

  .stat-label {
    color: var(--grey);
    font-size: 13px;
    margin-bottom: 10px;
  }

  .stat-value {
    color: var(--navy);
    font-size: 32px;
    font-weight: 700;
    font-family: 'Playfair Display', serif;
    line-height: 1;
  }

  .muted {
    margin-top: 8px;
    color: #6f6f6f;
    font-size: 12px;
  }

  .toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 16px;
  }

  .search-form {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }

  .search-input {
    min-width: 260px;
    padding: 10px 12px;
    border: 1.5px solid var(--border);
    border-radius: 10px;
    background: var(--cream);
    outline: none;
    font-family: 'DM Sans', sans-serif;
    font-size: 14px;
  }

  .search-input:focus {
    border-color: var(--coral);
    box-shadow: 0 0 0 4px rgba(232,96,60,0.08);
    background: var(--white);
  }

  .btn-small {
    display: inline-block;
    border: none;
    border-radius: 10px;
    padding: 9px 12px;
    text-decoration: none;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    font-family: 'DM Sans', sans-serif;
  }

  .btn-navy {
    background: var(--navy);
    color: #fff;
  }

  .btn-coral {
    background: var(--coral);
    color: #fff;
  }

  .btn-soft {
    background: #f1eee9;
    color: #2d2d2d;
    border: 1px solid var(--border);
  }

  .table-wrap {
    border: 1px solid var(--border);
    border-radius: 14px;
    overflow-x: auto;
    background: #fff;
  }

  table.data-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 760px;
  }

  .data-table th,
  .data-table td {
    padding: 12px 14px;
    border-bottom: 1px solid #f0ece6;
    text-align: left;
    font-size: 13px;
    vertical-align: middle;
  }

  .data-table th {
    color: var(--navy);
    font-size: 12px;
    letter-spacing: 0.3px;
    text-transform: uppercase;
    background: #fbf9f6;
  }

  .role-pill {
    display: inline-block;
    padding: 4px 9px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 600;
    background: #edf0f7;
    color: var(--navy);
  }

  .role-pill.admin {
    background: rgba(232, 96, 60, 0.14);
    color: #b6492e;
  }

  .actions {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
  }

  .inline-form {
    display: inline;
  }

  .flash {
    margin-bottom: 14px;
    font-size: 13px;
    line-height: 1.4;
    padding: 10px 12px;
    border-radius: 10px;
  }

  .flash.success {
    color: #1f7a43;
    background: #eaf8f0;
    border: 1px solid #b6e2c6;
  }

  .flash.error {
    color: #b43737;
    background: #fff1f1;
    border: 1px solid #f1c9c9;
  }

  .pagination {
    margin-top: 14px;
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
  }

  .form-card {
    border: 1px solid var(--border);
    border-radius: 14px;
    background: #fbf9f6;
    padding: 16px;
    margin-bottom: 16px;
  }

  .form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 12px;
  }

  .form-field label {
    display: block;
    margin-bottom: 6px;
    color: var(--navy);
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.4px;
    text-transform: uppercase;
  }

  .form-input,
  .form-select,
  .form-textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1.5px solid var(--border);
    border-radius: 10px;
    background: var(--white);
    font-family: 'DM Sans', sans-serif;
    font-size: 14px;
    outline: none;
  }

  .form-input:focus,
  .form-select:focus,
  .form-textarea:focus {
    border-color: var(--coral);
    box-shadow: 0 0 0 4px rgba(232,96,60,0.08);
  }

  /* Custom File Input Styling */
  .custom-file-wrap {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-top: 6px;
  }
  .custom-file-wrap input[type="file"] {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0,0,0,0);
    border: 0;
  }
  .custom-file-btn {
    display: inline-block;
    padding: 8px 14px;
    background: #fff;
    color: var(--charcoal);
    border: 1.5px solid var(--border);
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
  }
  .custom-file-btn:hover {
    border-color: var(--navy);
  }
  .custom-file-wrap input[type="file"]:focus + .custom-file-btn {
    border-color: var(--coral);
    box-shadow: 0 0 0 4px rgba(232,96,60,0.08);
  }
  .custom-file-name {
    font-size: 13px;
    color: var(--grey);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 150px;
  }

  .form-textarea {
    min-height: 100px;
    resize: vertical;
  }

  .cards-row {
    margin-bottom: 16px;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 12px;
  }

  .mini-card {
    border: 1px solid var(--border);
    border-radius: 12px;
    background: var(--panel);
    padding: 14px;
  }

  .mini-card h4 {
    color: var(--navy);
    font-size: 15px;
    margin-bottom: 8px;
  }

  @media (max-width: 900px) {
    .admin-sidebar {
      width: 210px;
      padding: 20px 15px;
    }
    .topbar h1 {
      font-size: 24px;
    }
  }

  @media (max-width: 760px) {
    .admin-shell {
      flex-direction: column;
    }
    .admin-sidebar {
      width: 100%;
      border-right: none;
      border-bottom: 1px solid rgba(255,255,255,0.12);
    }
    .content-wrap {
      border-radius: 0;
      min-height: auto;
    }
  }
</style>
</head>
<body>
<div class="admin-shell">
