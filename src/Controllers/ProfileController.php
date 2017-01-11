<?php

namespace FsFlex\LaraForum\Controllers;

use Carbon\Carbon;
use FsFlex\LaraForum\Helpers\Helper;
use FsFlex\LaraForum\Models\Country;
use FsFlex\LaraForum\Requests\UpdateProfileRequest;
use FsFlex\LaraForum\Models\User;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    protected $redirect_on_fail = 'discuss.index';

    public function show($user_name)
    {
        Helper::getDiscussTemplateRequire($user);
        $userpreview = User::where('name', $user_name)->with(['profile', 'profile.country'])->first();
        if (!$userpreview)
            abort(401);
        $userpreview->best_answer_count = $userpreview->posts()->isBestAnswer()->count();
        $threads = $userpreview->threads()->with('channel')->orderBy('id', 'desc')->take(50)->get();
        $posts = $userpreview->posts()->with(['thread', 'thread.channel'])->orderBy('id', 'desc')->take(50)->get();
        //
        $items = [];
       foreach ($threads as $thread) {
            $items[] = [
                'type' => 'thread',
                'thread_title' => $thread->title,
                'body' => $thread->body,
                'created_at' => $thread->created_at,
                'link' => route('discuss.show', [$thread->channel->name, $thread->id])
            ];
        }
        $userpreview->favourites_count = $userpreview->getFavouritesCount();
        foreach ($posts as $post) {
            $items[] = [
                'type' => 'post',
                'thread_title' => $post->thread->title,
                'body' => $post->body,
                'created_at' => $post->created_at,
                'link' => route('discuss.show', [$post->thread->channel->name, $post->thread->id]) //need rebuild
            ];
        }
        $sorter = collect($items);
        $max = 50;
        $take = max($max, $sorter->count());
        $items = $sorter->sortByDesc('created_at')->take($take);
        $contents = [];// with ['date','items_indate']
        $i = 0;//contents index
        foreach ($items as $item) {
            $startOfDay = $item['created_at']->startOfDay();
            if (!isset($contents[$i]))
                $contents[$i] = [
                    'date' => $startOfDay,
                    'items' => []
                ];
            if ($startOfDay < $contents[$i]['date']) {
                $i++;
                $contents[$i] = [
                    'date' => $startOfDay,
                    'items' => []
                ];
            }
            $contents[$i]['items'][] = $item;
        }
        $contents = collect($contents);
        //dd($userpreview);
        return view(Helper::loadView('profile.show'))->with(['user' => $user, 'userpreview' => $userpreview, 'contents' => $contents]);
    }

    public function edit($user_name)
    {
        Helper::getDiscussTemplateRequire($user,$channels);
        $userpreview = User::where('name',$user_name)->first();
        if (!$user || !$userpreview || $user->id != $userpreview->id)
            abort(401);
        $countries_data = Helper::getCountries();
        $countries = [];
        foreach ($countries_data as $country) {
            $countries[$country->id] = $country->name;
        }
        return view(Helper::loadView('profile.edit'),compact([
            'user','countries','userpreview','channels'
        ]));
    }

    public function update(UpdateProfileRequest $request, $user_name)
    {
        Helper::getDiscussTemplateRequire($user);
        $user_update = User::where('name', $user_name)->with(['profile'])->first();
        if (!$user_update || !$user || $user_update->id != $user->id)
            abort(401);
        $data = $request->all();
        $profile = $user_update->profile;
        $profile->country_id = $data['country'];
        $profile->is_available_for_hire = ($request->input('is_available_for_hire')) ? true : false ;
        $profile->website = $data['website'];
        $profile->twitter_username = $data['twitter_username'];
        $profile->github_username = $data['github_username'];
        $profile->place_of_employment = $data['place_of_employment'];
        $profile->job_title = $data['job_title'];
        $profile->hometown = $data['hometown'];
        $profile->avatar = $data['avatar'];
        $profile->save();
        return redirect()->route('profile.show',[$user_name]);
    }
}
