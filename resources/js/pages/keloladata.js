document.addEventListener("DOMContentLoaded", () => {
    const openUploadModal = document.getElementById("openUploadModal");
    const uploadModal = document.getElementById("uploadModal");
    const closeUploadModal = document.getElementById("closeUploadModal");
    const pickUploadFile = document.getElementById("pickUploadFile");
    const uploadFileInput = document.getElementById("uploadFileInput");
    const uploadFileName = document.getElementById("uploadFileName");

    const openModal = () => {
        if (!uploadModal) return;
        uploadModal.classList.remove("hidden");
        uploadModal.classList.add("flex");
    };

    const closeModal = () => {
        if (!uploadModal) return;
        uploadModal.classList.add("hidden");
        uploadModal.classList.remove("flex");
    };

    if (openUploadModal) {
        openUploadModal.addEventListener("click", openModal);
    }

    if (closeUploadModal) {
        closeUploadModal.addEventListener("click", closeModal);
    }

    if (uploadModal) {
        uploadModal.addEventListener("click", (event) => {
            if (event.target === uploadModal) {
                closeModal();
            }
        });
    }

    if (pickUploadFile && uploadFileInput && uploadFileName) {
        pickUploadFile.addEventListener("click", () => {
            uploadFileInput.click();
        });

        uploadFileInput.addEventListener("change", () => {
            const files = Array.from(uploadFileInput.files ?? []);
            const totalFiles = files.length;
            const label = totalFiles === 0
                ? "Pilih satu atau beberapa file CSV/TXT.."
                : totalFiles === 1
                    ? files[0].name
                    : `${files[0].name} +${totalFiles - 1} file`;

            uploadFileName.textContent = label;
            uploadFileName.classList.toggle("text-zinc-400", totalFiles === 0);
            uploadFileName.classList.toggle("text-neutral-700", totalFiles > 0);
        });
    }
});
