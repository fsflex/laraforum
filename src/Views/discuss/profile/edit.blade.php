@extends('forum::discuss.layouts.app')
@section('content')
    <div class="section is-normal">
        <div class="container">
            {{Form::open([
                'url'=>route('profile.update',[$user->name]),
                'class'=>"signup-form columns is-multiline",
            ])}}
            <section class="column is-6">
                <fieldset class="box h-100">
                    <h3 class="heading is-5" data-step="two">
                        Edit Your Profile
                    </h3>
                    <div class="control">
                        <label for="website" class="label">Personal Website:</label>
                        {{Form::text('website',$userpreview->profile->website,[
                            'class' => 'input',
                            'placeholder'=>'http://example.com'
                        ])}}
                        @if($errors->has('website'))
                            @foreach($errors->get('website') as $message)
                                <span class="help is-danger">
                                            * {{$message}}
                                        </span>
                            @endforeach
                        @endif
                    </div>

                    <div class="control">
                        <label for="twitter" class="label">Twitter Username:</label>
                        {{Form::text('twitter_username',$userpreview->profile->twitter_username,['class'=>'input'])}}
                        @if($errors->has('twitter_username'))
                            @foreach($errors->get('twitter_username') as $message)
                                <span class="help is-danger">
                                            * {{$message}}
                                        </span>
                            @endforeach
                        @endif
                    </div>

                    <div class="control">
                        <label for="github" name="github" class="label"> GitHub Username:</label>
                        {{Form::text('github_username',$userpreview->profile->github_username,['class'=>'input'])}}
                        @if($errors->has('github_username'))
                            @foreach($errors->get('github_username') as $message)
                                <span class="help is-danger">
                                            * {{$message}}
                                        </span>
                            @endforeach
                        @endif
                    </div>

                    <div class="control">
                        <label for="employment" name="employment" class="label">Place of Employment:</label>
                        {{Form::text('place_of_employment',$userpreview->profile->place_of_employment,['class'=>'input'])}}
                        @if($errors->has('place_of_employment'))
                            @foreach($errors->get('place_of_employment') as $message)
                                <span class="help is-danger">
                                            * {{$message}}
                                        </span>
                            @endforeach
                        @endif
                    </div>

                    <div class="control">
                        <label for="job_title" name="job_title" class="label">Job Title:</label>
                        {{Form::text('job_title',$userpreview->profile->job_title,['class'=>'input'])}}
                        @if($errors->has('job_title'))
                            @foreach($errors->get('job_title') as $message)
                                <span class="help is-danger">
                                            * {{$message}}
                                        </span>
                            @endforeach
                        @endif
                    </div>

                    <div class="control">
                        <label for="hometown" name="location" id="location" class="label">Hometown:</label>
                        {{Form::text('hometown',$userpreview->profile->hometown,['class'=>'input'])}}
                        @if($errors->has('hometown'))
                            @foreach($errors->get('hometown') as $message)
                                <span class="help is-danger">
                                            * {{$message}}
                                        </span>
                            @endforeach
                        @endif
                    </div>
                    <div class="control">
                        <label for="avatar" name="avatar" class="label">Avatar:</label>
                        {{Form::text('avatar',$userpreview->profile->avatar,[
                                                    'class'=>'input',
                                                    'placeholder'=>'Image url'
                                                    ])}}
                        @if($errors->has('avatar'))
                            @foreach($errors->get('avatar') as $message)
                                <span class="help is-danger">
                                            * {{$message}}
                                        </span>
                            @endforeach
                        @endif
                    </div>

                    <div class="control">
                        <label for="flag" name="flag" id="flag" class="label">Country Flag:</label>
                        <span class="select w-100">
                                {{Form::select('country',$countries,$userpreview->profile->country_id,['class'=>'w-100'])}}
                            @if($errors->has('country'))
                                @foreach($errors->get('country') as $message)
                                    <span class="help is-danger">
                                            * {{$message}}
                                        </span>
                                @endforeach
                            @endif
                            </span>
                    </div>

                    <div class="control">
                        <label for="available_for_hire" name="available_for_hire" class="label checkbox">
                            Available For Hire?
                            {{Form::checkbox('is_available_for_hire','true',$userpreview->profile->is_available_for_hire)}}
                        </label>
                    </div>

                    <div class="control">
                        <button type="submit" class="button is-outlined is-primary">
                            Update
                        </button>
                    </div>

                </fieldset>
            </section>
            {{Form::close()}}
        </div>
    </div>
@endsection