<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('laraforum.pagename', 'Laraforum') }}
        @section('title')
            {{(config('laraforum.basic_title'))?' - '.config('laraforum.basic_title'):''}}
        @show
    </title>
    @section('meta')
        <meta name="description" content="{{config('laraforum.basic_description')}}">
    @show
<!-- Styles -->
    <link href="{{asset('forum/discuss/css/min.css')}}" rel="stylesheet">
    <link href="{{asset('forum/discuss/css/rainbow.css')}}" rel="stylesheet">
    <link href="{{asset('forum/discuss/css/font.css')}}" rel="stylesheet">
    <link href="{{asset('forum/discuss/css/font1.css')}}" rel="stylesheet">
    <!-- Scripts -->
    @yield('header')
</head>
@if($user && !$user->profile)
    {{(\FsFlex\LaraForum\Helpers\Helper::autoloadUsersProfile() && $user->load('profile'))?'':''}}
@endif
<body class="{{isset($user) ? ' signedIn ':''}}profile has-banner"
>
<div id="app">
    <div id="root" class="'page">
        <nav class="nav is-normal">
            <div class="container">
                <div class="nav-left dont-flex mr-2">
                    <a class="nav-item is-brand" href="{{route('discuss.index')}}">
                        <font size="5">{{title_case( config('laraforum.pagename'))}}</font>
                    </a>
                </div>
                <div class="nav-center flex mr-2">
                    {{Form::open([
                        'method'=>'get',
                        'url'=>route('discuss.index'),
                        'class'=>'nav-search-form is-flex'
                    ])}}
                    <svg class="is-hidden-mobile" viewBox="0 0 20 20" version="1.1"
                         xmlns="http://www.w3.org/2000/svg"
                         xmlns:xlink="http://www.w3.org/1999/xlink" style="width: 30px"><title>search</title>
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <g id="search-icon" fill="#cacaca">
                                <path d="M11.1921711,12.6063847 C10.0235906,13.4815965 8.5723351,14 7,14 C3.13400675,14 0,10.8659932 0,7 C0,3.13400675 3.13400675,0 7,0 C10.8659932,0 14,3.13400675 14,7 C14,8.5723351 13.4815965,10.0235906 12.6063847,11.1921711 L14.0162265,12.6020129 C14.6819842,12.4223519 15.4217116,12.5932845 15.9484049,13.1199778 L18.7795171,15.95109 C19.5598243,16.7313971 19.5646685,17.9916807 18.7781746,18.7781746 C17.997126,19.5592232 16.736965,19.5653921 15.95109,18.7795171 L13.1199778,15.9484049 C12.5960188,15.4244459 12.4217025,14.6840739 12.6018353,14.0160489 L11.1921711,12.6063847 Z M7,12 C9.76142375,12 12,9.76142375 12,7 C12,4.23857625 9.76142375,2 7,2 C4.23857625,2 2,4.23857625 2,7 C2,9.76142375 4.23857625,12 7,12 Z"></path>
                            </g>
                        </g>
                    </svg>
                    <span class="algolia-autocomplete"
                          style="position: relative; display: inline-block; direction: ltr;">
                            <input type="search"
                                   class="input aa-hint"
                                   readonly=""
                                   autocomplete="off"
                                   spellcheck="false"
                                   tabindex="-1"
                                   style="position: absolute; top: 0px; left: 0px; border-color: transparent; box-shadow: none; opacity: 1; background: none 0% 0% / auto repeat scroll padding-box border-box rgb(255, 255, 255);">
                            <span
                                    class="algolia-autocomplete"
                                    style="position: relative; display: inline-block; direction: ltr;"><input
                                        type="search"
                                        class="input aa-input aa-hint"
                                        autocomplete="off"
                                        spellcheck="false"
                                        dir="auto"
                                        style="vertical-align: top; position: absolute; top: 0px; left: 0px; border-color: transparent; box-shadow: none; opacity: 1; background: none 0px 0px / auto repeat scroll padding-box border-box transparent;"
                                        readonly=""
                                        tabindex="-1">
                                <input
                                        type="search" class="input aa-input" id="search" name="search" required=""
                                        placeholder="{{url()->current()== route('discuss.index')?
                                                "SEARCH THE FORUM":
                                                "WHAT WILL YOU LEARN NEXT?"
                                        }}" autocomplete="off" spellcheck="false" dir="auto"
                                        value='{{(isset($search)&&$search)? $search : ''}}'
                                        style="position: relative; vertical-align: top; background-color: transparent;">
                                <span
                                        class="aa-dropdown-menu"
                                        style="position: absolute; top: 100%; z-index: 100; display: none; left: 0px; right: auto;">
                                    <div class="aa-dataset-1"></div>
                                    <div class="aa-dataset-2"></div>
                                </span>
                            </span>
                            <span
                                    class="aa-dropdown-menu"
                                    style="position: absolute; top: 100%; z-index: 100; display: none; left: 0px; right: auto;">
                                <div
                                        class="aa-dataset-0">
                                </div>
                            </span>
                        </span>
                    <button type="submit" class="button is-primary in-caps is-hidden-mobile">Search</button>
                    {{Form::close()}}
                </div>
                <span class="nav-toggle">
    <span></span>
    <span></span>
    <span></span>
</span>
                @unless(isset($user))
                    <span class="nav-toggle">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                    <div class="nav-right nav-menu">
                        <a class="nav-item is-bold in-caps" href="{{route('forum.register')}}">
                            Sign Up
                        </a>

                        <a href="{{route('forum.login')}}" class="nav-item is-bold color-primary in-caps">
                            Sign In
                        </a>
                    </div>
                    @else
                        <div class="nav-right nav-menu">
                            <div class="nav-item">
                                <ul class="is-vertically-centered">
                                    <li>
                                        <button id="user-notifications-toggle" class="button is-naked">
                                            <svg id="icon-alarm" height="24" viewBox="0 0 24 24" width="24"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path d="M0 0h24v24H0V0z" fill="none"></path>
                                                <path d="M10.01 21.01c0 1.1.89 1.99 1.99 1.99s1.99-.89 1.99-1.99h-3.98zm8.87-4.19V11c0-3.25-2.25-5.97-5.29-6.69v-.72C13.59 2.71 12.88 2 12 2s-1.59.71-1.59 1.59v.72C7.37 5.03 5.12 7.75 5.12 11v5.82L3 18.94V20h18v-1.06l-2.12-2.12zM16 13.01h-3v3h-2v-3H8V11h3V8h2v3h3v2.01z"></path>
                                            </svg>
                                        </button>
                                    </li>

                                    <li>
                                        <div class="dropdown  w-100">
                                            <div class="is-vertically-centered"><span slot="heading-before">
                    <a href="{{route('profile.show',[$user->name])}}">
                        <img src="{{
                            ($avatar = $user->profile->avatar) ? $avatar : asset('forum/discuss/images/basic/generic-avatar.png')
                        }}"
                             class="is-circle is-outlined bg-white" style="background-color:white" alt="{{$user->name}}"
                             width="50">
</a>
                </span>
                                                <button class="button has-dropdown w-100"><span
                                                            class="dropdown-heading ml-1 in-caps has-arrow"
                                                            slot="heading">
                    <span>My {{title_case(config('laraforum.pagename')) }}</span>
                    <span class="arrow"></span>
                </span></button>
                                            </div>
                                            <div class="dropdown-menu" style="min-width: 250px">
                                                <ul slot="dropdown-links">

                                                    <li class="nav-item dropdown-item ">
                                                        <a href="{{route('discuss.index',['favourites'=>'1'])}}">
                                                            <svg class="icon is-16x16 o-4 mbr-1"
                                                                 xmlns="http://www.w3.org/2000/svg" width="16"
                                                                 height="16" viewBox="0 0 16 16">
                                                                <path d="M14 14c-.05.69-1.27 1-2 1H5.67L4 14V8c1.36 0 2.11-.75 3.13-1.88 1.23-1.36 1.14-2.56.88-4.13-.08-.5.5-1 1-1 .83 0 2 2.73 2 4l-.02 1.03c0 .69.33.97 1.02.97h2c.63 0 .98.36 1 1l-1 6L14 14zm0-8h-2.02l.02-.98C12 3.72 10.83 0 9 0c-.58 0-1.17.3-1.56.77-.36.41-.5.91-.42 1.41.25 1.48.28 2.28-.63 3.28-1 1.09-1.48 1.55-2.39 1.55H2C.94 7 0 7.94 0 9v4c0 1.06.94 2 2 2h1.72l1.44.86c.16.09.33.14.52.14h6.33c1.13 0 2.84-.5 3-1.88l.98-5.95c.02-.08.02-.14.02-.2-.03-1.17-.84-1.97-2-1.97H14z"></path>
                                                            </svg>

                                                            <span>Favorites</span>
                                                        </a>
                                                    </li>

                                                    <li class="nav-item dropdown-item ">
                                                        <a href="{{route('discuss.index',['me'=>'1'])}}">
                                                            <svg class="icon is-16x16 o-4 mbr-1"
                                                                 xmlns="http://www.w3.org/2000/svg" width="16"
                                                                 height="16" viewBox="0 0 16 16">
                                                                <path d="M15 1H6c-.55 0-1 .45-1 1v2H1c-.55 0-1 .45-1 1v6c0 .55.45 1 1 1h1v3l3-3h4c.55 0 1-.45 1-1V9h1l3 3V9h1c.55 0 1-.45 1-1V2c0-.55-.45-1-1-1zM9 11H4.5L3 12.5V11H1V5h4v3c0 .55.45 1 1 1h3v2zm6-3h-2v1.5L11.5 8H6V2h9v6z"></path>
                                                            </svg>

                                                            <span>Questions</span>
                                                        </a>
                                                    </li>

                                                    <li class="nav-item dropdown-item ">
                                                        <a href="{{route('profile.show',[$user->name])}}">
                                                            <svg class="icon is-16x16 o-4 mbr-1"
                                                                 xmlns="http://www.w3.org/2000/svg" width="12"
                                                                 height="16" viewBox="0 0 12 16">
                                                                <path d="M12 14.002a.998.998 0 0 1-.998.998H1.001A1 1 0 0 1 0 13.999V13c0-2.633 4-4 4-4s.229-.409 0-1c-.841-.62-.944-1.59-1-4 .173-2.413 1.867-3 3-3s2.827.586 3 3c-.056 2.41-.159 3.38-1 4-.229.59 0 1 0 1s4 1.367 4 4v1.002z"></path>
                                                            </svg>

                                                            <span>Profile</span>
                                                        </a>
                                                    </li>
                                                    <li class="nav-item dropdown-item">
                                                        <a href="{{route('logout')}}">
                                                            <svg class="icon is-16x16 o-4 mbr-1"
                                                                 xmlns="http://www.w3.org/2000/svg" width="16"
                                                                 height="16" viewBox="0 0 16 16">
                                                                <path d="M12 9V7H8V5h4V3l4 3-4 3zm-2 3H6V3L2 1h8v3h1V1c0-.55-.45-1-1-1H1C.45 0 0 .45 0 1v11.38c0 .39.22.73.55.91L6 16.01V13h4c.55 0 1-.45 1-1V8h-1v4z"></path>
                                                            </svg>
                                                            <span>Logout</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        @endunless
            </div>
        </nav>


        @yield('content')

    </div>
</div>
@yield('js_module')
<!-- Scripts -->
{{--<script src="{{resources('forum/js/app.js')}}"></script>--}}
</body>
</html>
