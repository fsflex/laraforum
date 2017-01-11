@extends(\FsFlex\LaraForum\Helpers\Helper::loadView('layouts.app'))
@section('content')
    <div class="section">
        <div class="container filterable">
            <div class="columns">
                @include(\FsFlex\LaraForum\Helpers\Helper::loadView('layouts.nomal_leftbar'))
                <div class="column is-9 primary">
                    <div class="box">
                        {{Form::open(['url'=>route('discuss.store'),
                                        'autocomplete'=>'off'])}}
                        <div class="control">
                            <label for="channel" class="label">Pick a Channel:</label>
                            <span class="select">
        {{Form::select('channel',$channels_select,$channel_selected)}}
    </span>


                            @if($errors->has('channel'))
                                @foreach($errors->get('channel') as $message)
                                    <span class="help is-danger">{{$message}}</span>
                                @endforeach
                            @endif

                        </div>

                        <div class="control">
                            <label for="title" class="label">Provide a Title:</label>
                            {{Form::text('title',null,[
                                'class'=>'input',
                                'required'
                            ])}}
                            @if($errors->has('title'))
                                @foreach($errors->get('title') as $message)
                                    <span class="help is-danger">{{$message}}</span>
                                @endforeach
                            @endif
                        </div>

                        <div class="control">
                            <label for="body" class="label">Ask Away:</label>

                            <textarea id="body" class="textarea " name="body" data-autosize="" required=""
                                      placeholder="What do you need help with? Be specific, so that your peers are better able to assist you."
                                      style="overflow: hidden; word-wrap: break-word; resize: none; height: 147.6px;"></textarea>
                            @if($errors->has('body'))
                                @foreach($errors->get('body') as $message)
                                    <span class="help is-danger">{{$message}}</span>
                                @endforeach
                            @endif
                            @if($errors->has('msg'))
                                @foreach($errors->get('msg') as $message)
                                    <span class="help is-danger">{{$message}}</span>
                                @endforeach
                            @endif
                        </div>

                        <div class="control is-flex">
                            <div class="control is-grouped w-100-mobile is-aligned-center-mobile">
                                <div class="control mb-1-mobile">
                                    <a href="{{route('discuss.index')}}" class="button is-muted is-default">
                                        Cancel
                                    </a>
                                </div>
                                <div class="control">
                                    {{Form::button('Publish Discussion',['class'=>'button is-primary is-outlined','type'=>'submit'])}}
                                </div>
                            </div>
                        </div>
                        <!-- Form Errors -->
                        {{Form::close()}}
                    </div>
                </div>

            </div>
        </div>
    </div>
@stop