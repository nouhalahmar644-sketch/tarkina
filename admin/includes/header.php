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
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="../assets/css/typography.css">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --navy: #0b1c30;
    --coral: #f16e22;
    --cream: #FFFFFF;
    --charcoal: #2D2D2D;
    --grey: #8A8A8A;
    --border: #E2DDD6;
    --white: #ffffff;
    --panel: #f7f4ef;
    --bg-light: #f3f5f8;
  }

  body {
    font-family: 'DM Sans', sans-serif;
    background: var(--bg-light);
    color: #333;
    min-height: 100vh;
  }

  .admin-shell {
    display: flex;
    min-height: 100vh;
  }

  /* Left Sidebar (Navy) */
  .admin-sidebar {
    width: 250px;
    background: var(--navy);
    color: var(--white);
    padding: 28px 20px;
    display: flex;
    flex-direction: column;
    border-right: 1px solid rgba(255, 255, 255, 0.05);
    flex-shrink: 0;
  }

  .brand {
    font-family: 'Playfair Display', serif;
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 32px;
    letter-spacing: -0.5px;
    padding-left: 10px;
  }

  .brand span {
    color: var(--coral);
  }

  .sidebar-nav {
    display: flex;
    flex-direction: column;
    gap: 8px;
    flex: 1;
  }

  .sidebar-link {
    display: flex;
    align-items: center;
    gap: 14px;
    border-radius: 12px;
    padding: 12px 16px;
    text-decoration: none;
    color: rgba(255, 255, 255, 0.65);
    font-size: 14px;
    font-weight: 600;
    transition: all 0.3s ease;
  }

  .sidebar-link i {
    font-size: 18px;
  }

  .sidebar-link:hover {
    color: #fff;
  }

  .sidebar-link.active {
    background: #f16e22;
    color: #fff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }
  
  .sidebar-link.active i {
    color: #fff;
  }

  /* Upgrade / Pro Banner */
  .upgrade-card {
    background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.02) 100%);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 16px;
    padding: 20px;
    text-align: center;
    margin-top: 20px;
  }
  
  .upgrade-card h4 {
    font-size: 14px;
    margin-bottom: 8px;
    color: #fff;
  }
  
  .upgrade-card p {
    font-size: 12px;
    color: rgba(255,255,255,0.6);
    margin-bottom: 12px;
    line-height: 1.4;
  }
  
  .btn-upgrade {
    background: var(--coral);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 8px 16px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
  }

  /* Main Content Area */
  .admin-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-width: 0;
  }

  .content-wrap {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 24px 32px;
  }

  .topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
  }

  .topbar h1 {
    font-size: 24px;
    font-weight: 700;
    color: var(--navy);
  }

  .admin-chip {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 999px;
    padding: 8px 16px;
    color: var(--navy);
    font-weight: 600;
    font-size: 13px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.02);
  }

  .topbar-greeting h1 {
    font-size: 24px;
    font-weight: 700;
    color: var(--navy);
  }

  .search-wrapper {
    position: relative;
    width: 320px;
  }

  .search-wrapper i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--grey);
  }

  .search-wrapper input {
    width: 100%;
    padding: 12px 16px 12px 40px;
    border: none;
    border-radius: 999px;
    background: var(--white);
    font-family: 'DM Sans', sans-serif;
    font-size: 14px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.03);
    outline: none;
  }

  .search-wrapper input:focus {
    box-shadow: 0 0 0 3px rgba(241,110,34,0.15);
  }

  .page-body {
    background: var(--white);
    border-radius: 20px;
    padding: 24px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.03);
  }
  
  /* Fallback for other pages */
  .fallback-body .page-body {
    flex: 1;
  }

  .card-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
  }

  .stat-card {
    border: 1px solid var(--border);
    border-radius: 16px;
    background: var(--white);
    padding: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.02);
  }

  .stat-label {
    color: var(--grey);
    font-size: 13px;
    margin-bottom: 10px;
    font-weight: 600;
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
    color: #999;
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
    box-shadow: 0 0 0 4px rgba(241,110,34,0.1);
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
    background: rgba(241,110,34,0.14);
    color: #b35000;
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
    box-shadow: 0 0 0 4px rgba(241,110,34,0.1);
  }

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
    box-shadow: 0 0 0 4px rgba(241,110,34,0.1);
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
    .topbar-greeting h1 {
      font-size: 20px;
    }
  }

  @media (max-width: 760px) {
    .admin-shell {
      flex-direction: column;
    }
    .admin-sidebar {
      width: 100%;
      border-right: none;
    }
  }

  /* Premium Preview Cards Grid */
  .popular-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    margin-bottom: 24px;
  }
  .pop-card {
    background: var(--white);
    border-radius: 16px;
    border: 1px solid var(--border);
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.02);
    display: flex;
    flex-direction: column;
  }
  .pop-card-img {
    height: 140px;
    background: var(--cream);
    position: relative;
  }
  .pop-card-img img { width: 100%; height: 100%; object-fit: cover; }
  .pop-badge { position: absolute; top: 12px; left: 12px; background: var(--coral); color: #fff; padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: 700; box-shadow: 0 2px 8px rgba(0,0,0,0.2); }
  .pop-card-body { padding: 16px; flex: 1; display: flex; flex-direction: column; justify-content: space-between; }
  .pop-card-title { font-family: 'Playfair Display', serif; font-size: 18px; font-weight: 700; margin-bottom: 8px; color: var(--navy); }
  .pop-card-price { font-size: 14px; font-weight: 600; color: var(--navy); display: flex; justify-content: space-between; align-items: center; }
  .pop-btn { width: 32px; height: 32px; background: var(--navy); color: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center; border: none; cursor: pointer; transition: background 0.2s; text-decoration: none; }
  .pop-btn:hover { background: var(--coral); }
</style>
</head>
<body>
<div class="admin-shell">


