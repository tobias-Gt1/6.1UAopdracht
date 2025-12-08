<?php
// Header include: koptekst nabootsing van `Schermafbeelding header.png`
// Verwacht dat `style.css` in de root geladen wordt door de pagina die dit include't.
?>

<header class="site-top" role="banner">
    <div class="site-top__bar">
        <div class="container top__inner">
            <div class="search">
                <button class="search__btn" aria-label="Zoeken">
                    <!-- eenvoudige SVG loep -->
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <circle cx="11" cy="11" r="6" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" opacity="0.95"/>
                        <path d="M21 21l-4.35-4.35" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" opacity="0.95"/>
                    </svg>
                </button>
            </div>

                        <nav class="top-nav" aria-label="Hoofdnavigatie">
                            <ul class="top-nav__list">
                                <li><a href="#onderzoek">Onderzoek</a></li>
                                <li><a href="#ontdekken">Ontdekken</a></li>
                                <li><a href="#onderwijs">Onderwijs</a></li>
                                <li><a href="#vakgenoten">Vakgenoten</a></li>
                                <li><a href="#over-ons">Over ons</a></li>
                                <li><a href="#contact">Contact</a></li>
                                <li><a href="/en/" class="top-nav__lang">English</a></li>
                            </ul>
                        </nav>

            <div class="brand-logo">
                <div class="brand-logo__box" aria-hidden="true">
                    <img src="assets/UA.png" alt="UA logo" class="brand-logo__img">
                </div>
            </div>
        </div>
    </div>

    <!-- subtiele strook onder de nav zoals in de screenshot -->
    <div class="site-top__subbar" aria-hidden="true"></div>
</header>
