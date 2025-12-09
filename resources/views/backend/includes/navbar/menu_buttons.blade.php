@foreach($menuItems as $menuItem)
{{-- @dd($menuItem) --}}
    @php
        //This function will take the route name and return the access permission.
        if( !isset($menuItem['routeName']) || $menuItem['routeName'] == '' || $menuItem['routeName'] == null){
            $check = false;
        }else{
            if(isset($menuItem['permission']) && $menuItem['permission'] != '' && $menuItem['permission'] != null){
                $check = check_access_by_route_name($menuItem['routeName'],$menuItem['permission']);
            }else{
                $check = check_access_by_route_name($menuItem['routeName']);
            }
        }

        //Parameters
        $parameterArray = isset($menuItem['params']) ? $menuItem['params'] : [];
    @endphp
    @if ($check)
        <li class="nav-item @if ($page_slug == $menuItem['pageSlug']) active  @endif">
            <a href="{{ route($menuItem['routeName'], $parameterArray) }}"
                class="nav-link align-items-center @if ($page_slug == $menuItem['pageSlug']) active  @endif">
                <i class="nav-icon {{ __($menuItem['iconClass'] ?? 'fa-solid fa-minus') }} @if ($page_slug == $menuItem['pageSlug']) fa-beat-fade @endif"></i>
                <p>{{ __($menuItem['label']) }}</p>
            </a>
        </li>
    @endif
    {{-- For Main Menus  --}}
    @if(!isset($menuItem['routeName']) || $menuItem['routeName'] == '' || $menuItem['routeName'] == null)
        <li class="nav-item @if ($page_slug == $menuItem['pageSlug']) active @endif" >
            <a href="" class="nav-link align-items-center @if ($page_slug == $menuItem['pageSlug']) active  @endif">
                <i class="nav-icon {{ __($menuItem['iconClass'] ?? 'fa-solid fa-minus') }} @if ($page_slug == $menuItem['pageSlug']) fa-beat-fade @endif"></i>
                <p>{{ __($menuItem['label']) }}</p>
            </a>
        </li>
    @endif
@endforeach

