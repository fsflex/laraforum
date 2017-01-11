@extends(\FsFlex\LaraForum\Helpers\Helper::loadView('layouts.app'))
@if(isset($header) && $header['title'])
    @section('title')
        - {{$header['title']}}
    @endsection
@endif
@if(isset($header) && $header['description'])
    @section('meta')
        <meta name="description" content="{{$header['description']}}">
    @endsection
@endif
@section('content')
    <div class="section">
        <div class="container filterable">
            <div class="columns">
                @include(\FsFlex\LaraForum\Helpers\Helper::loadView('layouts.nomal_leftbar'))
                <div class="column is-9 primary">
                    @if(($user) && ($user->channel_edit_role) )
                    <div class="level-right ">
                        <a href="{{route('channel.edit',[$user->channel_edit_role])}}" class="button is-default is-primary column is-4">
                            Edit channel
                        </a>
                    </div>
                    @endif



                    <h1 class="title">{{(isset($message)&&$message)?$message:''}}</h1>
                    @if($threads->count() === 0)
                        {{(!isset($message))?'There are no relevant forum threads at this time.':''}}
                    @else
                        <div class="conversation-list">
                        @foreach($threads as $thread)
                            <!-- Begin thread -->
                                <div class="conversation-list-item ">
                                    <!-- The Creator's Avatar -->
                                    <div class="conversation-list-avatar is-hidden-mobile">
                                        <div>
                                            @if($thread->best_answer_id)
                                                <span class="icon is-answered">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon is-32x32" viewBox="0 0 12 16">
                                    <path d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5z" fill="white"></path>
                                </svg>

                                <a href="{{route('profile.show',[$thread->user->name])}}">
                                <img src="{{$thread->user->profile->avatar  ? $thread->user->profile->avatar :  asset('forum/discuss/images/basic/generic-avatar.png')}}"
                                     class="is-circle is-outlined bg-white" alt="sunrise" width="75">
                                </a>
                                </span>
                                            @else
                                                <a href="{{route('profile.show',[$thread->user->name])}}">
                                                    <img src="{{$thread->user->profile->avatar  ? $thread->user->profile->avatar :  asset('forum/discuss/images/basic/generic-avatar.png')}}"
                                                         class="is-circle is-outlined bg-white" alt="sunrise"
                                                         width="75">
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    <!-- The Conversation Title -->
                                    <div class="conversation-list-title">
                                        <h4 class="title is-5">
                                            <a href="{{route('discuss.show',[$thread->channel->name,$thread->slug])}}">
                                                @if($thread->title_with_highlight)
                                                    {!! $thread->title_with_highlight !!}
                                                @else
                                                    {{$thread->title}}
                                                @endif
                                            </a>
                                        </h4>

                                        <div class="meta in-caps mb-1">
                            <span>
                                <a href="{{route('discuss.index',['channel'=>$thread->channel->name])}}"
                                   style="color: {{$thread->channel->color}}"
                                   class="is-link">{{strtolower($thread->channel->name)}}</a> •
                                @if(!$thread->last_post_id)
                                    {{
                                     \Carbon\Carbon::instance($thread->created_at)->diffForHumans()
                                     }}
                            </span>
                                            by <a href="{{route('profile.show',[$thread->user->name])}}">
                                                {{$thread->user->name }}</a>
                                            @else
                                            {{\Carbon\Carbon::instance($thread->last_post->created_at)->diffForHumans()}}
                                            </span>
                                            by <a href="{{route('profile.show',[$thread->last_post->user->name])}}">
                                                {{$thread->last_post->user->name }}</a>
                                            @endif
                                            @if(is_a($thread->author_updated_at,\Carbon\Carbon::class))
                                                • <strong class="color-success">Updated</strong>
                                            @endif
                                        </div>
                                        <div class="content">
                                            @if($thread->body_with_highlight)
                                                {!! $thread->body_with_highlight !!}
                                            @else
                                                {{$thread->body}}
                                            @endif
                                        </div>
                                    </div>
                                    <!-- The Reply Count -->
                                    <div class="conversation-list-reply-count is-hidden-mobile">
                                        {{ $thread->posts_count }}
                                    </div>
                                </div>
                                <!-- End thread -->
                            @endforeach
                        </div>
                        {{$threads->links}}
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop