<nav class="desktopNav">
    <div class="column">
        <div class="top">
            <div class="logo"></div>
            <p class="border"></p>
        </div>
        <div class="menu">
            <a href="./dashboard.php">
                <div>
                    <img class="<?php echo ($current_page == 'dashboard') ? 'dashboardItem active' : 'dashboardItem'; ?>"
                        src="assets/images/Dashboard.svg" alt="dashboardIcon">
                    <p>Home</p>
                </div>
            </a>
            <a href="./roadmap.php">
                <div>
                    <img class="<?php echo ($current_page == 'roadmap') ? 'roadmapItem active' : 'roadmapItem'; ?>"
                        src="assets/images/Process.svg" alt="roadmapIcon">
                    <p>Stappenplan</p>
                </div>
            </a>
            <a href="./stats.php">
                <div>
                    <img class="<?php echo ($current_page == 'stats') ? 'statsItem active' : 'statsItem'; ?>"
                        src="assets/images/Stats.svg" alt="statsIcon">
                    <p>Statistieken</p>
                </div>
            </a>
            <a href="./subsidies.php">
                <div>
                    <img class="<?php echo ($current_page == 'subsidies') ? 'subsidiesItem active' : 'subsidiesItem'; ?>"
                        src="assets/images/subsidiesItem.svg" alt="subsidiesIcon">
                    <p>Subsidies</p>
                </div>
            </a>
            <a href="./helpdesk.php">
                <div>
                    <img class="<?php echo ($current_page == 'helpdesk') ? 'helpdeskItem active' : 'helpdeskItem'; ?>"
                        src="assets/images/Helpdesk.svg" alt="helpdeskIcon">
                    <p>Helpdesk</p>
                </div>
            </a>
        </div>
        <div class="logout">
            <a href="logout.php" style="color: white;">Logout</a>
        </div>
        <div class="settings">
            <a href="./settings.php">
                <div>
                    <img class="<?php echo ($current_page == 'settings') ? 'settingsItem active' : 'settingsItem'; ?>"
                        src="assets/images/Settings.svg" alt="settingsIcon">
                    <p>Settings</p>
                </div>
            </a>
            <img src="assets/images/Be.png" alt=".be">
        </div>
    </div>
    <div class="column center">
        <i class="fa fa-angle-double-right" aria-hidden="true"></i>
    </div>
</nav>
<script>
    function executeOnMaxWidth1200(callback) {
        const maxWidthQuery = window.matchMedia("(max-width: 1200px)");
        if (maxWidthQuery.matches) {
            callback();
        }
        maxWidthQuery.addListener((event) => {
            if (event.matches) {
                callback();
            }
        });
    }

    function executeOnMinWidth1200(callback) {
        const minWidthQuery = window.matchMedia("(min-width: 1200px)");
        if (minWidthQuery.matches) {
            callback();
        }
        minWidthQuery.addListener((event) => {
            if (event.matches) {
                callback();
            }
        });
    }

    executeOnMaxWidth1200(function () {
        let navIcon = document.querySelector(".desktopNav .center i");
        let desktopNav = document.querySelector(".desktopNav");
        let isOpen = false;

        navIcon.addEventListener("click", function (e) {
            if (!isOpen) {
                desktopNav.classList.add("open-desktopNav"); // Voeg de klasse toe om de animatie te starten
            } else {
                desktopNav.classList.remove("open-desktopNav"); // Verwijder de klasse om de animatie om te keren
            }
            isOpen = !isOpen;
        });
    });

    executeOnMinWidth1200(function () {
        let navIcon = document.querySelector(".desktopNav .center i");
        let desktopNav = document.querySelector(".desktopNav");
        let pTags = document.querySelectorAll(".desktopNav .column div div p");
        let desktopNavLogo = document.querySelector(".desktopNav .column .top .logo");
        let isOpen = false;

        navIcon.addEventListener("click", function (e) {
            if (!isOpen) {
                desktopNavLogo.style.backgroundImage = "url('assets/images/logoWhite.svg')";
                desktopNavLogo.style.width = "128px";
                desktopNav.style.width = "200px";
                pTags.forEach(pTag => {
                    pTag.style.display = "flex";
                });
            } else {
                desktopNav.style.width = "120px";
                desktopNavLogo.style.backgroundImage = "url('assets/images/FaviconWhite.svg')"; // Terugkeren naar standaard achtergrondafbeelding
                desktopNavLogo.style.width = "48px";
                pTags.forEach(pTag => {
                    pTag.style.display = "none";
                });
            }
            isOpen = !isOpen;
        });
    });



</script>