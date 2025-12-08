<?php
// Laad afbeeldingen en labels uit de database en toon ze in de panorama-strip
require_once __DIR__ . '/connect.php';

// Haal records op uit tabel `gegevens`
$sql = "SELECT gegevens_id, image, beschrijving, catalogusnummer FROM gegevens ORDER BY gegevens_id ASC";
$result = $conn->query($sql);
$rows = [];
if ($result) {
		while ($row = $result->fetch_assoc()) {
				$rows[] = $row;
		}
}
// optioneel: aantal voor id's
$count = count($rows);
?>
<!DOCTYPE html>
<html lang="nl">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="stylesheet" href="style.css?v=2" />
	<title>Panorama (database)</title>
	<style>
		/* Kleine veiligheidsmarge voor het geval lange beschrijvingen in de dropdown komen */
		.streets-dropdown { max-height: 60vh; overflow: auto; }
	</style>
	<?php /* eenvoudige no-cache om zeker te zijn dat nieuwe JS/CSS binnenkomt */ ?>
</head>

<body class="panorama-page">

	<header class="panorama-only-header" role="banner">
		<div class="container" style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
			<a href="index.php" class="panorama-back" aria-label="Terug">← Terug</a>

			<nav class="panorama-streets" aria-label="Straatnamen">
				<div class="streets-dropdown-wrap">
					<button class="streets-toggle" aria-haspopup="true" aria-expanded="false">Streetnames ▾</button>
					<ul class="streets-dropdown" hidden>
						<?php if ($count === 0): ?>
							<li><span style="padding:8px 12px;display:block;color:#666;">Geen records gevonden</span></li>
						<?php else: ?>
							<?php
							// maak compacte labels op basis van beschrijving of catalogusnummer
							$i = 1;
							foreach ($rows as $r):
									$beschrijving = trim((string)($r['beschrijving'] ?? ''));
									$cat = trim((string)($r['catalogusnummer'] ?? ''));
									$label = $beschrijving !== '' ? $beschrijving : ($cat !== '' ? ('Catalogus ' . $cat) : ('Afbeelding ' . $i));
							?>
								<li>
									<a href="#img-<?= $i ?>" data-target="#img-<?= $i ?>"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></a>
								</li>
							<?php $i++; endforeach; ?>
						<?php endif; ?>
					</ul>
				</div>
			</nav>

			<div class="panorama-brand" aria-hidden="true">
				<div class="brand-logo__box">
					<img src="assets/UA.png" alt="UA logo" class="brand-logo__img">
				</div>
			</div>
		</div>
	</header>

	<section class="panorama-wrap">
		<div class="panorama">
			<?php if ($count === 0): ?>
				<!-- Geen resultaten: toon een subtiele melding -->
				<div style="padding:16px;color:#fff;">Er zijn geen afbeeldingen gevonden in de database.</div>
			<?php else: ?>
				<?php
				// Render alle afbeeldingen uit DB
				$i = 1;
				foreach ($rows as $r):
						$src = 'assets/' . (string)($r['image'] ?? '');
						$beschrijving = trim((string)($r['beschrijving'] ?? ''));
						$cat = trim((string)($r['catalogusnummer'] ?? ''));
						$alt = $beschrijving !== '' ? $beschrijving : ($cat !== '' ? ('Catalogus ' . $cat) : ('Afbeelding ' . $i));
				?>
					<img id="img-<?= $i ?>" loading="lazy" src="<?= htmlspecialchars($src, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($alt, ENT_QUOTES, 'UTF-8') ?>" />
				<?php $i++; endforeach; ?>
			<?php endif; ?>
		</div>
	</section>

	<?php // voeg de zoom-overlay en hotspots toe (custom includes) ?>
	<?php include 'includes/zoom.php'; ?>
	<?php include 'includes/hotspot.php'; ?>
	<?php include 'includes/info.php'; ?>
	<script src="assets/js/vertical-to-horizontal.js"></script>

	<script>
		// Koppel klikgedrag voor de straatnamenlijst
		document.addEventListener('DOMContentLoaded', function () {
			var panorama = document.querySelector('.panorama');
			if (!panorama) return;
			var links = document.querySelectorAll('.panorama-streets a');

			links.forEach(function (a) {
				a.addEventListener('click', function (e) {
					e.preventDefault();
					var sel = a.dataset.target || a.getAttribute('href');
					var target = document.querySelector(sel);
					if (!target) return;
					panorama.scrollTo({ left: target.offsetLeft, behavior: 'smooth' });
					links.forEach(function (x) { x.classList.remove('active'); });
					a.classList.add('active');
					// sluit dropdown na keuze
					var dd = document.querySelector('.streets-dropdown');
					var toggle = document.querySelector('.streets-toggle');
					if (dd && toggle) { dd.hidden = true; toggle.setAttribute('aria-expanded', 'false'); }
				});
			});

			// Dropdown open/dicht
			var toggle = document.querySelector('.streets-toggle');
			var dropdown = document.querySelector('.streets-dropdown');
			if (toggle && dropdown) {
				toggle.addEventListener('click', function () {
					var open = !(dropdown.hidden);
					dropdown.hidden = open; // wissel
					toggle.setAttribute('aria-expanded', String(!open));
				});
				document.addEventListener('click', function (e) {
					if (!dropdown.hidden && !e.target.closest('.streets-dropdown-wrap')) {
						dropdown.hidden = true;
						toggle.setAttribute('aria-expanded', 'false');
					}
				});
			}
		});
	</script>

	<?php include 'includes/footer.php'; ?>
</body>

</html>
<?php
// Sluit de connectie netjes
if (isset($conn) && $conn instanceof mysqli) {
		$conn->close();
}
?>
