@extends('forum::discuss.layouts.app')
@if(isset($header))
@section('title')
    {{$header['title']}}
@endsection
@section('meta')
    <meta name="description" content="{{$header['description']}}">
@endsection
@endif
@section('content')
    <div class="section">
        <div class="container filterable">
            <div class="columns">
                @include('forum::discuss.layouts.nomal_leftbar')
                <div class="column is-9 primary">
                    <h1 class="title is-normal is-3 mbb-1">
                        {{$thread->title}}
                    </h1>

                    <p class="fs-smaller mb-1-mobile">
                        <strong class="color-text-lightest in-caps">
            <span class="is-hidden-mobile">
                {{\Carbon\Carbon::instance($thread->created_at)->diffForHumans()}}
            </span>
                            by <a href="{{route('profile.show',[$thread->user->name])}}">
                                {{$thread->user->name}}
                            </a>
                        </strong>
                    </p>
                    @if($user)
                        <div class="level is-narrow">
                    <span class="level-item mr-0">
                    @if($user->id === $thread->user->id)
                            <a href="{{route('discuss.edit',[$thread->id])}}" class="has-icon"
                               title="Edit Your Discussion">
                                <svg class="icon is-toggle" xmlns="http://www.w3.org/2000/svg" width="14" height="16"
                                     viewBox="0 0 14 16"><path
                                            d="M0 12v3h3l8-8-3-3-8 8zm3 2H1v-2h1v1h1v1zm10.3-9.3L12 6 9 3l1.3-1.3a.996.996 0 0 1 1.41 0l1.59 1.59c.39.39.39 1.02 0 1.41z"></path></svg>
                            </a>
                        @endif
                    </span>

                            {{Form::open([
                                'url'=>route(!($thread->is_notified)?'reach.store':'reach.destroy',['threads',$thread->id,'notify']),
                                'class'=> "level-item mr-0" ,
                            ])}}
                            {{ ($thread->is_notified) ? method_field('DELETE') : '' }}
                            <button type="submit" class="button is-naked p-0 has-icon"
                                    title="Want an email each time this thread receives a new reply?">
                                <svg class="icon is-toggle {{($thread->is_notified) ? 'is-active' : ''}}"
                                     xmlns="http://www.w3.org/2000/svg" width="14" height="16"
                                     viewBox="0 0 14 16">
                                    <path d="M0 4v8c0 .55.45 1 1 1h12c.55 0 1-.45 1-1V4c0-.55-.45-1-1-1H1c-.55 0-1 .45-1 1zm13 0L7 9 1 4h12zM1 5.5l4 3-4 3v-6zM2 12l3.5-3L7 10.5 8.5 9l3.5 3H2zm11-.5l-4-3 4-3v6z"></path>
                                </svg>
                            </button>
                            {{Form::close()}}
                            {{Form::open([
                                'url'=>route(!($thread->is_favourited)?'reach.store':'reach.destroy',['threads',$thread->id,'favourite']),
                                'class'=> "level-item mr-0" ,
                            ])}}
                            {{ ($thread->is_favourited) ? method_field('DELETE') : '' }}

                            <button id="js-favorite-thread" class="button is-naked p-0 has-icon" type="submit"
                                    title="Want to favorite this conversation?">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="16" viewBox="0 0 14 16"
                                     class="icon is-toggle {{($thread->is_favourited) ? 'is-active' : ''}}">
                                    <path d="M14 6l-4.9-.64L7 1 4.9 5.36 0 6l3.6 3.26L2.67 14 7 11.67 11.33 14l-.93-4.74z"></path>
                                </svg>
                            </button>
                            {{Form::close()}}
                            @if($thread->user->id != $user->id)
                                {{Form::open([
                                    'url'=>route(!($thread->is_disliked)?'reach.store':'reach.destroy',['threads',$thread->id,'dislike']),
                                    'class'=> "report-spam-form undefined" ,
                                ])}}
                                {{ ($thread->is_disliked) ? method_field('DELETE') : '' }}
                                <button type="submit" class="button is-naked p-0 has-icon"
                                        title="Is this conversation full of spam? Sheesh - people, right?">
                                    <img src="{{asset('forum/discuss/images/icons/frown.svg')}}" alt="Report Spam Icon">

                                </button>
                                {{Form::close()}}
                            @endif
                            @if($thread->manager_role)
                                {{Form::open([
                                    'url'=>route('discuss.manager.destroy',[$thread->id]),
                                    'class'=> "report-spam-form undefined" ,
                                ])}}
                                {{method_field('DELETE')}}
                                <button type="submit" class="button is-naked"
                                        title="Want to delete this discuss ?">
                                    <img src="{{asset('forum/discuss/images/icons/trash.svg')}}"
                                         alt="Delete Reply Icon">
                                </button>
                                {{Form::close()}}
                            @endif
                        </div>
                    @endif
                    <div class="columns">
                        <div class="column">
                            <div class="forum-question ">
                                <div class="content user-content">
                                    {{$thread->body}}
                                </div>
                                @if($thread->answer)
                                    <div class="mt-2 is-answer wow fadeIn" data-wow-delay=".3s"
                                         style="visibility: visible; animation-delay: 0.3s; animation-name: fadeIn;">
                                        <div class="best-answer-panel">
                                            <h5 class="title is-5 is-vertically-centered mb-0">
                                                <span class="flex">Best Answer</span>

                                                <small class="is-hidden-mobile">
                                                    (As Selected By {{$thread->user->name}})
                                                </small>
                                            </h5>

                                            <div class="best-answer-reply-wrap box is-rounded">
                                                <div class="reply " data-reply="">
                                                    <div class="media">
                                                        <figure class="reply-avatar media-left pr-1">
                                            <span class="image is-75x75 mb-1">
                                                <a href="{{route('profile.show',[$thread->answer->user->name])}}">
                                                    <img src="{{
                                                    ($avatar = $thread->answer->user->profile->avatar) ? $avatar : asset('forum/discuss/images/basic/generic-avatar.png')
                                                    }}" class="is-circle is-outlined bg-white" alt="tomi" width="75">
                                                </a>
                                            </span>

                                                            <div class="level is-justified-to-center">
                                                                <!-- The Like Button -->
                                                                @if($user)
                                                                    @if($user->id == $thread->answer->id)
                                                                        <button class="button is-naked  has-icon p-0"
                                                                                title="Like this status.">
                                                                            <svg class="icon is-28x28 is-toggle"
                                                                                 xmlns="http://www.w3.org/2000/svg"
                                                                                 width="16"
                                                                                 height="16" viewBox="0 0 16 16">
                                                                                <path d="M14 14c-.05.69-1.27 1-2 1H5.67L4 14V8c1.36 0 2.11-.75 3.13-1.88 1.23-1.36 1.14-2.56.88-4.13-.08-.5.5-1 1-1 .83 0 2 2.73 2 4l-.02 1.03c0 .69.33.97 1.02.97h2c.63 0 .98.36 1 1l-1 6L14 14zm0-8h-2.02l.02-.98C12 3.72 10.83 0 9 0c-.58 0-1.17.3-1.56.77-.36.41-.5.91-.42 1.41.25 1.48.28 2.28-.63 3.28-1 1.09-1.48 1.55-2.39 1.55H2C.94 7 0 7.94 0 9v4c0 1.06.94 2 2 2h1.72l1.44.86c.16.09.33.14.52.14h6.33c1.13 0 2.84-.5 3-1.88l.98-5.95c.02-.08.02-.14.02-.2-.03-1.17-.84-1.97-2-1.97H14z"></path>
                                                                            </svg>
                                                                            <span class="likes-count is-circle">{{
                                                            ($count = $thread->answer->likes_count) > 0 ? $count : ''
                                                            }}</span>
                                                                        </button>
                                                                    @else
                                                                        {{Form::open([
                                                                            'url'=>route(!($thread->answer->is_liked)?'reach.store':'reach.destroy',['posts',$thread->answer->id,'like']),
                                                                            'class'=> "is-vertically-centered" ,
                                                                        ])}}
                                                                        {{ ($thread->answer->is_liked) ? method_field('DELETE') : '' }}
                                                                        <button type="submit"
                                                                                class="button is-naked  has-icon p-0"
                                                                                title="Like this status.">
                                                                            <svg class="icon is-28x28 is-toggle {{
                                                                ($thread->answer->is_liked) ? 'is-active' : ''
                                                                    }}" xmlns="http://www.w3.org/2000/svg" width="16"
                                                                                 height="16" viewBox="0 0 16 16">
                                                                                <path d="M14 14c-.05.69-1.27 1-2 1H5.67L4 14V8c1.36 0 2.11-.75 3.13-1.88 1.23-1.36 1.14-2.56.88-4.13-.08-.5.5-1 1-1 .83 0 2 2.73 2 4l-.02 1.03c0 .69.33.97 1.02.97h2c.63 0 .98.36 1 1l-1 6L14 14zm0-8h-2.02l.02-.98C12 3.72 10.83 0 9 0c-.58 0-1.17.3-1.56.77-.36.41-.5.91-.42 1.41.25 1.48.28 2.28-.63 3.28-1 1.09-1.48 1.55-2.39 1.55H2C.94 7 0 7.94 0 9v4c0 1.06.94 2 2 2h1.72l1.44.86c.16.09.33.14.52.14h6.33c1.13 0 2.84-.5 3-1.88l.98-5.95c.02-.08.02-.14.02-.2-.03-1.17-.84-1.97-2-1.97H14z"></path>
                                                                            </svg>
                                                                            <span class="likes-count is-circle">{{
                                                            ($count = $thread->answer->likes_count) > 0 ? $count : ''
                                                            }}</span>
                                                                        </button>
                                                                    {{Form::close()}}
                                                                @endif
                                                            @endif
                                                            <!-- "Did This Answer Your Question?" Button -->
                                                                <span>
                                                    <span class="button is-naked is-checkmark is-active"
                                                          title="This reply has been marked as the correct answer for the thread.">
                                                        <svg class="icon " xmlns="http://www.w3.org/2000/svg" width="12"
                                                             height="16"
                                                             viewBox="0 0 12 16"><path
                                                                    d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5z"></path></svg>

                                                    </span>
                                                </span></div>
                                                        </figure>

                                                        <div class="media-content" style="min-width: 0">
                                                            <div class="is-flex mb-1">
                                                                <h5 class="pbr-1 is-bold">

                                                                    <a href="{{route('profile.show',[$thread->answer->user->name])}}">
                                                                        {{$thread->answer->user->name}}
                                                                    </a>
                                                                </h5>

                                                                <p>
                                                                    {{\Carbon\Carbon::instance($thread->answer->created_at)->diffForHumans()}}
                                                                </p>

                                                            </div>
                                                            <!-- The Formatted Body Text -->
                                                            <div class="content user-content" data-reply-body="">
                                                                <div>
                                                                    {{$thread->answer->body}}
                                                                </div>
                                                            </div>
                                                            <!-- The Editable Body in Markdown -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="wow fadeIn" data-wow-delay=".3s"
                                 style="visibility: visible; animation-delay: 0.3s; animation-name: fadeIn;">
                                @foreach($posts as $post)


                                    <div class="reply " data-reply="">
                                        <div class="media">
                                            <figure class="reply-avatar media-left pr-1">
                                <span class="image is-75x75 mb-1">
                                    <a href="{{route('profile.show',[$post->user->name])}}">
                                        <img src="{{
                                        ($avatar = $post->user->profile->avatar) ? $avatar : asset('forum/discuss/images/basic/generic-avatar.png')
                                        }}" class="is-circle is-outlined bg-white" alt="{{$post->user->name}}"
                                             width="75">
                                    </a>
                                </span>

                                                <div class="level is-justified-to-center">
                                                    <!-- The Like Button -->
                                                    @if($user)
                                                        @if($user->id == $post->user->id)
                                                            <button class="button is-naked  has-icon p-0"
                                                                    title="Like this status.">
                                                                <svg class="icon is-28x28 is-toggle"
                                                                     xmlns="http://www.w3.org/2000/svg" width="16"
                                                                     height="16" viewBox="0 0 16 16">
                                                                    <path d="M14 14c-.05.69-1.27 1-2 1H5.67L4 14V8c1.36 0 2.11-.75 3.13-1.88 1.23-1.36 1.14-2.56.88-4.13-.08-.5.5-1 1-1 .83 0 2 2.73 2 4l-.02 1.03c0 .69.33.97 1.02.97h2c.63 0 .98.36 1 1l-1 6L14 14zm0-8h-2.02l.02-.98C12 3.72 10.83 0 9 0c-.58 0-1.17.3-1.56.77-.36.41-.5.91-.42 1.41.25 1.48.28 2.28-.63 3.28-1 1.09-1.48 1.55-2.39 1.55H2C.94 7 0 7.94 0 9v4c0 1.06.94 2 2 2h1.72l1.44.86c.16.09.33.14.52.14h6.33c1.13 0 2.84-.5 3-1.88l.98-5.95c.02-.08.02-.14.02-.2-.03-1.17-.84-1.97-2-1.97H14z"></path>
                                                                </svg>
                                                                <span class="likes-count is-circle">{{
                                                            ($count = $post->likes_count) > 0 ? $count : ''
                                                            }}</span>
                                                            </button>
                                                        @else
                                                            {{Form::open([
                                                                'url'=>route(!($post->is_liked)?'reach.store':'reach.destroy',['posts',$post->id,'like']),
                                                                'class'=> "is-vertically-centered" ,
                                                            ])}}
                                                            {{ ($post->is_liked) ? method_field('DELETE') : '' }}
                                                            <button type="submit" class="button is-naked  has-icon p-0"
                                                                    title="Like this status.">
                                                                <svg class="icon is-28x28 is-toggle {{
                                                        ($post->is_liked) ? 'is-active' : ''
                                                            }}" xmlns="http://www.w3.org/2000/svg" width="16"
                                                                     height="16" viewBox="0 0 16 16">
                                                                    <path d="M14 14c-.05.69-1.27 1-2 1H5.67L4 14V8c1.36 0 2.11-.75 3.13-1.88 1.23-1.36 1.14-2.56.88-4.13-.08-.5.5-1 1-1 .83 0 2 2.73 2 4l-.02 1.03c0 .69.33.97 1.02.97h2c.63 0 .98.36 1 1l-1 6L14 14zm0-8h-2.02l.02-.98C12 3.72 10.83 0 9 0c-.58 0-1.17.3-1.56.77-.36.41-.5.91-.42 1.41.25 1.48.28 2.28-.63 3.28-1 1.09-1.48 1.55-2.39 1.55H2C.94 7 0 7.94 0 9v4c0 1.06.94 2 2 2h1.72l1.44.86c.16.09.33.14.52.14h6.33c1.13 0 2.84-.5 3-1.88l.98-5.95c.02-.08.02-.14.02-.2-.03-1.17-.84-1.97-2-1.97H14z"></path>
                                                                </svg>
                                                                <span class="likes-count is-circle">{{
                                                    ($count = $post->likes_count) > 0 ? $count : ''
                                                    }}</span>
                                                            </button>
                                                            {{Form::close()}}
                                                        @endif
                                                        @if($user->id == $thread->user->id && $thread->best_answer_id != $post->id)
                                                        <!-- "Did This Answer Your Question?" Button -->
                                                            {{Form::open([
                                                                'url'=>route('discuss.set_best',[$thread->id,$post->id]),
                                                                'class'=>"lh-1"
                                                            ])}}

                                                            <button title="Did this answer your question?"
                                                                    class="button is-naked is-checkmark" type="submit"
                                                                    data-title="Answered your question?">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon "
                                                                     viewBox="0 0 12 16" width="12" height="16">
                                                                    <path d="M 12 5 l -8 8 l -4 -4 l 1.5 -1.5 L 4 10 l 6.5 -6.5 Z"/>
                                                                </svg>
                                                            </button>
                                                            {{Form::close()}}
                                                        @else
                                                            @if($thread->answer && $thread->answer->id == $post->id)
                                                                <span>
                                                <span class="button is-naked is-checkmark is-active"
                                                      title="This reply has been marked as the correct answer for the thread.">
                                                    <svg class="icon " xmlns="http://www.w3.org/2000/svg" width="12"
                                                         height="16"
                                                         viewBox="0 0 12 16"><path
                                                                d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5z"></path></svg>

                                                </span>
                                            </span>
                                                            @endif
                                                        @endif
                                                    @endif
                                                </div>
                                            </figure>

                                            <div class="media-content" style="min-width: 0">
                                                <div class="is-flex mb-1">
                                                    <h5 class="pbr-1 is-bold">

                                                        <a href="{{route('profile.show',[$post->user->name])}}">{{$post->user->name}}</a>
                                                    </h5>
                                                    <p>
                                                        {{ \Carbon\Carbon::instance($post->created_at)->diffForHumans()}}
                                                    </p>
                                                </div>
                                            @if(old('post_editing_id') && old('post_editing_id') == $post->id)
                                                <!-- The Editable Body in Markdown -->
                                                    <div class="reply-markdown-body">
                                                        {{Form::open([
                                                            'url'=>route('post.update',[$post->id]),
                                                        ])}}
                                                        {{method_field('PUT')}}
                                                        {{Form::hidden('id',$post->id)}}
                                                        <div class="control">
                                            <textarea name="body" placeholder="Ask Away" class="textarea"
                                                      required=""
                                                      style="overflow: hidden; word-wrap: break-word; resize: none; height: 119.6px;">{{
                                                 $post->body
                                            }}</textarea>
                                                        </div>

                                                        <div>
                                                            <button type="button"
                                                                    class="button is-muted mbr-1 is-outlined mb-1-mobile">
                                                                Cancel
                                                            </button>

                                                            <button type="submit" class="button is-primary is-outlined">
                                                                Update Your Reply
                                                            </button>
                                                        </div>
                                                        {{Form::close()}}
                                                    </div>
                                            @else
                                                <!-- The Formatted Body Text -->
                                                    <div class="content user-content" data-reply-body="">
                                                        <div>
                                                            {{$post->body}}
                                                        </div>
                                                    </div>
                                                @endif
                                                @if($user && $user->id == $post->user_id )
                                                    <footer class="is-vertically-centered reply-edit-links">
                                                        <!-- Edit Reply Button -->
                                                        {{Form::open([
                                                            'url'=>route('post.edit',[$post->id])
                                                        ])}}
                                                        <button type="submit" class="edit-reply-button button is-naked"
                                                                title="Edit your reply.">
                                                            <img src="{{asset('forum/discuss/images/icons/edit.svg')}}"
                                                                 alt="Edit Icon">
                                                        </button>
                                                        {{Form::close()}}

                                                    <!-- The Delete Reply Button -->
                                                        {{Form::open([
                                                            'url'=>route('post.destroy',[$post->id])
                                                        ])}}
                                                        {{method_field('DELETE')}}
                                                        <button type="submit" class="button is-naked"
                                                                title="Want to delete this reply?">
                                                            <img src="{{asset('forum/discuss/images/icons/trash.svg')}}"
                                                                 alt="Delete Reply Icon">
                                                        </button>
                                                        {{Form::close()}}

                                                    </footer>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                @if($posts->total() !=$posts->count() )
                                    {{$posts->links()}}
                                @endif
                            </div>
                            @if(Auth::check())
                                <div class="media" style="margin-top: 2.5em">
                                    <figure class="media-left mr-2 is-hidden-mobile">
            <span class="image is-75x75">
                <a href="{{route('profile.show',[$user->name])}}">

    <img src="{{
     ($avatar = $user->profile()->first()->avatar) ? $avatar : asset('forum/discuss/images/basic/generic-avatar.png')
    }}"
         class="is-circle is-outlined bg-white" alt="{{$user->name}}" width="75">
</a>
            </span>
                                    </figure>

                                    <div class="media-content">
                                        {{Form::open([
                                            'url'=>route('post.store',[$thread->id]),
                                        ])}}
                                        <div class="control">
                            <textarea name="body" id="body" class="textarea mb-1"
                                      style="min-height: 200px; overflow: hidden; word-wrap: break-word; resize: none; height: 199.6px;"
                                      data-autosize="" required=""
                                      placeholder="You all will listen to what I have to say..."></textarea>
                                            @if($errors->has('msg'))
                                                @foreach($errors->get('msg') as $message)
                                                    <span class="help is-danger">{{$message}}</span>
                                                @endforeach
                                            @endif
                                        </div>


                                        <div class="is-flex-tablet">

                                            <button type="submit" class="button is-primary is-outlined is-submit-mobile"
                                                    data-single-click="">
                                                Post Your Reply
                                            </button>
                                        </div>
                                        {{Form::close()}}
                                    </div>
                                </div>
                            @else
                                <p class="has-text-centered mt-3">

                                    <a href="{{route('forum.login')}}">
                                        Sign In
                                    </a> or
                                    <a href="{{route('forum.register',['title'=>"Okay, we'll have you posting on the forum in no time!"])}}">create
                                        a forum account to participate in this discussion.</a>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop