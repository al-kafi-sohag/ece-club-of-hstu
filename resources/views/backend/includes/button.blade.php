@php
    //This function will take the route name and return the access permission.
    if(!isset($routeName) || $routeName == '' || $routeName == null){
        $check = false;
    }else{
        $check = check_access_by_route_name($routeName);
    }
    //Parameters
    $parameterArray = isset($params) ? $params: [];
@endphp
@if ($check)
    <a href="{{ route($routeName,$parameterArray) }}" class="{{$className}}" role="button">{{ __($label) }}</a>
@endif


