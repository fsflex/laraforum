@extends('forum::discuss.layouts.app')
@section('content')
    <div class="section">
        <div class="container filterable">
            <div class="columns">
                @include('forum::discuss.layouts.nomal_leftbar')
                <div class="column is-9 primary">
                    <div class="box">
                        {{Form::open(['url'=>route('channel.store'),
                        'autocomplete'=>'off'])}}


                        <div class="control">
                            <label for="title" class="label">Provide a Channel name:</label>
                            {{Form::text('name',null,[
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
                                               style="background: {{$color['hexcode']}};{{($color['is_weighing']?'color:white':'')}}"> {{$color['name']}}</option>
                                   @endforeach
                               </select>
                            </span>


                            @if($errors->has('color'))
                                @foreach($errors->get('color') as $message)
                                    <span class="help is-danger">{{$message}}</span>
                                @endforeach
                            @endif

                        </div>


                        <div class="control">
                            <label for="managers" class="label">Channel Manager List:(list of username and use ; as separator)</label>

                            <textarea id="managers" class="textarea " name="managers" data-autosize=""
                                      placeholder="List username can remove any thread or post created in this channel"
                                      style="overflow: hidden; word-wrap: break-word; resize: none; height: 147.6px;"></textarea>
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

                        <div class="control is-flex">
                            <div class="control is-grouped w-100-mobile is-aligned-center-mobile">
                                <div class="control mb-1-mobile">
                                    <a href="{{route('discuss.index')}}" class="button is-muted is-default">
                                        Cancel
                                    </a>
                                </div>
                                <div class="control">
                                    {{Form::button('Create Channel',['class'=>'button is-primary is-outlined','type'=>'submit'])}}
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