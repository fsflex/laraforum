@extends(\FsFlex\LaraForum\Helpers\Helper::loadView('layouts.app'))
@section('content')
    <section class="hero is-desktop is-primary is-bold pb-0-desktop">
        <div class="hero-body">
            <div class="container">
                <div class="columns">
                    <div class="column is-6-desktop is-8-tablet">
                        <div class="media">
                            <div class="media-left mb-1-mobile">
                                <a href="{{route('profile.show',[$userpreview->name])}}">
                                    <img src="{{
                                    $userpreview->profile->avatar ? $userpreview->profile->avatar : asset('forum/discuss/images/basic/generic-avatar.png')
                                    }}" class=" bg-white" style="background-color:white;" alt="{{$userpreview->name}}"
                                         width="100">
                                </a>
                            </div>

                            <div class="media-content has-text-centered-mobile">
                                <div class="is-vertically-centered is-block-mobile mb-1 is-justified-to-center-mobile">
                                    <h1 class="title is-3 is-bold mb-0 pbr-1">{{$userpreview->name}}</h1>
                                    @if($userpreview->profile->twitter_username)
                                        <a href="http://twitter.com/{{$userpreview->profile->twitter_username}}"
                                           target="_blank" class="twitter pbr-1">
                                            <span class="icon">
                                                <svg viewBox="0 0 2000 1625.36" version="1.1" class="icon o-4"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                  <path d="m 1999.9999,192.4 c -73.58,32.64 -152.67,54.69 -235.66,64.61 84.7,-50.78 149.77,-131.19 180.41,-227.01 -79.29,47.03 -167.1,81.17 -260.57,99.57 C 1609.3399,49.82 1502.6999,0 1384.6799,0 c -226.6,0 -410.328,183.71 -410.328,410.31 0,32.16 3.628,63.48 10.625,93.51 -341.016,-17.11 -643.368,-180.47 -845.739,-428.72 -35.324,60.6 -55.5583,131.09 -55.5583,206.29 0,142.36 72.4373,267.95 182.5433,341.53 -67.262,-2.13 -130.535,-20.59 -185.8519,-51.32 -0.039,1.71 -0.039,3.42 -0.039,5.16 0,198.803 141.441,364.635 329.145,402.342 -34.426,9.375 -70.676,14.395 -108.098,14.395 -26.441,0 -52.145,-2.578 -77.203,-7.364 52.215,163.008 203.75,281.649 383.304,284.946 -140.429,110.062 -317.351,175.66 -509.5972,175.66 -33.1211,0 -65.7851,-1.949 -97.8828,-5.738 181.586,116.4176 397.27,184.359 628.988,184.359 754.732,0 1167.462,-625.238 1167.462,-1167.47 0,-17.79 -0.41,-35.48 -1.2,-53.08 80.1799,-57.86 149.7399,-130.12 204.7499,-212.41"
                                                        style="fill:white"></path>
                                                </svg>
                                            </span>
                                        </a>
                                    @endif
                                    @if($userpreview->profile->github_username)
                                        <a href="http://github.com/{{$userpreview->profile->github_username}}"
                                           target="_blank" class="github">
                                            <span class="icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"
                                                     class="icon o-4"><path
                                                            d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0 0 16 8c0-4.42-3.58-8-8-8z"
                                                            fill="white"></path></svg>
                                            </span>
                                        </a>
                                    @endif
                                </div>
                                @if($userpreview->profile->job_title || $userpreview->profile->place_of_employment)
                                    <p class="is-bold in-caps">
                                        {{$userpreview->profile->job_title ? $userpreview->profile->job_title : 'Employer'}}
                                        {{$userpreview->profile->place_of_employment ? 'at '.$userpreview->profile->place_of_employment : ''}}
                                    </p>
                                @endif
                                @if($userpreview->profile->country->short_name != 'us')
                                    <p class="banner-profile-location">
                                        {{$userpreview->profile->country->name}}
                                        , {{strtoupper($userpreview->profile->country->short_name)}}

                                        <i class="flag flag-{{$userpreview->profile->country->short_name}}"></i>
                                    </p>
                                @endif
                            </div> <!-- end media-content -->
                        </div> <!-- end media -->
                    </div> <!-- end column -->

                    <div class="column
                            is-4-tablet is-offset-2-desktop
                            has-text-right has-text-centered-mobile
                            is-bold in-caps">
                        @if($userpreview->profile->is_available_for_hire)
                            <p class="hire-me mb-2-mobile">
                                <a href="{{$userpreview->profile->website}}" class="button is-success">
                                    Hire Me
                                </a>
                            </p>
                        @endif
                        {{--<span class="color-text-lightest">Experience</span>

                        <h4 class="experience-count is-white title is-1 mb-0 pbb-1">
                            1,028,560
                        </h4>--}}

                        <p class="in-caps is-bold">{{$userpreview->best_answer_count}} Best Reply Awards</p>
                    </div>
                </div> <!-- end columns -->
            </div>
        </div>
    </section>


    <div class="bar section is-small is-justified-to-center-mobile has-text-centered-mobile">
        <div class="container">
            <ul class="level">
                <li class="level-item is-narrow">
                    Member Since <strong>{{$userpreview->created_at->diffForHumans()}}</strong>

                <li class="level-item is-narrow">
                    <strong>{{$userpreview->favourites_count}}</strong>
                    Favorites
                </li>
                @if($user && $user->id == $userpreview->id)
                    <li class="level-item has-text-right has-text-centered-mobile">
                        <a href="{{route('profile.edit',[$userpreview->name])}}" id="editProfile"
                           class="button is-default is-primary">
                            Edit Profile
                        </a>
                    </li>
                @endif

            </ul>
        </div>
    </div>


    <div class="section is-normal">
        <div class="container">
            <div class="timeline">
                @if($contents->count()<1)
                    <p>No current activity.</p>
                @else
                    @foreach($contents as $content)
                        <div class="columns timeline-section">

                            <!-- The date for the Timeline group. -->
                            <div class="timeline-date column is-3">
                                <p class="body is-muted is-bold in-caps">
                                    {{\Carbon\Carbon::today()==$content['date']? 'today' :$content['date']->format('jS F\, Y')}}
                                </p>
                            </div>


                            <!-- The list of events for that date. -->
                            <div class="timeline-contents column is-9">
                                <!-- type, description, link, date -->
                                @foreach($content['items'] as $item)
                                    <div class="timeline-contents-item mb-4">
                                        <p class="color-text-lightest is-bold mb-1">
                                            <a href="{{route('profile.show',[$userpreview->name])}}">{{$userpreview->name}}</a>

                                            @if($item['type'] === 'post' )
                                                left a reply on
                                            @elseif($item['type'] === 'thread' )
                                                started a new conversation
                                            @endif
                                            <a href="{{$item['link']}}">
                                                {{$item['thread_title']}}
                                            </a>

                                            <span class="in-caps">• {{$item['created_at']->diffForHumans()}}</span>
                                        </p>

                                        <div class="content">
                                            <p>{{$item['body']}}</p>

                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        </div>

                    @endforeach

                @endif
            </div>

        </div>
    </div>

@endsection