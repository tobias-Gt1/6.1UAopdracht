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
                    <div class="hero-card">
                        <div class="hero-slideshow" aria-hidden="true">
                            <div class="slide" style="background-image:url('assets/Beeld02.png')"></div>
                            <div class="slide" style="background-image:url('assets/Beeld03.png')"></div>
                            <div class="slide" style="background-image:url('assets/Beeld04.png')"></div>
                            <div class="slide" style="background-image:url('assets/Beeld05.png')"></div>
                        </div>
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
                    <div class="side-slideshow" aria-hidden="true">
                        <img src="assets/Beeld06.png" alt="Afbeelding 1" class="side-slide" loading="lazy">
                        <img src="assets/Beeld07.png" alt="Afbeelding 2" class="side-slide" loading="lazy">
                        <img src="assets/Beeld08.png" alt="Afbeelding 3" class="side-slide" loading="lazy">
                        <img src="assets/Beeld09.png" alt="Afbeelding 4" class="side-slide" loading="lazy">
                    </div>

                    <div class="news-card">
                        <div class="news-card__kicker">Nieuws</div>
                        <h3 class="news-card__title">Bijzondere aanwinst: Nieuw licht op de Pauluspoort</h3>
                    </div>
                </aside>
            </div>
        </section>

        <section class="section section--light">
            <div class="container">
                <div class="feature-panel">
                    <div class="feature-text">
                        <h2>Expo en tentoonstellingen</h2>
                        <p>Utrecht begint hier — ontdek onze collecties en verhalen. Bekijk onze panoramas en verken historische stadgezichten, toegelicht met data en beschrijvingen.</p>
                    </div>
                    <div class="feature-actions">
                        <a href="index.php" class="laperello-btn">Naar panoramas</a>
                    </div>
                </div>
            </div>
        </section>
    </main>


    <script>
    // slideshow hero section
    document.addEventListener('DOMContentLoaded', function(){
        function startSlideshow(containerSel, slideSel, interval){
            var container = document.querySelector(containerSel);
            if(!container) return;
            var slides = Array.from(container.querySelectorAll(slideSel));
            if(!slides.length) return;
            var current = 0;
            slides.forEach(function(s,i){ if(i===0) s.classList.add('active'); else s.classList.remove('active'); });
            setInterval(function(){
                slides[current].classList.remove('active');
                current = (current + 1) % slides.length;
                slides[current].classList.add('active');
            }, interval);
        }
        startSlideshow('.hero-slideshow', '.slide', 4500);
        startSlideshow('.side-slideshow', '.side-slide', 3500);
    });
    </script>

    <?php include 'includes/footer.php'; ?>
</body>
</html>