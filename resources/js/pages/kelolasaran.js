document.addEventListener("DOMContentLoaded", () => {
    const editSaranButtons = document.querySelectorAll(".edit-saran-btn");
    const editSaranModal = document.getElementById("editSaranModal");
    const closeEditSaranModal = document.getElementById("closeEditSaranModal");
    const editSaranForm = document.getElementById("editSaranForm");
    const editKategoriIspu = document.getElementById("editKategoriIspu");
    const editSaranText = document.getElementById("editSaranText");

    const openModal = () => {
        if (!editSaranModal) return;
        editSaranModal.classList.remove("hidden");
        editSaranModal.classList.add("flex");
    };

    const closeModal = () => {
        if (!editSaranModal) return;
        editSaranModal.classList.add("hidden");
        editSaranModal.classList.remove("flex");
    };

    editSaranButtons.forEach((button) => {
        button.addEventListener("click", () => {
            if (editKategoriIspu) {
                editKategoriIspu.value = button.dataset.kategori ?? "";
            }

            if (editSaranText) {
                editSaranText.value = button.dataset.saran ?? "";
            }

            if (editSaranForm) {
                editSaranForm.action = button.dataset.updateUrl ?? "";
            }

            openModal();
        });
    });

    if (closeEditSaranModal) {
        closeEditSaranModal.addEventListener("click", closeModal);
    }

    if (editSaranModal) {
        editSaranModal.addEventListener("click", (event) => {
            if (event.target === editSaranModal) {
                closeModal();
            }
        });
    }
});
