<?php
require 'db.php';
session_start();

$stories = mysqli_query($conn, "
  SELECT s.*, 
         u.prenom, u.nom, u.photo_profil,
         r.nom AS region_nom
  FROM stories s
  JOIN utilisateur u ON s.utilisateur_id = u.id
  LEFT JOIN region r ON s.region_id = r.id
  ORDER BY s.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tarkina Stories</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <style>
    .stories-hero {
      background: linear-gradient(135deg, #1B3A4B 0%, #E05A2B 100%);
      padding: 80px 0 50px;
      text-align: center;
      color: #fff;
    }
    .stories-hero h1 {
      font-size: 2.8rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
    }
    .stories-hero p {
      font-size: 1.1rem;
      opacity: 0.85;
    }
    .stories-grid {
      columns: 3;
      column-gap: 1.2rem;
      padding: 3rem 0;
    }
    @media (max-width: 992px) { .stories-grid { columns: 2; } }
    @media (max-width: 576px) { .stories-grid { columns: 1; } }

    .story-card {
      break-inside: avoid;
      background: #fff;
      border-radius: 16px;
      overflow: hidden;
      margin-bottom: 1.2rem;
      border: 1px solid #e2ddd8;
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .story-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 32px rgba(27,58,75,0.12);
    }
    .story-card img {
      width: 100%;
      display: block;
      object-fit: cover;
    }
    .story-card-body {
      padding: 14px 16px;
    }
    .story-region-tag {
      display: inline-block;
      background: #fdeee8;
      color: #E05A2B;
      border-radius: 999px;
      font-size: 11px;
      font-weight: 600;
      padding: 3px 10px;
      margin-bottom: 8px;
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }
    .story-caption {
      font-size: 14px;
      color: #333;
      margin-bottom: 12px;
      line-height: 1.5;
    }
    .story-footer {
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-top: 1px solid #f0ece7;
      padding-top: 10px;
      margin-top: 4px;
    }
    .story-author {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .story-avatar {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      object-fit: cover;
      background: #e2ddd8;
    }
    .story-author-name {
      font-size: 13px;
      font-weight: 500;
      color: #1B3A4B;
    }
    .like-btn {
      background: none;
      border: 1.5px solid #e2ddd8;
      border-radius: 999px;
      padding: 4px 12px;
      font-size: 13px;
      color: #888;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 5px;
      transition: all 0.18s;
    }
    .like-btn:hover, .like-btn.liked {
      background: #fdeee8;
      border-color: #E05A2B;
      color: #E05A2B;
    }
    .add-story-btn {
      background: #E05A2B;
      color: #fff;
      border: none;
      border-radius: 999px;
      padding: 10px 28px;
      font-size: 15px;
      font-weight: 600;
      text-decoration: none;
      display: inline-block;
      margin-top: 1.2rem;
      transition: background 0.18s;
    }
    .add-story-btn:hover { background: #c94e22; color: #fff; }
    .empty-state {
      text-align: center;
      padding: 60px 0;
      color: #aaa;
    }
    .empty-state i { font-size: 3rem; margin-bottom: 1rem; display: block; }
  </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="stories-hero">
  <div class="container">
    <h1>Tarkina Stories</h1>
    <p>Des voyageurs partagent leurs vraies expériences en Tunisie</p>
    <?php if (isset($_SESSION['user_id'])): ?>
      <a href="story-add.php" class="add-story-btn">
        <i class="bi bi-plus-lg"></i> Partager mon expérience
      </a>
    <?php else: ?>
      <a href="login.php" class="add-story-btn">Connectez-vous pour partager</a>
    <?php endif; ?>
  </div>
</div>

<div class="container">
  <?php if (mysqli_num_rows($stories) === 0): ?>
    <div class="empty-state">
      <i class="bi bi-images"></i>
      <p>Aucune story pour l'instant. Soyez le premier à partager !</p>
    </div>
  <?php else: ?>
    <div class="stories-grid">
      <?php while ($s = mysqli_fetch_assoc($stories)): ?>
        <div class="story-card" id="story-<?= $s['id'] ?>">
          <img src="uploads/stories/<?= htmlspecialchars($s['photo']) ?>" alt="Story">
          <div class="story-card-body">
            <?php if ($s['region_nom']): ?>
              <span class="story-region-tag">
                <i class="bi bi-geo-alt-fill"></i> <?= htmlspecialchars($s['region_nom']) ?>
              </span>
            <?php endif; ?>
            <p class="story-caption"><?= htmlspecialchars($s['caption']) ?></p>
            <div class="story-footer">
              <div class="story-author">
                <?php if ($s['photo_profil']): ?>
                  <img src="uploads/profils/<?= htmlspecialchars($s['photo_profil']) ?>" class="story-avatar" alt="Avatar">
                <?php else: ?>
                  <div class="story-avatar" style="background:#e2ddd8;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:600;color:#1B3A4B;">
                    <?= strtoupper(substr($s['prenom'],0,1)) ?>
                  </div>
                <?php endif; ?>
                <span class="story-author-name"><?= htmlspecialchars($s['prenom'].' '.$s['nom']) ?></span>
              </div>
              <div style="display:flex;align-items:center;gap:8px;">
                <button class="like-btn" onclick="likeStory(<?= $s['id'] ?>, this)">
                  <i class="bi bi-heart"></i>
                  <span class="like-count"><?= $s['likes'] ?></span>
                </button>
                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $s['utilisateur_id']): ?>
                  <form method="POST" action="story-delete.php" onsubmit="return confirm('Supprimer cette story ?')">
                    <input type="hidden" name="id" value="<?= $s['id'] ?>">
                    <button type="submit" class="like-btn" style="border-color:#ffb3b3;color:#cc3333;">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function likeStory(id, btn) {
  fetch('story-like.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'id=' + id
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      btn.querySelector('.like-count').textContent = data.likes;
      btn.classList.toggle('liked');
      btn.querySelector('i').className = btn.classList.contains('liked') ? 'bi bi-heart-fill' : 'bi bi-heart';
    }
  });
}
</script>
</body>
</html>
