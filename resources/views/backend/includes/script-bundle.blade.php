{{-- Flasher JS --}}
<script src="{{ asset('vendor/flasher/flasher.min.js') }}"></script>
<script src="{{ asset('vendor/flasher/sweetalert2.min.js') }}"></script>
<script src="{{ asset('vendor/flasher/toastr.min.js') }}"></script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/pickr.min.js"></script>

<script src="{{ asset('backend/js/custom.js') }}"></script>
<script src="{{ asset('backend/js/imageUpload.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

@stack('scripts')
