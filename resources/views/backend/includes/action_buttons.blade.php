<div class="dropdown">
    <a class="btn btn-sm btn-primary btn-icon-only" href="javascript:void(0)" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-ellipsis-v"></i>
    </a>
    <ul class="dropdown-menu dropdown-menu-end">
        @foreach($menuItems as $menuItem)
        @php
            //This function will take the route name and return the access permission.
            if(!isset($menuItem['routeName']) || $menuItem['routeName'] == '' || $menuItem['routeName'] == null || $menuItem['routeName'] =='javascript:void(0)'){
                $check = false;
            }else{
                $check = check_access_by_route_name($menuItem['routeName']);
            }

            $parameterArray = isset($menuItem['params']) ? $menuItem['params'] : [];
            $method = isset($menuItem['method']) ? $menuItem['method'] : 'get';
            $warning = isset($menuItem['warning']) ? $menuItem['warning'] : false;
            $className = isset($menuItem['className']) ? $menuItem['className'] : '';
        @endphp
        @if ($check)
            @if($method == 'get')
                <a class="dropdown-item {{$className}} @if($warning) action-delete @endif" @if($warning) @endif href="{{ route($menuItem['routeName'], $parameterArray) }}">{{ __($menuItem['label']) }}</a>
            @else
                <form action="{{ route($menuItem['routeName'], $parameterArray) }}" method="POST">
                    @csrf
                    @method($method)
                    <button type="submit" class="dropdown-item {{$className}} @if($warning) action-delete @endif" @if($warning) @endif>{{ __($menuItem['label']) }}</button>
                </form>
            @endif
        @elseif($menuItem['routeName']=='javascript:void(0)')
            <a class="dropdown-item {{$className}} @if($warning) action-delete @endif" data-id="{{$menuItem['params'][0]}}" href="{{$menuItem['routeName']}}">{{ __($menuItem['label']) }}</a>
        @endif

        @endforeach
    </ul>
</div>
