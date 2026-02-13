<footer class="footer__container">
    <p id="footer__id">ItineraEase Group:</p>
    <div class="footer__links">
        <div class="footer__link--wrapper">
            <div class="footer__link--items">
                <p><strong>Plan</strong></p>
                <a href="index.php">Map</a>
                <a href="search.php">Search</a>
            </div>
            <div class="footer__link--items">
                <p><strong>Support</strong></p>
                <a href="faq.php">FAQ</a>
                <a href="contact.php">Contact</a>
                <a href="mailto: support@itineraease.com"> support@itineraease.com</a>
            </div>
        </div>
    </div>
    <hr style="border: .1px solid lightgrey;">
    <div class="social__media">
        <div class="social__media--wrap">
            <p class="website__rights"> © <?php echo date('Y'); ?> ItineraEase, Inc., an ItineraEase 
              Group company. All rights reserved. ItineraEase and the ItineraEase 
              Logo are trademarks of ItineraEase, Inc.</p>
        </div>
    </div>
</footer>

<script>
function myFunction() {
    document.getElementById("myDropdown").classList.toggle("show");
}

// Close the dropdown menu if the user clicks outside of it
window.onclick = function(event) {
    if (!event.target.matches('.dropbtn')) {
        var dropdowns = document.getElementsByClassName("dropdown-content");
        var i;
        for (i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (openDropdown.classList.contains('show')) {
                openDropdown.classList.remove('show');
            }   
        }
    }
}
const hamburgerMenu = document.querySelector(".hamburger_menu");
const navMenu = document.querySelector(".nav_menu");
hamburgerMenu.addEventListener("click", toggleMenu);

function toggleMenu() {
    hamburgerMenu.classList.toggle("change");
    navMenu.classList.toggle("change");
}

var tablinks = document.getElementsByClassName("tab-links");
var tabcontents = document.getElementsByClassName("tab-contents");

function opentab(tabname) {
    for(tablink of tablinks) {
        tablink.classList.remove("active-link");
    }

    for(tabcontent of tabcontents) {
        tabcontent.classList.remove("active-tab");
    }

    event.currentTarget.classList.add("active-link");
    document.getElementById(tabname).classList.add("active-tab")
}
</script>
