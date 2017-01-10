@extends('forum::discuss.layouts.app')

@section('content')
    <div class="section is-normal">
        <div class="container">
            {{Form::open([
                'url'=>route('forum.login'),
                'class'=>"signup-form columns is-multiline",
            ])}}
            {{ csrf_field() }}
            <section class="column is-6">
                <fieldset class="box h-100">
                    <h1 class="title is-3 mb-a has-text-centered">
                        Log In
                    </h1>
                    <div class="inputs-wrap py-3">
                        <!-- Email Address -->
                        <div class="control">
                            {{Form::email('email','',[
                                'required',
                                'class'=>'input',
                                'placeholder'=>'Email'
                            ])}}

                        </div>

                        <!-- Password -->
                        <div class="control">
                            {{Form::password('password',[
                                'required',
                                'class'=>'input',
                                'placeholder'=>'Password',
                            ])}}
                        </div>
                        {{Form::hidden('remember','remember')}}
                        <div class="control">
                            {{Form::button('Log In',[
                                'class'=>'button is-primary is-submit ',
                                'type'=>'submit'
                            ])}}
                        </div>
                        @if($errors->any())
                            <span class="help is-danger">
                                                We couldn't verify your credentials.
                                    </span>
                        @endif
                    </div>
                    <footer class="is-flex is-justified-space-between">
                        <a href="{{route('forum.register')}}">
                            Sign Up!
                        </a>
                        <p class="color-success">
                            Forgot Your Password?
                        </p>
                    </footer>
                </fieldset>

            </section>
            {{Form::close()}}
        </div>
    </div>
@endsection
