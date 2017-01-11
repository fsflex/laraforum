@extends(\FsFlex\LaraForum\Helpers\Helper::loadView('layouts.app'))
@section('content')
    <div class="section">
        <div class="container filterable">
            <div class="columns">
                @include(\FsFlex\LaraForum\Helpers\Helper::loadView('layouts.nomal_leftbar'))
                <div class="column is-9 primary">

                    <div class="box">
                        {{Form::open(['url'=>route('channel.destroy',[$channel_removing->id]),
                        'autocomplete'=>'off'])}}


                        <div class="control">
                            <label for="title" class="label">Channel name:</label>
                            {{Form::text('name',$channel_removing->name,[
                            'class'=>'input',
                            'required','readonly'
                            ])}}

                        </div>
                        <div class="control">
                            <label for="title" class="label ">Please chosen action for all discuss in this channel:</label>
                            <div class="control w-100"> <input type="radio" name="action" value="remove">Remove all !</div>
                            @if($channels_to_keep->count()>0)
                                <div class="control w-100">
                                    <input type="radio" name="action" value="keep">Keep all to channel
                                    <span class="select">
                                   <select  name="keep_channel">
                                        @foreach($channels_to_keep as $channel)
                                            <option value="{{$channel->id}}">{{title_case($channel->name) }}</option>
                                        @endforeach
                                   </select>
                                </span>
                                </div>
                            @endif
                        </div>
                        @if($errors->has('action'))
                            @foreach($errors->get('action') as $message)
                                <span class="help is-danger">
                                            * {{$message}}
                                        </span>
                            @endforeach
                        @endif
                        <div class="control">
                            <label for="title" class="label">Please submit <span class="color-red"> REMOVE</span> : </label>
                            {{Form::text('accepted',null,[
                            'class'=>'input',
                            'required'
                            ])}}

                        </div>
                        @if($errors->has('accepted'))
                            @foreach($errors->get('accepted') as $message)
                                <span class="help is-danger">
                                            * {{$message}}
                                        </span>
                            @endforeach
                        @endif

                        <div class="control is-flex ">
                            <div class="control is-grouped w-100-mobile is-aligned-center-mobile">
                                <div class="control mb-1-mobile">
                                    <a href="{{route('discuss.index')}}" class="button is-muted is-default">
                                        Cancel
                                    </a>
                                </div>
                                <div class="control">
                                    {{Form::button('Remove Channel',['class'=>'button is-danger is-outlined','type'=>'submit'])}}
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


















