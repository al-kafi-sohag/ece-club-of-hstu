document.addEventListener('DOMContentLoaded', function () {
    // ===== Edit page simple "Add Page" (existing behaviour) =====
    const addPageButton = document.getElementById('add-page-button');
    const addPageForm = document.getElementById('add-page-form');

    if (addPageButton && addPageForm) {
        addPageButton.addEventListener('click', function (e) {
            console.log('addPageForm  exist');
            // On edit page we still support the simple "add blank page" flow
            if (addPageForm) {
                e.preventDefault();
                addPageForm.submit();
            }
        });
    }
});

// ===== Create page inline page builder (jQuery based) =====
(function ($) {
    $(function () {
        const $pagesCount = $('input[name="pages_count"]');
        const $pagesList = $('#book-pages-list');
        const $pageBuilderForm = $(
            'form[action*="backend/book/create"], ' +
            'form[action*="backend/book/edit"]'
        );
        const $titleInput = $('input[name="title"]');
        const $slugInput = $('input[name="slug"]');
        const $priceInput = $('input[name="price"]');
        const $discountPriceInput = $('input[name="discount_price"]');
        const $discountBadge = $('#discount-percentage-badge');

        // Auto-generate slug from title (only when slug not manually changed)
        if ($titleInput.length && $slugInput.length) {
            let lastAutoSlug = $slugInput.val() || '';

            function slugify(str) {
                return str
                    .toString()
                    .toLowerCase()
                    .trim()
                    .replace(/[\s\_]+/g, '-')      // spaces/underscores to dashes
                    .replace(/[^a-z0-9\-]/g, '')   // remove non-alphanumeric/dash
                    .replace(/\-+/g, '-');         // collapse multiple dashes
            }

            $titleInput.on('input', function () {
                const titleVal = $(this).val() || '';
                const generated = slugify(titleVal);

                const currentSlug = $slugInput.val() || '';
                if (!currentSlug || currentSlug === lastAutoSlug) {
                    $slugInput.val(generated);
                    lastAutoSlug = generated;
                }
            });

            $slugInput.on('input', function () {
                // As soon as user edits slug manually, stop forcing updates
                lastAutoSlug = $(this).val() || '';
            });
        }

        // Live discount percentage badge under discount_price
        function updateDiscountBadge() {
            if (!$priceInput.length || !$discountPriceInput.length || !$discountBadge.length) {
                return;
            }

            const price = parseFloat($priceInput.val());
            const discountPrice = parseFloat($discountPriceInput.val());

            if (!price || !discountPrice || discountPrice > price) {
                $discountBadge.addClass('d-none');
                return;
            }

            if(price == discountPrice){
               var  percent = 100;
            }else{
                var percent = 100 - Math.round(((price - discountPrice) / price) * 100);
            }

            if (percent <= 0) {
                $discountBadge.addClass('d-none');
                return;
            }

            $discountBadge.text(percent + '%');
            $discountBadge.removeClass('d-none');
        }

        if ($priceInput.length && $discountPriceInput.length && $discountBadge.length) {
            $priceInput.on('input', updateDiscountBadge);
            $discountPriceInput.on('input', updateDiscountBadge);
            // Initial call in case values are pre-filled (edit page)
            updateDiscountBadge();
        }

        if ($pagesList.length === 0 || $pageBuilderForm.length === 0) {
            return;
        }

        function renderPages(count) {
            let total = parseInt(count, 10);
            if (!total || total < 1) {
                $pagesList.empty();
                return;
            }

            const existingCards = $pagesList.find('.page-card');
            const existingCount = existingCards.length;

            // Nothing to do if counts match
            if (existingCount === total) {
                return;
            }

            // If total is less than current, remove extra pages from the end
            if (total < existingCount) {
                const toRemove = existingCount - total;
                $pagesList
                    .find('.page-card')
                    .slice(-toRemove)
                    .each(function () {
                        $(this).closest('.col-12').remove();
                    });
                return;
            }

            // If total is greater than current, append new blank pages
            for (let i = existingCount + 1; i <= total; i++) {
                const index = i - 1;

                const cardHtml = `
                    <div class="col-12 mb-3">
                        <div class="card page-card h-100" data-index="${index}">
                            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                                <strong>Page ${i}</strong>
                                <button type="button"
                                        class="btn btn-xs btn-outline-danger remove-page-button"
                                        data-index="${index}"
                                        title="Remove this page">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="page-canvas position-relative border rounded overflow-hidden" style="height: 260px; background:#f8f9fa;">
                                    <div class="page-placeholder d-flex align-items-center justify-content-center h-100 text-muted small">
                                        Upload an image for this page
                                    </div>
                                    <img src="" alt="Page ${i}" class="page-image img-fluid w-100 h-100 object-fit-cover d-none">
                                    <div class="page-text-overlay position-absolute text-center small"
                                        contenteditable="false"
                                        data-mode="drag"
                                        data-index="${index}"
                                        data-x="50"
                                        data-y="50"
                                        style="cursor: move; top:17.0513%; left:50%; transform:translate(-50%, -50%);">
                                        Double click here and type text for this page...
                                    </div>
                                </div>

                                <div class="form-group mt-2">
                                    <label class="form-label mb-1">Page Image</label>
                                    <input type="file"
                                           name="pages[${index}][background_image]"
                                           class="form-control page-background-input"
                                           data-index="${index}"
                                           accept="image/jpeg,image/jpg,image/png,image/webp,image/svg+xml">
                                </div>

                                <input type="hidden" name="pages[${index}][page_number]" value="${i}">
                                <input type="hidden" name="pages[${index}][text]" class="page-text-input-${index}">
                                <input type="hidden" name="pages[${index}][text_x]" class="page-text-x-${index}" value="50">
                                <input type="hidden" name="pages[${index}][text_y]" class="page-text-y-${index}" value="50">
                            </div>
                        </div>
                    </div>
                `;

                $pagesList.append(cardHtml);
            }
        }

        // Generate pages when pages_count changes
        $pagesCount.on('change keyup', function () {
            const value = $(this).val();
            renderPages(value);
        });

        // Simple drag for text overlay within page canvas
        let dragging = null;
        let dragOffset = { x: 0, y: 0 };

        $(document).on('mousedown', '.page-text-overlay', function (e) {
            const $this = $(this);

            // If in edit mode, don't start drag
            if ($this.data('mode') === 'edit') {
                return;
            }

            const $canvas = $this.closest('.page-canvas');
            if ($canvas.length === 0) return;

            dragging = {
                $el: $this,
                $canvas: $canvas
            };

            const rect = $canvas[0].getBoundingClientRect();
            const elRect = this.getBoundingClientRect();

            dragOffset.x = e.clientX - elRect.left;
            dragOffset.y = e.clientY - elRect.top;

            e.preventDefault();
        });

        $(document).on('mouseup', function () {
            dragging = null;
        });

        $(document).on('mousemove', function (e) {
            if (!dragging) return;

            const rect = dragging.$canvas[0].getBoundingClientRect();

            let left = e.clientX - rect.left - dragOffset.x;
            let top = e.clientY - rect.top - dragOffset.y;

            // constrain inside canvas
            left = Math.max(0, Math.min(left, rect.width - dragging.$el.outerWidth()));
            top = Math.max(0, Math.min(top, rect.height - dragging.$el.outerHeight()));

            // convert to percentage for storage
            const xPercent = (left + dragging.$el.outerWidth() / 2) / rect.width * 100;
            const yPercent = (top + dragging.$el.outerHeight() / 2) / rect.height * 100;

            dragging.$el.css({
                left: xPercent + '%',
                top: yPercent + '%',
                transform: 'translate(-50%, -50%)'
            });

            dragging.$el.attr('data-x', xPercent.toFixed(2));
            dragging.$el.attr('data-y', yPercent.toFixed(2));
        });

        // Remove a single page card
        $(document).on('click', '.remove-page-button', function () {
            const index = $(this).data('index');
            const $wrapper = $('.page-card[data-index="' + index + '"]').closest('.col-12');

            if ($wrapper.length) {
                $wrapper.remove();
            }

            // Update pages_count to reflect remaining pages
            const remaining = $('.page-card').length;
            if (remaining >= 0 && $pagesCount.length) {
                $pagesCount.val(remaining);
            }
        });

        // Double-click → switch to edit mode (typing)
        $(document).on('dblclick', '.page-text-overlay', function () {
            const $this = $(this);
            $this.attr('contenteditable', 'true');
            $this.data('mode', 'edit');
            $this.css('cursor', 'text');
            $this.focus();
        });

        // Blur → back to drag mode
        $(document).on('blur', '.page-text-overlay', function () {
            const $this = $(this);
            $this.attr('contenteditable', 'false');
            $this.data('mode', 'drag');
            $this.css('cursor', 'move');
        });

        // ESC key inside textbox → finish editing (back to drag)
        $(document).on('keydown', '.page-text-overlay', function (e) {
            if (e.key === 'Escape') {
                e.preventDefault();
                $(this).blur();
            }
        });

        // + Add Page button (create & edit pages) – increments count and regenerates
        $(document).on('click', '#add-page-button', function (e) {
            console.log('add page button clicked');
            console.log($pageBuilderForm.length);
            if (!$pageBuilderForm.length) {
                return;
            }
            e.preventDefault();
            let current = parseInt($pagesCount.val() || '0', 10);
            current = isNaN(current) ? 0 : current;
            current += 1;
            $pagesCount.val(current);
            renderPages(current);
        });

        // Image preview inside card
        $(document).on('change', '.page-background-input', function () {
            const input = this;
            const index = $(this).data('index');
            const file = input.files && input.files[0];
            if (!file) {
                return;
            }

            const reader = new FileReader();
            reader.onload = function (ev) {
                const $card = $('.page-card[data-index="' + index + '"]');
                $card.find('.page-image')
                    .attr('src', ev.target.result)
                    .removeClass('d-none');
                $card.find('.page-placeholder').addClass('d-none');
            };
            reader.readAsDataURL(file);
        });

        // Before submit, sync overlay text into hidden inputs
        $pageBuilderForm.on('submit', function () {
            $('.page-text-overlay').each(function () {
                const index = $(this).data('index');
                const text = $(this).text().trim();
                const $hidden = $('.page-text-input-' + index);
                const $hiddenX = $('.page-text-x-' + index);
                const $hiddenY = $('.page-text-y-' + index);
                const x = $(this).attr('data-x') || 50;
                const y = $(this).attr('data-y') || 50;
                if ($hidden.length) {
                    $hidden.val(text);
                }
                if ($hiddenX.length) {
                    $hiddenX.val(x);
                }
                if ($hiddenY.length) {
                    $hiddenY.val(y);
                }
            });
        });
    });
})(jQuery);

