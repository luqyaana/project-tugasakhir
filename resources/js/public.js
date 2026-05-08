document.addEventListener("DOMContentLoaded", () => {
    const btn = document.getElementById("menu-btn");
    const menu = document.getElementById("mobile-menu");

    if (!btn || !menu) return;

    btn.addEventListener("click", () => {
        const isHidden = menu.classList.toggle("hidden");
        btn.setAttribute("aria-expanded", String(!isHidden));
    });

    window.addEventListener("resize", () => {
        if (window.innerWidth >= 768) {
            menu.classList.add("hidden");
            btn.setAttribute("aria-expanded", "false");
        }
    });
});