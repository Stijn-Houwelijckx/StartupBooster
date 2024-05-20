<nav class="mobileNav">
    <div class="hamburger"></div>
    <div class="profilePicture"></div>
</nav>

<div class="mobilemenu">
    <div class="top">
        <i class="fa fa-plus"></i>
        <div class="profilePicture"></div>
    </div>
    <div class="menu">
        <a href="./dashboard.php">
            <div>
                <img class="<?php echo ($current_page == 'dashboard') ? 'dashboardItem active' : 'dashboardItem'; ?>"
                    src="../assets/images/icons/dashboard.svg" alt="dashboardIcon">
                <p>Home</p>
            </div>
        </a>
        <a href="./tasks.php">
            <div>
                <img class="<?php echo ($current_page == 'roadmap') ? 'roadmapItem active' : 'roadmapItem'; ?>"
                    src="../assets/images/icons/process.svg" alt="roadmapIcon">
                <p>Stappenplan</p>
            </div>
        </a>
        <a href="./stats.php">
            <div>
                <img class="<?php echo ($current_page == 'stats') ? 'statsItem active' : 'statsItem'; ?>"
                    src="../assets/images/icons/stats.svg" alt="statsIcon">
                <p>Statistieken</p>
            </div>
        </a>
        <a href="./subsidies.php">
            <div>
                <img class="<?php echo ($current_page == 'subsidies') ? 'subsidiesItem active' : 'subsidiesItem'; ?>"
                    src="../assets/images/icons/subsidiesItem.svg" alt="subsidiesIcon">
                <p>Subsidies</p>
            </div>
        </a>
        <a href="./users.php">
            <div>
                <img class="<?php echo ($current_page == 'users') ? 'usersItem active' : 'usersItem'; ?>"
                    src="../assets/images/icons/users.svg" alt="usersIcon">
                <p>Users</p>
            </div>
        </a>
    </div>
</div>

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
                        src="../assets/images/icons/dashboard.svg" alt="dashboardIcon">
                    <p>Home</p>
                </div>
            </a>
            <a href="./tasks.php">
                <div>
                    <img class="<?php echo ($current_page == 'roadmap') ? 'roadmapItem active' : 'roadmapItem'; ?>"
                        src="../assets/images/icons/process.svg" alt="roadmapIcon">
                    <p>Stappenplan</p>
                </div>
            </a>
            <a href="./stats.php">
                <div>
                    <img class="<?php echo ($current_page == 'stats') ? 'statsItem active' : 'statsItem'; ?>"
                        src="../assets/images/icons/stats.svg" alt="statsIcon">
                    <p>Statistieken</p>
                </div>
            </a>
            <a href="./subsidies.php">
                <div>
                    <img class="<?php echo ($current_page == 'subsidies') ? 'subsidiesItem active' : 'subsidiesItem'; ?>"
                        src="../assets/images/icons/subsidiesItem.svg" alt="subsidiesIcon">
                    <p>Subsidies</p>
                </div>
            </a>
            <a href="./users.php">
                <div>
                    <img class="<?php echo ($current_page == 'users') ? 'usersItem active' : 'usersItem'; ?>"
                        src="../assets/images/icons/users.svg" alt="usersIcon">
                    <p>Users</p>
                </div>
            </a>
        </div>
        <div class="settings">
            <a href="../logout.php">
                <div>
                    <img class="<?php echo ($current_page == 'logout') ? 'logoutItem active' : 'logoutItem'; ?>"
                        src="../assets/images/icons/logout.svg" alt="logoutIcon">
                    <p>Logout</p>
                </div>
            </a>
            <a href="./settings.php">
                <div>
                    <img class="<?php echo ($current_page == 'settings') ? 'settingsItem active' : 'settingsItem'; ?>"
                        src="../assets/images/icons/settings.svg" alt="settingsIcon">
                    <p>Settings</p>
                </div>
            </a>
            <img src="../assets/images/Be.png" alt=".be">
        </div>
    </div>
    <div class="column center">
        <i class="fa fa-angle-double-right" aria-hidden="true"></i>
    </div>
</nav>
<script>
    let hamburger = document.querySelector(".mobileNav .hamburger");
    let mobilemenu = document.querySelector(".mobilemenu");
    let mobilemenuItems = document.querySelectorAll(".mobilemenu a");

    hamburger.addEventListener("click", function (e) {
        document.querySelector(".mobileNav").style.display = "none";
        mobilemenu.style.display = "flex";

        document.querySelector(".mobilemenu .fa-plus").addEventListener("click", function (e) {
            mobilemenu.style.display = "none";
            document.querySelector(".mobileNav").style.display = "flex";
        });
    });

    for (let i = 0; i < mobilemenuItems.length; i++) {
        mobilemenuItems[i].addEventListener("click", function (e) {
            mobilemenu.style.display = "none";
        });
    }


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
                desktopNavLogo.style.backgroundImage = "url('../assets/images/logoWhite.svg')";
                desktopNavLogo.style.width = "128px";
                desktopNav.style.width = "200px";
                pTags.forEach(pTag => {
                    pTag.style.display = "flex";
                });
            } else {
                desktopNav.style.width = "120px";
                desktopNavLogo.style.backgroundImage = "url('../assets/images/FaviconWhite.svg')"; // Terugkeren naar standaard achtergrondafbeelding
                desktopNavLogo.style.width = "48px";
                pTags.forEach(pTag => {
                    pTag.style.display = "none";
                });
            }
            isOpen = !isOpen;
        });
    });
</script>