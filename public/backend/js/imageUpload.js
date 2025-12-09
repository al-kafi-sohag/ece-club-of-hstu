var allowedTypes = ["image/jpeg", "image/jpg", "image/png", "image/webp","image/svg+xml"];
var defaultMaxSize = 2; // 2MB default
var cropper = null;
var currentInput = null;
var errorMsg = "";

$(document).ready(function () {
    $(".dynamic-image").each(function () {
        var input = $(this);
        var src = input.data("src");

        if (src) {
            renderPreview(input, src);
        }

        input.off("change").on("change", function (e) {
            var file = e.target.files[0];
            if (!file) return;

            var maxSize = (input.data("maxfilesize") || defaultMaxSize) * 1024 * 1024;
            console.log(input.data("maxfilesize"),defaultMaxSize,maxSize);
            if (!allowedTypes.includes(file.type)) {
                errorMsg = "Please select a valid image file " + allowedTypes.join(", ") + " only.";
                toastr.error(errorMsg);
                input.val("");
                return;
            }

            if (file.size > maxSize) {
                errorMsg = "File size must be less than " + maxSize / 1024 / 1024 + "MB";
                toastr.error(errorMsg);
                input.val("");
                return;
            }

            var reader = new FileReader();
            reader.onload = function (e) {
                currentInput = input;
                $("#imageToCrop").attr("src", e.target.result);
                $("#cropModal").modal("show");
            };
            reader.readAsDataURL(file);
        });
    });

    // Initialize cropper when modal opens
    $("#cropModal").on("shown.bs.modal", function () {
        if (cropper) cropper.destroy();

        var width = currentInput.data("width") || 300;
        var height = currentInput.data("height") || 300;

        cropper = new Cropper(document.getElementById("imageToCrop"), {
            aspectRatio: width / height,
            viewMode: 1,
            autoCropArea: 1,
            responsive: true,
            background: false,
            zoomable: true,
        });
        });

    // Handle crop
    $("#cropButton").click(function () {
        if (!cropper || !currentInput) return;

        var width = currentInput.data("width") || 300;
        var height = currentInput.data("height") || 300;

        // Preserve original file type
        var originalFile = currentInput[0].files[0];
        var originalType = originalFile.type || "image/png"; // default fallback
        var originalExt = originalType.split("/")[1] || "png";

        var canvas = cropper.getCroppedCanvas({
            width,
            height,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: "high",
        });

        canvas.toBlob(
            function (blob) {
                if (!blob || blob.size === 0) {
                    toastr.error("Cropping failed, please try again.");
                    return;
                }

                var croppedFile = new File([blob], `cropped_image.${originalExt}`, {
                    type: originalType,
                });

                var dataTransfer = new DataTransfer();
                dataTransfer.items.add(croppedFile);
                currentInput[0].files = dataTransfer.files;

                renderPreview(currentInput, canvas.toDataURL(originalType));

                $("#cropModal").modal("hide");
            },
            originalType
        );
    });

    $("#cropModal").on("hidden.bs.modal", function () {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
    });

    function renderPreview(input, src) {
        const wrapper = input.closest(".form-group");
        wrapper.find(".image-preview").remove();

        const preview = $(`
            <div class="image-preview mt-2 position-relative d-inline-block">
                <img src="${src}" class="img-thumbnail" style="max-width: 150px; height: auto;">
                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 remove-image">&times;</button>
            </div>
        `);

        preview.find(".remove-image").on("click", function () {
            preview.remove();
            input.val("");
        });

        wrapper.append(preview);
    }
});
