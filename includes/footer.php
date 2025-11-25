<?php
// Footer include: alleen de footer-markup (verwacht dat de host-pagina `style.css` laadt)
?>

<footer class="site-footer" role="contentinfo">
    <div class="site-footer__inner container">
        <div class="site-footer__links">
            <a href="#">Privacy</a>
            <a href="#">Voorwaarden</a>
            <a href="#">Contact</a>
        </div>
        <div class="site-footer__copy">© 2025 Het Utrechts Archief</div>
    </div>
</footer>

<script>
// Dropdown toggle for mobile: klik op de parent link opent/sluit submenu
(function(){
    document.addEventListener('click', function(e){
        var dropdowns = document.querySelectorAll('.has-dropdown.open');
        if(!e.target.closest('.has-dropdown')){
            dropdowns.forEach(function(d){ d.classList.remove('open'); });
        }
    });

    document.querySelectorAll('.has-dropdown > a').forEach(function(link){
        link.addEventListener('click', function(e){
            if(window.innerWidth <= 900){
                e.preventDefault();
                this.parentElement.classList.toggle('open');
            }
        });
    });
})();
</script>
