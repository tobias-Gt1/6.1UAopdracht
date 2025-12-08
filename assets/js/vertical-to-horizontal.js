// Zet verticale wheel-scroll om naar horizontale scroll voor de panorama
(function () {
  var panorama = null;
  function init() {
    // geef voorkeur aan outer wrapper indien aanwezig, anders fallback naar .panorama
    panorama =
      document.querySelector(".panorama-wrap") ||
      document.querySelector(".panorama");
    if (!panorama) return;

    // Als panorama-wrap bestaat is de daadwerkelijke scroll-container de inner .panorama
    if (panorama.classList && panorama.classList.contains("panorama-wrap")) {
      var inner = panorama.querySelector(".panorama");
      if (inner) panorama = inner;
    }

    // wheel-handler: zet verticale scroll om naar horizontale
    window.addEventListener(
      "wheel",
      function (e) {
        // negeer pinch-zoom modifiers of wanneer overlay actief is
        if (e.ctrlKey) return;
        var tag = (e.target && e.target.tagName) || "";
        if (/INPUT|TEXTAREA|SELECT|OPTION/.test(tag)) return;
        var overlay =
          document.getElementById && document.getElementById("zoom-overlay");
        if (
          overlay &&
          overlay.classList.contains &&
          overlay.classList.contains("active")
        )
          return;

        // alleen handelen wanneer verticale beweging dominant is
        if (Math.abs(e.deltaY) <= Math.abs(e.deltaX)) return;

        // voorkom de standaard verticale paginascroll en vertaal naar horizontale
        e.preventDefault();
        // gebruik een factor om de horizontale beweging natuurlijker te laten aanvoelen
        var multiplier = 1.25;
        panorama.scrollLeft += e.deltaY * multiplier;
      },
      { passive: false }
    );
  }

  if (document.readyState === "loading")
    document.addEventListener("DOMContentLoaded", init);
  else init();
})();
