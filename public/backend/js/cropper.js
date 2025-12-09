$(document).ready(function () {
    let cropper;
    const maxFileSize = 5 * 1024 * 1024; // 5MB in bytes
    const allowedTypes = ["image/jpeg", "image/jpg", "image/png"];

    $("#cameraIcon").click(function () {
        $("#image").click();
    });

    $("#image").change(function (e) {
        const file = e.target.files[0];
        if (!file) return;

        if (!allowedTypes.includes(file.type)) {
            flasher.error(
                "Please select a valid image file (JPG, PNG, JPEG only)"
            );
            this.value = "";
            return;
        }

        if (file.size > maxFileSize) {
            flasher.error("File size must be less than 5MB");
            this.value = "";
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            $("#imageToCrop").attr("src", e.target.result);
            $("#cropModal").modal("show");
            $("#cropModal").on("shown.bs.modal", function () {
                if (cropper) {
                    cropper.destroy();
                }

                cropper = new Cropper(document.getElementById("imageToCrop"), {
                    aspectRatio: 1,
                    viewMode: 1,
                    dragMode: "move",
                    autoCropArea: 0.8,
                    restore: false,
                    guides: true,
                    center: true,
                    highlight: false,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                });
            });
        };
        reader.readAsDataURL(file);
    });

    $("#cropButton").click(function () {
        if (!cropper) return;

        const canvas = cropper.getCroppedCanvas({
            width: 300,
            height: 300,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: "high",
        });

        canvas.toBlob(
            function (blob) {
                const croppedFile = new File([blob], "cropped_image.jpg", {
                    type: "image/jpeg",
                });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(croppedFile);
                const imageInput = document.getElementById("image");
                imageInput.files = dataTransfer.files;

                const previewImg = document.querySelector(".profile-image");
                previewImg.src = canvas.toDataURL("image/jpeg");

                $("#cropModal").modal("hide");
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
            },
            "image/jpeg",
            0.9
        );
    });

    $("#cropModal").on("hidden.bs.modal", function () {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
    });
});
