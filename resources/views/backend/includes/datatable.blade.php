{{-- To use this component, you need to pass the following parameters:
    mainClass: string
    columns_to_show: array
    order: string
    length: int


    Example:
    @include('backend.includes.datatable', [
        'mainClass' => 'users-table',
        'columns_to_show' => [0, 1, 2, 3, 4],
        'order' => 'desc',
        'length' => 10,
    ])

--}}

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    $(document).ready(function() {
        var main_class = {!! json_encode($mainClass ?? 'datatable') !!};
        var columnsToShow = {!! json_encode($columns_to_show ?? []) !!};
        var order = {!! json_encode($order ?? 'asc') !!};
        var length = {!! json_encode($length ?? 10) !!};

        $('.' + main_class).css('width', '100%');
        $('.' + main_class).each(function() {
            $(this).DataTable({
                processing: true,
                serverSide: false,
                dom: 'Bfrtip',
                responsive: true,
                iDisplayLength: length,
                order: [[0, order]],
                buttons: ['colvis', 'pageLength'],
                language: {
                    emptyTable: 'No data available in table',
                    zeroRecords: 'No matching records found'
                }
            });
        });
    });
});
</script>
@endpush
