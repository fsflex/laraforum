<?php

namespace FsFlex\LaraForum\Controllers;


use FsFlex\LaraForum\Helpers\Helper;
use FsFlex\LaraForum\Models\Channel;
use FsFlex\LaraForum\Models\User;
use FsFlex\LaraForum\Requests\StoreChannelRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChannelController extends Controller
{
    public function store(StoreChannelRequest $request)
    {
        //validate managers
        $managers_id = [];
        if ($managers = $request->input('managers')) {
            $managers_id = $this->validateManagers($managers, $redirect);
            if (!$managers_id)
                return $redirect;
        }
        $new_channel = new Channel;
        $new_channel->name = $request->input('name');
        $new_channel->color = $request->input('color');
        $new_channel->managers = $managers_id;
        $new_channel->save();
        Helper::forgetChannels();
        return redirect()->route('discuss.index');
    }


    public function create()
    {
        if (!Auth::check() || Auth::user()->name !== config('laraforum.admin_username'))
            abort(401);
        Helper::getDiscussTemplateRequire($user, $channels);
        $colors = Helper::getColorsLibrary();
        return view('forum::' . config('laraforum.template') . '.channel.create', compact(['user', 'channels', 'colors']));
    }

    public function edit($channel_id)
    {
        Helper::getDiscussTemplateRequire($user, $channels);
        $channel = $channels->where('id', $channel_id)->first();
        if (!$channel)
            abort(404);
        if (!Auth::check() || !(Auth::user()->name === config('laraforum.admin_username') || $channel->isManager(Auth::user()->id)))
            abort(401);
        $managers_list = ($managers_list = User::find($channel->managers)) ? $managers_list->pluck('name') : [];
        if (count($managers_list) > 1) {
            $channel->managers_list = $managers_list[0];
            for ($i = 1; $i < count($managers_list); $i++)
                $channel->managers_list .= ';' . $managers_list[$i];
        } else {
            $channel->managers_list = '';
        }
        $channel_editing = $channel;

        $colors = Helper::getColorsLibrary();
        return view('forum::' . config('laraforum.template') . '.channel.edit', compact(['channel_editing', 'user', 'channels', 'colors']));
    }

    public function update(Request $request, $channel_id)
    {
        $channels = Helper::getChannels();
        $channel = $channels->where('id', $channel_id)->first();
        if (!$channel)
            abort(404);
        if (!Auth::check())
            abort(401);
        if (!$request->has('color') || !$request->has('name'))
            abort(404);
        if ($request->has('manager') && Auth::user()->name !== config('laraforum.admin_username'))
            abort(401);
        if (!(Auth::user()->name === config('laraforum.admin_username') || !$channel->isManager(Auth::user()->id)))
            abort(401);
        if ($request->input('name') !== $channel->name && $channels->where('name', $request->input('name'))->first())
            return redirect()->back()->withErrors(['name' => 'Channel name you input is existed.']);
        $managers_id = [];
        if ($managers = $request->input('managers')) {
            $managers_id = $this->validateManagers($managers, $redirect);
            if (!$managers_id)
                return $redirect;
        }
        $channel->name = $request->input('name');
        $channel->color = $request->input('color');
        $channel->managers = $managers_id;
        $channel->save();
        Helper::forgetChannels();
        session(['message.channel_updated' => 'channel information updated']);
        return redirect()->route('channel.edit', [$channel->id]);
    }

    public function remove($channel_id)
    {
        if (!Auth::check() || Auth::user()->name !== config('laraforum.admin_username'))
            abort(401);
        Helper::getDiscussTemplateRequire($user, $channels);
        $channel = $channels->where('id', $channel_id)->first();
        if (!$channel)
            abort(404);
        $channel_removing = $channel;
        $channels_to_keep = $channels->where('id', '<>', $channel->id);
        return view('forum::' . config('laraforum.template') . '.channel.remove', compact(['channels_to_keep', 'channel_removing', 'user', 'channels']));
    }

    public function destroy(Request $request, $channel_id)
    {
        if (!Auth::check() || Auth::user()->name !== config('laraforum.admin_username'))
            abort(401);
        if ($request->input('accepted') !== 'REMOVE')
            return redirect()->back()->withErrors(['accepted' => 'Please submit exactly REMOVE']);
        switch ($request->input('action')) {
            case 'remove': {
                $threads_id = DB::table('threads')->select('id')->where('channel_id', $channel_id)->get()->pluck('id');
                $posts_id = DB::table('posts')->select('id')->where('thread_id', $threads_id)->get();
                $posts_id = ($posts_id->count() > 0) ? $posts_id->pluck('id') : [];
                DB::table('threads')->where('channel_id', $channel_id)->delete();
                DB::table('posts')->whereIn('thread_id', $threads_id)->delete();
                DB::table('user_reaches')->whereIn('thread_id', $threads_id)->delete();
                DB::table('user_likes')->whereIn('post_id', $posts_id)->delete();
                break;
            }
            case 'keep': {
                if (!($keep_id = $request->input('keep_channel')))
                    abort(404);
                DB::table('threads')->where('channel_id', $channel_id)->update(['channel_id' => $keep_id]);
                break;
            }
            default : {
                return redirect()->back()->withErrors(['action' => 'Please chosen action']);
                break;
            }
        }
        $channel_remove = Helper::getChannels()->where('id', $channel_id)->first();
        $channel_remove->delete();
        Helper::forgetChannels();
        return redirect()->route('discuss.index');
    }

    public function validateManagers($managers, &$redirect)
    {
        $managers = str_replace(' ', '', $managers);
        $managers = explode(';', $managers);
        array_unique($managers);
        $users = DB::table('users')->select(['id', 'name'])->whereIn('name', $managers)->get();
        if ($users->count() == 0) {
            $redirect = redirect()->back()->withErrors(['managers' => 'We can\'t find any user name you input']);
            return false;
        }
        if ($users->count() != count($managers)) {
            $error_messages = [];
            foreach ($managers as $manager) {
                if (!$users->where('name', $manager)->first())
                    $error_messages[] = $manager . ' do not match any username.';
            }
            $redirect = redirect()->back()->withErrors(['managers' => $error_messages]);
            return false;
        }
        return $users->pluck('id');
    }
}























