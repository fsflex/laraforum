@extends('forum::discuss.layouts.app')
@section('content')
    @if(Request::has('title'))
        <section class="hero is-blue">
            <div class="hero-body">
                <div class="container">
                    <div class="has-text-centered w-80 ma">
                        <h1 class="title is-1 is-light">{{Request::input('title')}}</h1>
                    </div>
                </div>
            </div>
        </section>
    @endif
    <div class="section is-normal">
        <div class="container">
            {{Form::open([
                'url'=>route('forum.register.store'),
                'class'=>"signup-form columns is-multiline",
            ])}}
            {{ csrf_field() }}
            <section class="column is-6">
                <fieldset class="box h-100">
                    <h3 class="heading is-5" data-step="two">
                        Choose Your Credentials
                    </h3>

                    <!-- Username -->
                    <div class="control">
                        {{Form::text('name','',[
                            'required',
                            'class'=>'input',
                            'placeholder'=>'Username'
                        ])}}
                        @if($errors->has('name'))
                            @foreach($errors->get('name') as $message)
                                <span class="help is-danger">
                                            * {{$message}}
                                        </span>
                            @endforeach
                        @endif

                    </div>

                    <!-- Email Address -->
                    <div class="control">
                        {{Form::email('email','',[
                            'required',
                            'class'=>'input',
                            'placeholder'=>'Email Address'
                        ])}}
                        @if($errors->has('email'))
                            @foreach($errors->get('email') as $message)
                                <span class="help is-danger">
                                            * {{$message}}
                                        </span>
                            @endforeach
                        @endif

                    </div>

                    <!-- Password -->
                    <div class="control">
                        {{Form::password('password',[
                            'required',
                            'class'=>'input',
                            'placeholder'=>'Desired Password',
                        ])}}
                    </div>

                    <!-- Repeat Password -->
                    <div class="control">
                        {{Form::password('password_confirmation',[
                            'required',
                            'class'=>'input',
                            'placeholder'=>'Repeat Password'
                        ])}}
                        @if($errors->has('password'))
                            @foreach($errors->get('password') as $message)
                                <span class="help is-danger">
                                            * {{$message}}
                                        </span>
                            @endforeach
                        @endif
                    </div>

                    <div class="control">
                        {{Form::button('Create Account!',[
                            'class'=>'button is-primary is-submit ',
                            'type'=>'submit'
                        ])}}
                    </div>

                </fieldset>
            </section>
            {{Form::close()}}
        </div>
    </div>
@endsection
