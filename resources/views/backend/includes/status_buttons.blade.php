<div class="btn-group">
    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
        @isset($action_text)
            {!!  $action_text !!}
        @else
            Action
        @endisset
    </button>
    <ul class="dropdown-menu">
        @foreach ($menuItems as $menuItem)
            @php
                //This function will take the route name and return the access permission.
                if (
                    !isset($menuItem['routeName']) ||
                    $menuItem['routeName'] == '' ||
                    $menuItem['routeName'] == null ||
                    $menuItem['routeName'] == 'javascript:void(0)'
                ) {
                    $check = false;
                } else {
                    $check = check_access_by_route_name($menuItem['routeName']);
                }

                //Parameters
                $parameterArray = isset($menuItem['params']) ? $menuItem['params'] : [];
            @endphp
            @if ($check)
                <li>
                    <a class="dropdown-item @if (isset($menuItem['className'])) {{ $menuItem['className'] }} @endif @if (isset($menuItem['delete']) && $menuItem['delete'] == true) action-delete @endif"
                    @if (isset($menuItem['delete']) && $menuItem['delete'] == true) onclick="return confirm('Are you sure?')" @endif
                    href="{{ route($menuItem['routeName'], $parameterArray) }}">{{ _($menuItem['label']) }}</a>
                </li>
            @elseif($menuItem['routeName'] == 'javascript:void(0)')
                <li>
                    <a class="dropdown-item @if (isset($menuItem['className'])) {{ $menuItem['className'] }} @endif"
                    data-id="{{ $menuItem['params'][0] }}"
                    href="{{ $menuItem['routeName'] }}">{{ _($menuItem['label']) }}</a>
                </li>
            @endif
        @endforeach
    </ul>
</div>

