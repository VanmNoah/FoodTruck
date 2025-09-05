<?php
session_start();
/*if (!isset($_SESSION['admin'])) {
  header('Location: login.php');
  exit;
}*/

require_once '../includes/db.php';
$menuStmt = $pdo->query("SELECT * FROM menu");
$menuItems = $menuStmt->fetchAll();

$categoryStmt = $pdo->query("SELECT * FROM categories");
$allCategories = $categoryStmt->fetchAll();


$eventStmt = $pdo->query("SELECT * FROM events");
$events = $eventStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="nl">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
  <link rel="stylesheet" href="../assets/css/dashboard.css">
  <script>
    function toggleAddForm(formId) {
      const form = document.getElementById(formId);
      if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
      } else {
        form.style.display = 'none';
      }
    }

    function toggleSidebar() {
      const sidebar = document.querySelector('.sidebar');
      sidebar.classList.toggle('active');
    }

    function confirmDelete(itemId, itemType) {
      return confirm(`Weet je zeker dat je dit ${itemType} wilt verwijderen?`);
    }
  </script>
</head>

<body>
  <div class="admin-container">
    <aside class="sidebar">
      <div class="sidebar-header">
        <span class="logo">RB</span>
        <h1>Restaurant Beheer</h1>
      </div>
      <ul class="nav-menu">
        <li class="nav-item">
          <a href="#" class="nav-link active">
            <i class="fas fa-home"></i> Dashboard
          </a>
        </li>
        <li class="nav-item">
          <a href="#menu-section" class="nav-link">
            <i class="fas fa-utensils"></i> Menu Beheer
          </a>
        </li>
        <li class="nav-item">
          <a href="#events-section" class="nav-link">
            <i class="fas fa-calendar-alt"></i> Evenementen
          </a>
        </li>
        <li class="nav-item">
          <a href="#categories-section" class="nav-link">
            <i class="fas fa-tags"></i> Categorieën
          </a>
        </li>
        <li class="nav-item">
          <a href="logout.php" class="nav-link">
            <i class="fas fa-sign-out-alt"></i> Uitloggen
          </a>
        </li>
      </ul>
    </aside>
    <main class="main-content">
      <div class="topbar">
        <button class="toggle-btn" onclick="toggleSidebar()">
          <i class="fas fa-bars"></i>
        </button>
        <h2>Admin Dashboard</h2>
        <div class="user-actions">
          <a href="logout.php" class="btn btn-danger">
            <i class="fas fa-sign-out-alt"></i> Uitloggen
          </a>
        </div>
      </div>
      <div id="menu-section" class="form-section">
        <div class="section-header">
          <h3><i class="fas fa-utensils"></i> Menu Beheer</h3>
          <button type="button" class="btn btn-primary" onclick="toggleAddForm('add-menu-form')">
            <i class="fas fa-plus"></i> Nieuw Menu Item
          </button>
        </div>

        <div class="form-container">
          <div id="add-menu-form" class="add-new-section">
            <h4>Nieuw Menu Item Toevoegen</h4>
            <form method="post" action="actions/add/add_menu_item.php">
              <div class="form-group">
                <label for="menu-title">Titel:</label>
                <input type="text" id="menu-title" class="form-control" name="title" required>
              </div>

              <div class="form-group">
                <label for="menu-description">Beschrijving:</label>
                <textarea id="menu-description" class="form-control" name="description" required></textarea>
              </div>

              <div class="form-group">
                <label for="menu-price">Prijs (€):</label>
                <input type="number" id="menu-price" class="form-control" step="0.01" name="price" required>
              </div>

              <div class="form-group">
                <label>Categorieën:</label>
                <div class="category-container">
                  <?php if (!empty($allCategories)): ?>
                    <?php foreach ($allCategories as $cat): ?>
                      <label class="category-checkbox">
                        <input type="checkbox" name="categories[]" value="<?= $cat['id'] ?>">
                        <?= htmlspecialchars($cat['name']) ?>
                      </label>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <p>Geen categorieën beschikbaar. <a href="#" onclick="toggleAddForm('add-category-form')">Voeg eerst
                        categorieën toe</a>.</p>
                  <?php endif; ?>
                </div>
              </div>

              <div class="form-actions">
                <button type="submit" class="btn btn-success">
                  <i class="fas fa-save"></i> Toevoegen
                </button>
              </div>
            </form>
          </div>

          <?php if (empty($menuItems)): ?>
            <div class="empty-message">
              <i class="fas fa-info-circle"></i>
              Er zijn nog geen menu-items. Voeg een nieuw item toe met de knop hierboven.
            </div>
          <?php else: ?>
            <form method="post" action="actions/save/save_menu.php">
              <?php foreach ($menuItems as $item): ?>
                <div class="card">
                  <input type="hidden" name="menu[<?= $item['id'] ?>][id]" value="<?= $item['id'] ?>">

                  <div class="form-group">
                    <label>Titel:</label>
                    <input type="text" class="form-control" name="menu[<?= $item['id'] ?>][title]"
                      value="<?= htmlspecialchars($item['title']) ?>">
                  </div>

                  <div class="form-group">
                    <label>Beschrijving:</label>
                    <textarea class="form-control"
                      name="menu[<?= $item['id'] ?>][description]"><?= htmlspecialchars($item['description']) ?></textarea>
                  </div>

                  <div class="form-group">
                    <label>Prijs (€):</label>
                    <input type="number" class="form-control" step="0.01" name="menu[<?= $item['id'] ?>][price]"
                      value="<?= htmlspecialchars($item['price']) ?>">
                  </div>

                  <div class="form-group">
                    <label>Categorieën:</label>
                    <div class="category-container">
                      <?php foreach ($allCategories as $cat): ?>
                        <label class="category-checkbox">
                          <input type="checkbox" name="menu[<?= $item['id'] ?>][categories][]" value="<?= $cat['id'] ?>"
                            <?= in_array($cat['id'], $menuCategories[$item['id']] ?? []) ? 'checked' : '' ?>>
                          <?= htmlspecialchars($cat['name']) ?>
                        </label>
                      <?php endforeach; ?>
                    </div>
                  </div>

                  <div class="form-actions">
                    <a href="actions/delete/delete_menu_item.php?id=<?= $item['id'] ?>" class="btn btn-danger"
                      onclick="return confirmDelete(<?= $item['id'] ?>, 'menu item')">
                      <i class="fas fa-trash"></i> Verwijderen
                    </a>
                  </div>
                </div>
              <?php endforeach; ?>

              <div class="form-actions">
                <button type="submit" class="btn btn-success">
                  <i class="fas fa-save"></i> Wijzigingen Opslaan
                </button>
              </div>
            </form>
          <?php endif; ?>
        </div>
      </div>
      <div id="events-section" class="form-section">
        <div class="section-header">
          <h3><i class="fas fa-calendar-alt"></i> Evenementen Beheer</h3>
          <button type="button" class="btn btn-primary" onclick="toggleAddForm('add-event-form')">
            <i class="fas fa-plus"></i> Nieuw Event
          </button>
        </div>
        <div class="form-container">
          <div id="add-event-form" class="add-new-section">
            <h4>Nieuw Event Toevoegen</h4>
            <form method="post" action="actions/add/add_event.php">
              <div class="form-group">
                <label for="event-name">Naam:</label>
                <input type="text" id="event-name" class="form-control" name="name" required>
              </div>

              <div class="form-group">
                <label for="event-location">Locatie:</label>
                <input type="text" id="event-location" class="form-control" name="location" required>
              </div>

              <div class="form-group">
                <label for="event-date">Datum:</label>
                <input type="date" id="event-date" class="form-control" name="date" required>
              </div>

              <div class="form-group">
                <label for="event-time">Tijd:</label>
                <input type="time" id="event-time" class="form-control" name="time" required>
              </div>

              <div class="form-actions">
                <button type="submit" class="btn btn-success">
                  <i class="fas fa-save"></i> Toevoegen
                </button>
              </div>
            </form>
          </div>

          <?php if (empty($events)): ?>
            <div class="empty-message">
              <i class="fas fa-info-circle"></i>
              Er zijn nog geen events. Voeg een nieuw event toe met de knop hierboven.
            </div>
          <?php else: ?>
            <form method="post" action="actions/save/save_events.php">
              <?php foreach ($events as $event): ?>
                <div class="card">
                  <input type="hidden" name="events[<?= $event['id'] ?>][id]" value="<?= $event['id'] ?>">

                  <div class="form-group">
                    <label>Naam:</label>
                    <input type="text" class="form-control" name="events[<?= $event['id'] ?>][name]"
                      value="<?= htmlspecialchars($event['name']) ?>">
                  </div>

                  <div class="form-group">
                    <label>Locatie:</label>
                    <input type="text" class="form-control" name="events[<?= $event['id'] ?>][location]"
                      value="<?= htmlspecialchars($event['location']) ?>">
                  </div>

                  <div class="form-group">
                    <label>Datum:</label>
                    <input type="date" class="form-control" name="events[<?= $event['id'] ?>][date]"
                      value="<?= $event['date'] ?>">
                  </div>

                  <div class="form-group">
                    <label>Tijd:</label>
                    <input type="time" class="form-control" name="events[<?= $event['id'] ?>][time]"
                      value="<?= $event['time'] ?>">
                  </div>

                  <div class="form-actions">
                    <a href="actions/delete/delete_event.php?id=<?= $event['id'] ?>" class="btn btn-danger"
                      onclick="return confirmDelete(<?= $event['id'] ?>, 'event')">
                      <i class="fas fa-trash"></i> Verwijderen
                    </a>
                  </div>
                </div>
              <?php endforeach; ?>

              <div class="form-actions">
                <button type="submit" class="btn btn-success">
                  <i class="fas fa-save"></i> Wijzigingen Opslaan
                </button>
              </div>
            </form>
          <?php endif; ?>
        </div>
      </div>
      <div id="categories-section" class="form-section">
        <div class="section-header">
          <h3><i class="fas fa-tags"></i> Categorieën Beheer</h3>
          <button type="button" class="btn btn-primary" onclick="toggleAddForm('add-category-form')">
            <i class="fas fa-plus"></i> Nieuwe Categorie
          </button>
        </div>
        <div class="form-container">
          <div id="add-category-form" class="add-new-section">
            <h4>Nieuwe Categorie Toevoegen</h4>
            <form method="post" action="actions/add/add_category.php">
              <div class="form-group">
                <label for="category-name">Naam:</label>
                <input type="text" id="category-name" class="form-control" name="name" required>
              </div>

              <div class="form-actions">
                <button type="submit" class="btn btn-success">
                  <i class="fas fa-save"></i> Toevoegen
                </button>
              </div>
            </form>
          </div>

          <?php if (empty($allCategories)): ?>
            <div class="empty-message">
              <i class="fas fa-info-circle"></i>
              Er zijn nog geen categorieën. Voeg een nieuwe categorie toe met de knop hierboven.
            </div>
          <?php else: ?>
            <form method="post" action="actions/save/save_categories.php">
              <div class="card">
                <table class="table">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Categorie Naam</th>
                      <th>Acties</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($allCategories as $category): ?>
                      <tr>
                        <td><?= $category['id'] ?></td>
                        <td>
                          <input type="text" class="form-control" name="categories[<?= $category['id'] ?>][name]"
                            value="<?= htmlspecialchars($category['name']) ?>">
                        </td>
                        <td>
                          <a href="actions/delete/delete_category.php?id=<?= $category['id'] ?>" class="btn btn-danger"
                            onclick="return confirmDelete(<?= $category['id'] ?>, 'categorie')">
                            <i class="fas fa-trash"></i> Verwijderen
                          </a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>

              <div class="form-actions">
                <button type="submit" class="btn btn-success">
                  <i class="fas fa-save"></i> Wijzigingen Opslaan
                </button>
              </div>
            </form>
          <?php endif; ?>
        </div>
      </div>
    </main>
  </div>
</body>

</html>