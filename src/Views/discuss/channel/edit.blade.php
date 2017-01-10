@extends('forum::discuss.layouts.app')
@section('content')
    <div class="section">
        <div class="container filterable">
            <div class="columns">
                @include('forum::discuss.layouts.nomal_leftbar')
                <div class="column is-9 primary">
                    @if(session()->has('message.channel_updated'))
                        <section class="hero is-blue centered-heading">
                            <h1 class="title is-1 is-light">{{title_case(session()->pull('message.channel_updated')) }}</h1>
                        </section>
                    @endif
                    <div class="box">
                        {{Form::open(['url'=>route('channel.update',[$channel_editing->id]),
                        'autocomplete'=>'off'])}}


                        <div class="control">
                            <label for="title" class="label">Provide a Channel name:</label>
                            {{Form::text('name',$channel_editing->name,[
                            'class'=>'input',
                            'required'
                            ])}}
                            @if($errors->has('name'))
                                @foreach($errors->get('name') as $message)
                                    <span class="help is-danger">{{$message}}</span>
                                @endforeach
                            @endif
                        </div>


                        <div class="control">
                            <label for="channel" class="label">Pick a Color:</label>
                            <span class="select">
                               <select id="color" name="color">
                                    @foreach($colors as $color)
                                       <option value="{{$color['hexcode']}}"
                                               style="background: {{$color['hexcode']}};{{($color['is_weighing']?'color:white':'')}}" {{($color['hexcode']===$channel_editing->color)? 'selected' : ''}}> {{$color['name']}}</option>
                                   @endforeach
                               </select>
                            </span>


                            @if($errors->has('color'))
                                @foreach($errors->get('color') as $message)
                                    <span class="help is-danger">{{$message}}</span>
                                @endforeach
                            @endif

                        </div>

                        @if($user->name === config('laraforum.admin_username'))
                            <div class="control">
                                <label for="managers" class="label">Channel Manager List:(list of username and use ; as
                                    separator)</label>

                                <textarea id="managers" class="textarea " name="managers" data-autosize=""
                                          placeholder="List username can remove any thread or post created in this channel"
                                          style="overflow: hidden; word-wrap: break-word; resize: none; height: 147.6px;">{{$channel_editing->managers_list}}</textarea>
                                @if($errors->has('managers'))
                                    @foreach($errors->get('managers') as $message)
                                        <span class="help is-danger">{{$message}}</span>
                                    @endforeach
                                @endif
                                @if($errors->has('msg'))
                                    @foreach($errors->get('msg') as $message)
                                        <span class="help is-danger">{{$message}}</span>
                                    @endforeach
                                @endif
                            </div>
                        @endif
                        <div class="control is-flex ">
                            <div class="control is-grouped w-100-mobile is-aligned-center-mobile">
                                <div class="control mb-1-mobile">
                                    <a href="{{route('discuss.index')}}" class="button is-muted is-default">
                                        Cancel
                                    </a>
                                </div>
                                <div class="control">
                                    {{Form::button('Update Channel',['class'=>'button is-primary is-outlined','type'=>'submit'])}}
                                </div>
                                @if($user->name === config('laraforum.admin_username'))
                                    <div class="control ">
                                       <a class="button is-default is-danger" href="{{route('channel.remove',[$channel_editing->id])}}">Remove Channel</a>
                                    </div>
                                @endif
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