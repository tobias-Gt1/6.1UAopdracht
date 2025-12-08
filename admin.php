<?php
session_start();
include 'connect.php';

// Alleen toegankelijk als ingelogd
if (empty($_SESSION['logged_in'])) {
	header('Location: login.php');
	exit;
}

// Upload map
$uploadDir = __DIR__ . '/uploads';
if (!is_dir($uploadDir)) {
	@mkdir($uploadDir, 0755, true);
}

function safe_filename($name) {
	return preg_replace('/[^A-Za-z0-9._-]/', '_', $name);
}

// PRG: verwerk POST en redirect
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$action = $_POST['action'] ?? '';

	if ($action === 'logout') {
		session_unset();
		session_destroy();
		header('Location: login.php');
		exit;
	}

	if ($action === 'create_panorama' || $action === 'edit_panorama') {
		$beschrijving = trim($_POST['beschrijving'] ?? '');
		$catalogus = (int)($_POST['catalogusnummer'] ?? 0);
		$id = (int)($_POST['gegevens_id'] ?? 0);
		$imageName = '';

		if (!empty($_FILES['image']['name'])) {
			$orig = basename($_FILES['image']['name']);
			$tmp = $_FILES['image']['tmp_name'];
			$imageName = time() . '_' . safe_filename($orig);
			$target = $uploadDir . '/' . $imageName;
			if (!@move_uploaded_file($tmp, $target)) {
				$_SESSION['flash'] = 'Upload van afbeelding is mislukt.';
				header('Location: admin.php');
				exit;
			}
		}

		if ($action === 'create_panorama') {
			$stmt = $conn->prepare("INSERT INTO `gegevens` (`image`,`beschrijving`,`catalogusnummer`) VALUES (?,?,?)");
			$stmt->bind_param('ssi', $imageName, $beschrijving, $catalogus);
			$ok = $stmt->execute();
			$stmt->close();
			$_SESSION['flash'] = $ok ? 'Panorama aangemaakt.' : 'Fout bij aanmaken panorama.';
		} else { // edit
			if ($imageName) {
				$stmt = $conn->prepare("UPDATE `gegevens` SET `image`=?, `beschrijving`=?, `catalogusnummer`=? WHERE `gegevens_id`=?");
				$stmt->bind_param('ssii', $imageName, $beschrijving, $catalogus, $id);
			} else {
				$stmt = $conn->prepare("UPDATE `gegevens` SET `beschrijving`=?, `catalogusnummer`=? WHERE `gegevens_id`=?");
				$stmt->bind_param('sii', $beschrijving, $catalogus, $id);
			}
			$ok = $stmt->execute();
			$stmt->close();
			$_SESSION['flash'] = $ok ? 'Panorama bijgewerkt.' : 'Fout bij bijwerken panorama.';
		}
		header('Location: admin.php');
		exit;
	}

	if ($action === 'delete_panorama') {
		$id = (int)($_POST['gegevens_id'] ?? 0);
		// verwijder eventueel bestand
		$stmt = $conn->prepare("SELECT `image` FROM `gegevens` WHERE `gegevens_id`=? LIMIT 1");
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$stmt->bind_result($img);
		$stmt->fetch();
		$stmt->close();
		if ($img) { @unlink($uploadDir . '/' . $img); }
		$stmt = $conn->prepare("DELETE FROM `gegevens` WHERE `gegevens_id`=?");
		$stmt->bind_param('i', $id);
		$ok = $stmt->execute();
		$stmt->close();
		$_SESSION['flash'] = $ok ? 'Panorama verwijderd.' : 'Fout bij verwijderen panorama.';
		header('Location: admin.php');
		exit;
	}
}

// Data voor weergave
$panoramas = [];
$res = $conn->query("SELECT `gegevens_id`,`image`,`beschrijving`,`catalogusnummer` FROM `gegevens` ORDER BY `gegevens_id` ASC");
if ($res) { while ($r = $res->fetch_assoc()) $panoramas[] = $r; $res->free(); }

$flash = $_SESSION['flash'] ?? '';
if ($flash) unset($_SESSION['flash']);
?>
<!doctype html>
<html lang="nl">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title>Admin · Panorama CRUD</title>
	<link rel="stylesheet" href="style.css">
	<!-- Geen inline styles; alles in style.css -->
	<meta name="robots" content="noindex,nofollow" />
	</head>
<body class="admin-page">
	<div class="admin-shell">
		<header class="admin-header">
			<div>
				<p class="eyebrow">Beheer</p>
				<h1>Panorama CRUD</h1>
				<p class="muted">Voeg panorama's toe, bewerk of verwijder ze.</p>
			</div>
			<form method="post" class="logout-form">
				<input type="hidden" name="action" value="logout">
				<button class="btn ghost" type="submit">Uitloggen</button>
			</form>
		</header>

		<?php if ($flash): ?>
			<div class="flash"><?= htmlspecialchars($flash) ?></div>
		<?php endif; ?>

		<div class="admin-grid">
			<section class="panel">
				<div class="panel-head">
					<div>
						<p class="eyebrow">Nieuw</p>
						<h2>Nieuw panorama</h2>
					</div>
				</div>
				<form method="post" enctype="multipart/form-data" class="form-grid">
					<input type="hidden" name="action" value="create_panorama">
					<label class="field">
						<span>Afbeelding</span>
						<input type="file" name="image" accept="image/*" required>
					</label>
					<label class="field">
						<span>Beschrijving</span>
						<textarea name="beschrijving" rows="2" placeholder="Korte beschrijving..."></textarea>
					</label>
					<label class="field">
						<span>Catalogusnummer</span>
						<input type="number" name="catalogusnummer" value="0" min="0">
					</label>
					<div class="form-actions">
						<button class="btn primary" type="submit">Aanmaken</button>
					</div>
				</form>
			</section>

			<section class="panel">
				<div class="panel-head">
					<div>
						<p class="eyebrow">Overzicht</p>
						<h2>Alle panorama's (<?= count($panoramas) ?>)</h2>
					</div>
				</div>
				<?php if (!count($panoramas)): ?>
					<p class="muted">Nog geen panorama's aangemaakt.</p>
				<?php else: ?>
				<div class="card-list">
					<?php foreach ($panoramas as $p): ?>
						<article class="card-row">
							<div class="thumb">
								<?php
								$pimg = $p['image'] ?? '';
								if ($pimg) {
									echo '<img src="image.php?id=' . (int)$p['gegevens_id'] . '" alt="">';
								} else {
									echo '<div class="thumb ph">Geen afbeelding</div>';
								}
								?>
							</div>
							<div class="card-body">
								<div class="card-top">
									<div>
										<h3>#<?= $p['gegevens_id'] ?> · Catalogus <?= htmlspecialchars($p['catalogusnummer']) ?></h3>
										<p class="muted small"><?= htmlspecialchars($p['beschrijving']) ?></p>
									</div>
									<div class="badge">Panorama</div>
								</div>
								<details class="inline-edit">
									<summary>Bewerk</summary>
									<form method="post" enctype="multipart/form-data" class="form-grid compact">
										<input type="hidden" name="action" value="edit_panorama">
										<input type="hidden" name="gegevens_id" value="<?= $p['gegevens_id'] ?>">
										<label class="field"><span>Afbeelding vervangen</span><input type="file" name="image" accept="image/*"></label>
										<label class="field"><span>Beschrijving</span><textarea name="beschrijving" rows="2"><?= htmlspecialchars($p['beschrijving']) ?></textarea></label>
										<label class="field"><span>Catalogusnummer</span><input type="number" name="catalogusnummer" value="<?= htmlspecialchars($p['catalogusnummer']) ?>" min="0"></label>
										<div class="form-actions"><button class="btn primary" type="submit">Opslaan</button></div>
									</form>
								</details>
								<form method="post" onsubmit="return confirm('Verwijder dit panorama?');">
									<input type="hidden" name="action" value="delete_panorama">
									<input type="hidden" name="gegevens_id" value="<?= $p['gegevens_id'] ?>">
									<button class="btn danger" type="submit">Verwijder</button>
								</form>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>
			</section>
		</div>
	</div>
</body>
</html>
