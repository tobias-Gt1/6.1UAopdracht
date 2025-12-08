<?php
?>
<!-- Hotspot include: registration API + programmatic lookup -->
<div id="hotspot-panel" aria-hidden="true" style="display:none;">
    <div class="hotspot-inner">
        <button id="hotspot-close" class="hotspot-close" aria-label="Close">×</button>
        <div class="hotspot-thumb-wrap"><img id="hotspot-thumb" src="" alt="" /></div>
        <div class="hotspot-meta">
            <div id="hotspot-title" class="hotspot-title"></div>
            <div id="hotspot-desc" class="hotspot-desc"></div>
        </div>
    </div>
</div>

<style>
    /* Local hotspot UI (small) */
    #hotspot-panel {
        position: fixed;
        left: 50%;
        transform: translateX(-50%);
        bottom: 18px;
        z-index: 1300;
        background: rgba(0, 0, 0, 0.78);
        color: #fff;
        border-radius: 8px;
        padding: 10px 12px;
        min-width: 260px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6);
    }

    #hotspot-panel .hotspot-inner {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    #hotspot-panel .hotspot-thumb-wrap {
        width: 64px;
        height: 64px;
        flex: 0 0 64px;
        border-radius: 6px;
        overflow: hidden;
        background: #111;
    }

    #hotspot-panel img#hotspot-thumb {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    #hotspot-panel .hotspot-meta {
        flex: 1 1 auto;
    }

    #hotspot-panel .hotspot-title {
        font-weight: 700;
        margin-bottom: 6px;
    }

    #hotspot-panel .hotspot-desc {
        font-size: 0.95rem;
        opacity: 0.95;
    }

    #hotspot-panel .hotspot-close {
        position: absolute;
        right: 8px;
        top: 6px;
        background: transparent;
        border: none;
        color: #fff;
        font-size: 18px;
        cursor: pointer;
    }

    #hotspot-panel[aria-hidden="true"] {
        display: none;
    }

    /* hotspot markers overlayed on zoom image */
    #hotspot-markers {
        position: fixed;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 1295;
    }

    .hotspot-marker {
        position: absolute;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: rgba(255, 80, 80, 0.92);
        border: 2px solid #fff;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.4);
        transform: translate(-50%, -50%);
        pointer-events: auto;
        cursor: pointer;
        transition: transform 120ms ease, box-shadow 120ms ease;
    }

    .hotspot-marker:hover {
        transform: translate(-50%, -50%) scale(1.12);
        box-shadow: 0 10px 26px rgba(0, 0, 0, 0.55);
    }

    .hotspot-marker.small {
        width: 12px;
        height: 12px;
    }
</style>
</style>

<script>
    (function() {
        // eenvoudige in-memory map: selector -> array met hotspots
        window._hotspotsMap = window._hotspotsMap || {};

        function showPanel(h) {
            var panel = document.getElementById('hotspot-panel');
            if (!panel) return;
            var thumb = document.getElementById('hotspot-thumb');
            var title = document.getElementById('hotspot-title');
            var desc = document.getElementById('hotspot-desc');
            if (h.thumb) {
                thumb.src = h.thumb;
                thumb.style.display = 'block';
            } else {
                thumb.src = '';
                thumb.style.display = 'none';
            }
            title.textContent = h.title || '';
            desc.textContent = h.text || '';
            panel.setAttribute('aria-hidden', 'false');
            panel.style.display = 'block';
        }

        function hidePanel() {
            var p = document.getElementById('hotspot-panel');
            if (!p) return;
            p.setAttribute('aria-hidden', 'true');
            p.style.display = 'none';
        }

        document.getElementById('hotspot-close').addEventListener('click', function(e) {
            e.stopPropagation();
            hidePanel();
        });
        document.addEventListener('click', function(e) {
            var p = document.getElementById('hotspot-panel');
            if (!p) return;
            if (p.getAttribute('aria-hidden') === 'true') return;
            if (e.target.closest && e.target.closest('#hotspot-panel')) return;
            hidePanel();
        });

        // registreerHotspots(selector, hotspotsArray)
        // hotspot: { x: <procent>, y: <procent>, url: <string>, target: '_self'|'_blank', title:'', text:'', thumb:'' }
        window.registerHotspots = function(selector, hotspots) {
            if (!selector) return 0;
            var arr = Array.isArray(hotspots) ? hotspots.map(function(h) {
                return {
                    x: parseFloat(h.x),
                    y: parseFloat(h.y),
                    url: h.url || '',
                    target: h.target || '_self',
                    title: h.title || '',
                    text: h.text || '',
                    thumb: h.thumb || ''
                };
            }) : [];
            window._hotspotsMap[selector] = arr;
            // als overlay actief is, (her)maak markers voor deze selector
            try {
                var overlay = document.getElementById('zoom-overlay');
                if (overlay && overlay.classList.contains('active')) createMarkersForSelector(selector);
            } catch (e) {}
            return arr.length;
        };

        // openHotspotAt(selector, xPercent, yPercent) -> retourneert true als hotspot gevonden/afgehandeld
        window.openHotspotAt = function(selector, xPercent, yPercent, clientX, clientY) {
            var list = (window._hotspotsMap && window._hotspotsMap[selector]) || [];
            if (!list.length) return false;
            var best = null,
                bestDist = Infinity;
            list.forEach(function(h) {
                var dx = h.x - xPercent;
                var dy = h.y - yPercent;
                var d = Math.hypot(dx, dy);
                if (d < bestDist) {
                    bestDist = d;
                    best = h;
                }
            });
            // drempel in procent-eenheden (instelbaar)
            var threshold = 6; // 6% van afbeeldingsafmetingen
            if (best && bestDist <= threshold) {
                // als hotspot een url heeft, behandel
                if (best.url) {
                    // toon info-box (voorkeur) indien beschikbaar, positioneer bij meegegeven client-coördinaten
                    try {
                        if (window.showInfoBox) {
                            var cx = typeof clientX !== 'undefined' ? clientX : (window.innerWidth / 2);
                            var cy = typeof clientY !== 'undefined' ? clientY : (window.innerHeight / 2);
                            window.showInfoBox(best, cx, cy);
                            return true;
                        }
                    } catch (e) {}
                    // fallback: toon paneel als info-box niet aanwezig is
                    showPanel(best);
                    return true;
                }
                // anders toon een klein paneel
                showPanel(best);
                return true;
            }
            return false;
        };

        // --- Beheer zichtbare markers ---
        var _markerState = {
            selector: null,
            nodes: [],
            raf: null
        };

        function clearMarkers() {
            try {
                _markerState.nodes.forEach(function(n) {
                    n.remove();
                });
                _markerState.nodes = [];
                var cont = document.getElementById('hotspot-markers');
                if (cont) cont.remove();
                if (_markerState.raf) {
                    cancelAnimationFrame(_markerState.raf);
                    _markerState.raf = null;
                }
            } catch (e) {}
        }

        function createMarkersForSelector(selector) {
            clearMarkers();
            var list = (window._hotspotsMap && window._hotspotsMap[selector]) || [];
            if (!list.length) return;
            var cont = document.createElement('div');
            cont.id = 'hotspot-markers';
            document.body.appendChild(cont);
            list.forEach(function(h, idx) {
                var el = document.createElement('button');
                el.className = 'hotspot-marker';
                el.title = h.title || '';
                el.dataset._hsIndex = idx;
                el.dataset._hsSelector = selector;
                el.addEventListener('click', function(ev) {
                    ev.stopPropagation();
                    ev.preventDefault();
                    var sel = this.dataset._hsSelector;
                    var i = parseInt(this.dataset._hsIndex, 10);
                    var H = (window._hotspotsMap && window._hotspotsMap[sel]) || [];
                    var hh = H[i];
                    if (!hh) return;
                    if (hh.url) {
                        if (window.showInfoBox) {
                            window.showInfoBox(hh, ev.clientX, ev.clientY);
                        } else {
                            showPanel(hh);
                        }
                    } else showPanel(hh);
                });
                cont.appendChild(el);
                _markerState.nodes.push(el);
            });
            _markerState.selector = selector;
            // start update-loop
            function update() {
                positionMarkers(selector);
                _markerState.raf = requestAnimationFrame(update);
            }
            _markerState.raf = requestAnimationFrame(update);
        }

        function positionMarkers(selector) {
            var list = (window._hotspotsMap && window._hotspotsMap[selector]) || [];
            var rect = null;
            var img = document.querySelector(selector);
            if (!img) return;
            rect = img.getBoundingClientRect();
            var cont = document.getElementById('hotspot-markers');
            if (!cont) return;
            _markerState.nodes.forEach(function(node, i) {
                var h = list[i];
                if (!h) return;
                // bereken pixelpositie relatief aan viewport met behulp van img rect
                var px = rect.left + (rect.width * (h.x / 100));
                var py = rect.top + (rect.height * (h.y / 100));
                node.style.left = px + 'px';
                node.style.top = py + 'px';
            });
        }

        // observeer overlay en maak/verwijder markers overeenkomstig
        (function watchOverlay() {
            var prev = false;

            function tick() {
                var overlay = document.getElementById('zoom-overlay');
                var active = overlay && overlay.classList.contains('active');
                if (active && !prev) {
                    // overlay zojuist geopend: maak markers voor #zoom-img
                    if (window._hotspotsMap && window._hotspotsMap['#zoom-img']) createMarkersForSelector('#zoom-img');
                } else if (!active && prev) {
                    // overlay gesloten: verwijder markers
                    clearMarkers();
                }
                prev = active;
                requestAnimationFrame(tick);
            }
            requestAnimationFrame(tick);
        })();

    })();
</script>