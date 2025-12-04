<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="landingpage" href="landing.php">
    <title>laperello</title>
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <main class="container">
        <section class="panorama-gallery">
            <div class="gallery-header" style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:12px;">
                <h1 class="panorama-title" style="margin:0">Panoramas</h1>
                <div>
                    <a href="landing.php" class="laperello-btn" style="padding:8px 12px;font-size:0.95rem;">Terug</a>
                </div>
            </div>
            <div class="panorama-info">
                <p>
                    Hier vind je een verzameling panoramische beelden uit onze collectie. Onder de titel zie je basisinformatie over de set: wat het is, wanneer de foto's zijn gemaakt en eventuele aanvullende opmerkingen.
                </p>
                <ul class="panorama-meta">
                    <li><strong>Wat:</strong> Stadsgezichten en archiefbeelden</li>
                    <li><strong>Wanneer:</strong> Diverse jaren - zie per panorama</li>
                    <li><strong>Opmerkingen:</strong> Klik op een panorama om de volledige weergave te openen</li>
                </ul>
            </div>
            <h2>Panorama collectie</h2>
            <div class="panorama-grid">
                <a href="panorama.php" class="panorama-card" title="Open panorama 1">
                    <div class="panorama-card__thumb">
                        <img src="assets/Beeld02.png" alt="Panorama 1 thumbnail" loading="lazy">
                    </div>
                    <div class="panorama-card__meta">
                        <h3>Utrecht 1859</h3>
                        <p> Panorama van Utrecht, op de lithostenen getekend door J. Bos</p>
                    </div>
                </a>

                <!-- Placeholder panoramas -->
                <a href="#" class="panorama-card panorama-card--placeholder" aria-disabled="true">
                    <div class="panorama-card__thumb" style="background-image:url('assets/Beeld10.png')"></div>
                    <div class="panorama-card__meta">
                        <h3>Panorama 2</h3>
                        <p>Placeholder</p>
                    </div>
                </a>

                <a href="#" class="panorama-card panorama-card--placeholder" aria-disabled="true">
                    <div class="panorama-card__thumb" style="background-image:url('assets/Beeld11.png')"></div>
                    <div class="panorama-card__meta">
                        <h3>Panorama 3</h3>
                        <p>Placeholder</p>
                    </div>
                </a>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>

</body>

</html>