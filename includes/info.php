<?php
?>
<!-- Info box for hotspots: shows a small white card near a marker -->
<div id="info-box" aria-hidden="true">
    <button id="info-box-close" aria-label="Close">×</button>
    <div id="info-box-inner">
        <div id="info-box-thumb"><img id="info-box-img" src="" alt="" /></div>
        <div id="info-box-copy">
            <div id="info-box-title"></div>
            <div id="info-box-text"></div>
        </div>
    </div>
</div>
<script>
    (function() {
        var box = document.getElementById('info-box');
        var img = document.getElementById('info-box-img');
        var title = document.getElementById('info-box-title');
        var text = document.getElementById('info-box-text');
        var close = document.getElementById('info-box-close');

        function clamp(v, a, b) {
            return Math.max(a, Math.min(b, v));
        }

        window.showInfoBox = function(hotspot, clientX, clientY){
            if(!box) return;
            // vullen
            img.src = hotspot.thumb || hotspot.url || '';
            img.style.display = img.src ? 'block' : 'none';
            title.textContent = hotspot.title || '';
            text.textContent = hotspot.text || '';
            // bereken positie nabij clientX/clientY (voorkeur: rechts)
            var pad = 12;
            var w = Math.max(220, Math.min(320, box.offsetWidth || 260));
            // als breedte niet beschikbaar (verborgen), toon dan en meet
            box.style.left = '-9999px';
            box.style.top = '-9999px';
            box.setAttribute('aria-hidden', 'false');
            box.style.display = 'block';
            var bw = box.offsetWidth;
            var bh = box.offsetHeight;
            var viewportW = window.innerWidth;
            var viewportH = window.innerHeight;
            var left = clientX + 12;
            var top = clientY - (bh / 2);
            // als overflow naar rechts, plaats links van het punt
            if (left + bw + pad > viewportW) left = clientX - bw - 12;
            // beperk verticaal
            top = clamp(top, pad, viewportH - bh - pad);
            // als nog buiten beeld links, beperk
            left = clamp(left, pad, viewportW - bw - pad);
            box.style.left = left + 'px';
            box.style.top = top + 'px';
            // pijl-klassen
            box.classList.remove('info-box-arrow-left', 'info-box-arrow-right', 'info-box-arrow-top', 'info-box-arrow-bottom');
            if (clientX + bw + 24 > viewportW) box.classList.add('info-box-arrow-right');
            else box.classList.add('info-box-arrow-left');
        };

        window.hideInfoBox = function() {
            if (!box) return;
            box.setAttribute('aria-hidden', 'true');
            box.style.display = 'none';
        };

        close.addEventListener('click', function(e) {
            e.stopPropagation();
            window.hideInfoBox();
        });

        // verberg bij klikken buiten
        document.addEventListener('click', function(e) {
            if (!box) return;
            if (box.getAttribute('aria-hidden') === 'true') return;
            if (e.target.closest && e.target.closest('#info-box')) return;
            window.hideInfoBox();
        });
        // herpositioneer bij wijziging van grootte
        window.addEventListener('resize', function() {
            if (box && box.getAttribute('aria-hidden') === 'false') window.hideInfoBox();
        });

    })();
</script>

<style>
    /* Minimal white info box positioned near hotspot marker */
    #info-box {
        position: fixed;
        z-index: 1310;
        min-width: 220px;
        max-width: 320px;
        background: #fff;
        color: #111;
        border-radius: 8px;
        box-shadow: 0 8px 30px rgba(2, 6, 23, 0.18);
        padding: 10px;
        display: none;
        align-items: stretch;
        pointer-events: auto;
    }

    #info-box[aria-hidden="false"] {
        display: block;
    }

    #info-box #info-box-inner {
        display: flex;
        gap: 10px;
        align-items: flex-start;
    }

    #info-box-thumb {
        width: 72px;
        height: 72px;
        flex: 0 0 72px;
        overflow: hidden;
        border-radius: 6px;
        background: #eee;
    }

    #info-box-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    #info-box-copy {
        flex: 1 1 auto;
        font-size: 0.95rem;
    }

    #info-box-title {
        font-weight: 700;
        margin-bottom: 6px;
    }

    #info-box-text {
        color: #333;
        font-size: 0.9rem;
        line-height: 1.2;
    }

    #info-box-close {
        position: absolute;
        right: 8px;
        top: 6px;
        border: none;
        background: transparent;
        font-size: 18px;
        cursor: pointer;
        color: #222;
    }

    /* small arrow to indicate attachment (optional): uses pseudo-element on the box when positioned)
   We'll toggle classes via JS if we want an arrow on left/right/top/bottom */
    .info-box-arrow-left::after,
    .info-box-arrow-right::after,
    .info-box-arrow-top::after,
    .info-box-arrow-bottom::after {
        content: '';
        position: absolute;
        width: 12px;
        height: 12px;
        background: #fff;
        transform: rotate(45deg);
        box-shadow: 0 4px 12px rgba(2, 6, 23, 0.08);
    }

    .info-box-arrow-left::after {
        left: -6px;
        top: 14px;
    }

    .info-box-arrow-right::after {
        right: -6px;
        top: 14px;
    }

    .info-box-arrow-top::after {
        top: -6px;
        left: 18px;
    }

    .info-box-arrow-bottom::after {
        bottom: -6px;
        left: 18px;
    }

    @media (max-width:520px) {
        #info-box {
            min-width: 180px;
            max-width: calc(100% - 20px);
        }

        #info-box-thumb {
            width: 56px;
            height: 56px;
        }
    }
</style>