document.addEventListener("DOMContentLoaded", () => {
    const profileButton = document.getElementById("profileButton");
    const profileMenu = document.getElementById("profileMenu");

    if (!profileButton || !profileMenu) return;

    profileButton.addEventListener("click", (event) => {
        event.stopPropagation();
        const isHidden = profileMenu.classList.toggle("hidden");
        profileButton.setAttribute("aria-expanded", String(!isHidden));
    });

    document.addEventListener("click", (event) => {
        if (
            !profileButton.contains(event.target) &&
            !profileMenu.contains(event.target)
        ) {
            profileMenu.classList.add("hidden");
            profileButton.setAttribute("aria-expanded", "false");
        }
    });
});