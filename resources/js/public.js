document.addEventListener("DOMContentLoaded", () => {
    const btn = document.getElementById("menu-btn");
    const menu = document.getElementById("mobile-menu");

    if (!btn || !menu) return;

    const openMenu = () => {
        menu.classList.remove("hidden");
        btn.setAttribute("aria-expanded", "true");
    };

    const closeMenu = () => {
        menu.classList.add("hidden");
        btn.setAttribute("aria-expanded", "false");
    };

    const toggleMenu = (event) => {
        event.stopPropagation();

        if (menu.classList.contains("hidden")) {
            openMenu();
        } else {
            closeMenu();
        }
    };

    btn.addEventListener("click", toggleMenu);

    menu.querySelectorAll("a").forEach((link) => {
        link.addEventListener("click", closeMenu);
    });

    document.addEventListener("click", (event) => {
        const clickInsideMenu = menu.contains(event.target);
        const clickOnButton = btn.contains(event.target);

        if (!clickInsideMenu && !clickOnButton) {
            closeMenu();
        }
    });

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape") {
            closeMenu();
        }
    });

    window.addEventListener("resize", () => {
        if (window.innerWidth >= 768) {
            closeMenu();
        }
    });
});