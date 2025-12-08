<?php
?>

<div id="zoom-overlay" aria-hidden="true">
  <button id="zoom-close" class="zoom-close" aria-label="Close">×</button>
  <button id="zoom-prev" class="zoom-nav zoom-prev" aria-label="Vorige">‹</button>
  <button id="zoom-next" class="zoom-nav zoom-next" aria-label="Volgende">›</button>
  <img id="zoom-img" src="" alt="" />
</div>
<div class="zoom-controls" role="toolbar" aria-label="Zoom controls">
  <button id="zoom-minus" aria-label="Zoom out">−</button>
  <input id="zoom-range" type="range" min="1" max="3" step="0.05" value="1" aria-label="Zoom level">
  <button id="zoom-plus" aria-label="Zoom in">+</button>
  <button id="zoom-reset" aria-label="Reset zoom">Reset</button>
</div>
</div>

<script>
  (function() {
    var overlay = document.getElementById('zoom-overlay');
    var inner = overlay && overlay.querySelector('.zoom-inner');
    var imgEl = document.getElementById('zoom-img');
    var closeBtn = document.getElementById('zoom-close');
    var prevBtn = document.getElementById('zoom-prev');
    var nextBtn = document.getElementById('zoom-next');
    var minusBtn = document.getElementById('zoom-minus');
    var plusBtn = document.getElementById('zoom-plus');
    var resetBtn = document.getElementById('zoom-reset');
    var range = document.getElementById('zoom-range');

    var images = [];

    function refreshImages() {
      images = Array.from(document.querySelectorAll('.panorama img'));
    }
    refreshImages();

    var currentIndex = -1;
    var zoom = 1;
    var minZoom = parseFloat(range.getAttribute('min')) || 1;
    var maxZoom = parseFloat(range.getAttribute('max')) || 3;
    var step = parseFloat(range.getAttribute('step')) || 0.05;
    var translate = {
      x: 0,
      y: 0
    };

    function clamp(v) {
      return Math.max(minZoom, Math.min(maxZoom, v));
    }

    function applyTransform() {
      imgEl.style.transform = 'translate(' + translate.x + 'px, ' + translate.y + 'px) scale(' + zoom + ')';
    }

    function openByIndex(i) {
      refreshImages();
      if (i < 0 || i >= images.length) return;
      currentIndex = i;
      imgEl.src = images[i].src;
      imgEl.alt = images[i].alt || '';
      zoom = 1;
      translate.x = 0;
      translate.y = 0;
      range.value = zoom;
      overlay.classList.add('active');
      overlay.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
      applyTransform();
    }

    function openBySrc(src) {
      refreshImages();
      var i = images.findIndex(function(im) {
        return im.src === src || im.getAttribute('src') === src;
      });
      if (i === -1) {
        // fallback: toon src als externe afbeelding
        currentIndex = -1;
        imgEl.src = src;
        imgEl.alt = '';
        zoom = 1;
        translate.x = 0;
        translate.y = 0;
        range.value = zoom;
        overlay.classList.add('active');
        overlay.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        applyTransform();
      } else {
        openByIndex(i);
      }
    }

    window.openZoomist = function(src, alt) {
      if (!overlay) return;
      if (typeof src === 'number') return openByIndex(src);
      openBySrc(src);
    };

    function closeOverlay() {
      if (!overlay) return;
      overlay.classList.remove('active');
      overlay.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
      imgEl.src = '';
      currentIndex = -1;
      zoom = 1;
      translate.x = 0;
      translate.y = 0;
      applyTransform();
    }

    function showNext() {
      if (images.length && currentIndex >= 0) {
        openByIndex((currentIndex + 1) % images.length);
      }
    }

    function showPrev() {
      if (images.length && currentIndex >= 0) {
        openByIndex((currentIndex - 1 + images.length) % images.length);
      }
    }

    function setZoom(v) {
      zoom = clamp(v);
      range.value = zoom;
      if (zoom <= 1) {
        translate.x = 0;
        translate.y = 0;
      }
      applyTransform();
    }

    // bediening
    if (closeBtn) closeBtn.addEventListener('click', closeOverlay);
    if (prevBtn) prevBtn.addEventListener('click', showPrev);
    if (nextBtn) nextBtn.addEventListener('click', showNext);
    if (minusBtn) minusBtn.addEventListener('click', function() {
      setZoom(zoom - step);
    });
    if (plusBtn) plusBtn.addEventListener('click', function() {
      setZoom(zoom + step);
    });
    if (resetBtn) resetBtn.addEventListener('click', function() {
      setZoom(1);
    });
    if (range) range.addEventListener('input', function(e) {
      setZoom(parseFloat(e.target.value));
    });

    // toetsenbord
    document.addEventListener('keydown', function(e) {
      if (!overlay.classList.contains('active')) return;
      if (e.key === 'Escape') {
        e.preventDefault();
        closeOverlay();
      } else if (e.key === 'ArrowRight') {
        e.preventDefault();
        showNext();
      } else if (e.key === 'ArrowLeft') {
        e.preventDefault();
        showPrev();
      } else if (e.key === '+' || e.key === '=') {
        e.preventDefault();
        setZoom(zoom + step);
      } else if (e.key === '-') {
        e.preventDefault();
        setZoom(zoom - step);
      }
    });

    // pannen en knijpen-naar-zoom (pinch-to-zoom)
    var isPanning = false,
      start = {
        x: 0,
        y: 0
      },
      startTranslate = {
        x: 0,
        y: 0
      };
    var pinchStartDist = 0,
      pinchStartZoom = 1;
    var moved = false;

    imgEl.addEventListener('mousedown', function(e) {
      if (zoom <= 1) return;
      isPanning = true;
      start.x = e.clientX;
      start.y = e.clientY;
      startTranslate.x = translate.x;
      startTranslate.y = translate.y;
      imgEl.style.cursor = 'grabbing';
      e.preventDefault();
    });
    document.addEventListener('mousemove', function(e) {
      if (!isPanning) return;
      var dx = e.clientX - start.x;
      var dy = e.clientY - start.y;
      translate.x = startTranslate.x + dx;
      translate.y = startTranslate.y + dy;
      moved = true;
      applyTransform();
    });
    document.addEventListener('mouseup', function() {
      if (isPanning) {
        isPanning = false;
        imgEl.style.cursor = 'grab';
      }
      setTimeout(function() {
        moved = false;
      }, 50);
    });

    // touch / aanraking
    imgEl.addEventListener('touchstart', function(e) {
      if (e.touches.length === 1) {
        if (zoom <= 1) return;
        start.x = e.touches[0].clientX;
        start.y = e.touches[0].clientY;
        startTranslate.x = translate.x;
        startTranslate.y = translate.y;
        isPanning = true;
      } else if (e.touches.length === 2) { // knijp-zoom (pinch)
        pinchStartDist = Math.hypot(e.touches[0].clientX - e.touches[1].clientX, e.touches[0].clientY - e.touches[1].clientY);
        pinchStartZoom = zoom;
        isPanning = false;
      }
    }, {
      passive: false
    });

    imgEl.addEventListener('touchmove', function(e) {
      if (e.touches.length === 1 && isPanning) {
        var dx = e.touches[0].clientX - start.x;
        var dy = e.touches[0].clientY - start.y;
        translate.x = startTranslate.x + dx;
        translate.y = startTranslate.y + dy;
        applyTransform();
        e.preventDefault();
      } else if (e.touches.length === 2) {
        var d = Math.hypot(e.touches[0].clientX - e.touches[1].clientX, e.touches[0].clientY - e.touches[1].clientY);
        if (pinchStartDist > 0) {
          var ratio = d / pinchStartDist;
          setZoom(pinchStartZoom * ratio);
        }
        e.preventDefault();
      }
    }, {
      passive: false
    });

    imgEl.addEventListener('touchend', function(e) {
      if (e.touches.length === 0) {
        isPanning = false;
        pinchStartDist = 0;
      }
    });

    // klikken op afbeelding probeert eerst hotspots, en schakelt daarna snel tussen zoomstanden (wanneer geopend)
    imgEl.addEventListener('click', function(e) {
      e.stopPropagation();
      if (moved) {
        moved = false;
        return;
      } // niet togglen na slepen/pannen
      try {
        var rect = imgEl.getBoundingClientRect();
        var px = (e.clientX - rect.left) / rect.width * 100;
        var py = (e.clientY - rect.top) / rect.height * 100;
        if (window.openHotspotAt && window.openHotspotAt('#zoom-img', px, py, e.clientX, e.clientY)) {
          return; // hotspot geopend - houd huidige zoom/pan
        }
      } catch (err) {
        /* ignore */
      }
      if (zoom <= 1) setZoom(2);
      else setZoom(1);
    });

    // Sta toe dat klikken op elke .panorama img de overlay opent
    function attachOpeners() {
      refreshImages();
      images.forEach(function(item, idx) {
        item.style.cursor = 'zoom-in';
        item.removeEventListener('click', item._zoomHandler);
        var handler = function() {
          window.openZoomist(idx);
        };
        item.addEventListener('click', handler);
        item._zoomHandler = handler;
      });
    }
    // koppel bij DOM-ready en wanneer aangeroepen
    attachOpeners();

  })();
</script>