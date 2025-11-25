<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title>Document</title>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="container page-main">
        <section class="home-hero">
            <div class="hero-grid">
                <div class="hero-left">
                    <div class="hero-card" style="background-image:url('assets/Schermafbeelding%20header.png')">
                        <div class="hero-card__bar">Ontdek het verleden van Utrecht</div>

                        <div class="hero-card__search">
                            <div class="search-label">Zoeken door onze bronnen</div>
                            <form class="search-form" action="#" method="get">
                                <input type="text" name="q" placeholder="Zoekterm, plaats of persoon" aria-label="Zoeken" />
                                <button class="search-btn">Zoeken ›</button>
                            </form>
                        </div>
                    </div>
                </div>

                <aside class="hero-right">
                    <img src="assets/Schermafbeelding122.png" alt="Afbeelding" class="side-image">

                    <div class="news-card">
                        <div class="news-card__kicker">Nieuws</div>
                        <h3 class="news-card__title">Bijzondere aanwinst: Nieuw licht op de Pauluspoort</h3>
                    </div>
                </aside>
            </div>
        </section>

        <!-- Extra content voorbeeld -->
        <section class="section section--light">
            <div class="container">
                <h2>Expo en tentoonstellingen</h2>
                <p>Utrecht begint hier — ontdek onze collecties en verhalen.</p>
            </div>
        </section>
    </main>

    <section class="laperello-cta">
        <div class="container">
            <a href="index.php" class="laperello-btn" id="laperello-trigger">Open Laperello</a>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>
</html>