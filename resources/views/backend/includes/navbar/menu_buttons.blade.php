@foreach($menuItems as $menuItem)
{{-- @dd($menuItem) --}}
    @php
        $check = true;
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

