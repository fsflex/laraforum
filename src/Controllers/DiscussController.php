<?php

namespace FsFlex\LaraForum\Controllers;

use FsFlex\LaraForum\Requests\StoreDiscussRequest;
use FsFlex\LaraForum\Models\Post;
use FsFlex\LaraForum\Models\Thread;
use FsFlex\LaraForum\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use FsFlex\LaraForum\Helpers\Helper;

class DiscussController extends Controller
{
    protected $redirect_on_fail = 'discuss.index';


    public function show($channel, $thread)
    {
        $maxPostInPage = config('laraforum.posts_paginate');
        Helper::getDiscussTemplateRequire($user, $channels);
        $thread = Thread::where('slug', $thread)->orWhere('id', $thread)
            ->with(['channel', 'user', 'answer.likes', 'answer.user.profile'])
            ->first();
        if (!$thread) {
            abort(401);
        }
        if (strtolower($thread->channel->name) != strtolower($channel))
            abort(401);

        $posts = $thread->posts()->with(['user.profile'])->withCount('likes')->paginate($maxPostInPage);
        $reach_name = [];
        if ($reaches = ($user) ? $thread->anyReachesBy($user->id)->get() : [])
            foreach ($reaches as $reach) {
                $reach_name[] = $reach->name;
            }
        $thread->is_notified = (in_array('notify', $reach_name)) ? true : false;
        $thread->is_favourited = (in_array('favourite', $reach_name)) ? true : false;
        $thread->is_disliked = (in_array('dislike', $reach_name)) ? true : false;
        $user_liked_in_thread = ($user) ? $user->likes()->where('thread_id', $thread->id)->get()->pluck('id')->toArray() : [];
        if ($thread->answer)
            $thread->answer->is_liked = (in_array($thread->answer->id, $user_liked_in_thread)) ? true : false;
        foreach ($posts as $post) {
            $post->is_liked = (in_array($post->id, $user_liked_in_thread)) ? true : false;
        }
        $thread->manager_role = ($user && ($user->name === config('laraforum.admin_username') || $thread->channel->isManager($user->id))) ? 1 : 0;
        $header = [
            'title' => $thread->title,
            'description' => $thread->body,
        ];
        return view('forum::' . config('laraforum.template') . '.discuss.show',
            compact(['thread', 'posts', 'user', 'channels', 'header']));
    }

    /**
     *  function find thread by some option in view
     */
    public function index(Request $request)
    {
        Helper::getDiscussTemplateRequire($user, $channels);
        $cr_channel = null;
        $header = null;
        if ($request->has('channel')) {
            $cr_channel = $channels->where('name', $request->input('channel'))->first();
            if (!$cr_channel)
                abort(404);
        }
        $message = null;
        $search = null;
        $maxInPage = config('laraforum.threads_paginate');
        switch ($filler = $this->getOneFillerName($request)) {
            case 'trending': {
                $order = DB::table('posts')->select(DB::raw('thread_id,count(id) AS total_posts'))
                    ->whereBetween('created_at', [new Carbon('last monday'), new Carbon('now')])
                    ->groupBy('thread_id')
                    ->orderBy('total_posts', 'DESC');
                $order = $order->paginate($maxInPage);
                $order->appends($request->all());
                $threads = Thread::whereIn('id', $order->pluck('thread_id'))
                    ->with(['channel', 'user', 'last_post.user', 'user.profile'])
                    ->get();
                foreach ($threads as $thread) {
                    $thread->posts_count = $order->where('thread_id', $thread->id)->first()->total_posts;
                }
                $threads = $threads->sortByDesc('posts_count');
                $threads->links = ($order->total() != $order->count()) ? $order->links() : '';
                return view('forum::' . config('laraforum.template') . '.discuss.index')
                    ->with(['threads' => $threads, 'user' => $user, 'message' => $message, 'search' => $search, 'channels' => $channels]);
                break;
            }
            case 'popular': {
                $order = DB::table('posts')->select(DB::raw('thread_id,count(id) AS total_posts'))
                    ->groupBy('thread_id')->orderBy('total_posts', 'DESC');
                $order = $order->paginate($maxInPage);
                $order->appends($request->all());
                $threads = Thread::whereIn('id', $order->pluck('thread_id'))
                    ->with(['channel', 'user', 'last_post.user', 'user.profile'])
                    ->get();
                foreach ($threads as $thread) {
                    $thread->posts_count = $order->where('thread_id', $thread->id)->first()->total_posts;
                }
                $threads = $threads->sortByDesc('posts_count');
                $threads->links = ($order->total() != $order->count()) ? $order->links() : '';
                return view('forum::' . config('laraforum.template') . '.discuss.index')
                    ->with(['threads' => $threads, 'user' => $user, 'message' => $message, 'search' => $search, 'channels' => $channels]);
                break;
            }
            case 'answered': {
                $threads_fill = ($request->input('answered')) ?
                    DB::table('threads')->select('id')->whereNotNull('best_answer_id') :
                    DB::table('threads')->select('id')->whereNull('best_answer_id');
                break;
            }
            case 'me': {
                if (!$user)
                    abort(404);
                $threads_fill = DB::table('threads')->select('id')->where('user_id', $user->id);
                break;
            }
            case 'favourites': {
                if (!$user)
                    abort(404);
                $favourited_id = Helper::getReaches()->where('name', 'favourite')->first();
                $threads_favourited_id = DB::table('user_reaches')->select('thread_id')->where('reach_id', $favourited_id)->get();
                $threads_favourited_id = ($threads_favourited_id->count() > 0) ? $threads_favourited_id->pluck('thread_id') : [];
                $threads_fill = DB::table('threads')->select('id')->whereIn('id', $threads_favourited_id);
                break;
            }
            case 'contributed_to': {
                if (!$user)
                    abort(404);
                $threads_contributed_to_id = DB::table('posts')->select('thread_id')
                    ->where('user_id', $user->id)->groupBy('thread_id')->get();
                $threads_contributed_to_id = ($threads_contributed_to_id->count() > 0) ? $threads_contributed_to_id->pluck('thread_id') : [];
                $threads_fill = DB::table('threads')->select('id')->whereIn('id', $threads_contributed_to_id);
                break;
            }
            case 'search': {
                $search = $request->input('search');
                $search = trim($search, ' ');
                $search = preg_replace('/[\s]+/u', ' ', $search);
                $search_key = '%' . $search . '%';
                $threads_fill = DB::table('threads')->select('id')->where('title', 'LIKE', $search_key)->orWhere('body', 'LIKE', $search_key);
                break;
            }
            case 'dynamic_search': {
                $search = $request->input('dynamic_search');
                $search_key = ' ' . $search . ' ';
                $search_key = preg_replace('/[\W\s]+/u', '%', $search_key);
                $message = 'We can\'t find discuss exists your key, this is result of dynamic search.';
                $threads_fill = DB::table('threads')->select('id')->where('title', 'LIKE', $search_key)->orWhere('body', 'LIKE', $search_key);
                break;
            }
            case 'channel': {

                $threads_fill = DB::table('threads')->select('id')->where('channel_id', $cr_channel->id);
                $header = ['title' => 'Channel : ' . title_case($cr_channel->name)];
                break;
            }
            default: {
                $threads_fill = DB::table('threads')->select('id');
                break;
            }
        }
        $threads_fill = $threads_fill->orderBy('last_post_id', 'DESC')->paginate($maxInPage);
        $threads_fill_id = ($threads_fill->count() > 0) ? $threads_fill->pluck('id') : [];
        $threads = Thread::with(['channel', 'user', 'last_post.user', 'user.profile'])->whereIn('id', $threads_fill_id)->get();
        if ($cr_channel) {
            if ($user)
                $user->channel_edit_role = ($user->name === config('laraforum.admin_username') ||
                    $cr_channel->isManager($user->id)) ? $cr_channel->id : false;
        }
        $threads_fill->appends($request->all());
        if ($filler == 'search' && $threads_fill->total() < 1)
            return redirect()->route('discuss.index', ['dynamic_search' => $request->input('search')]);
        if ($filler == 'dynamic_search' && $threads_fill->total() < 1)
            $message = 'We can\'t find anything with this key, please try another';
        if ($filler == 'search' || $filler == 'dynamic_search')
            $threads = $this->highlightKey($threads, $search_key);
        if ($search)
            $search = preg_replace('/[\W\s]+/u', ' ', $search);
        //add post_count
        $data_posts_count = DB::table('posts')->select(DB::raw('thread_id,count(id) as total_posts'))
            ->whereIn('thread_id', $threads->pluck('id'))
            ->groupBy('thread_id')
            ->get();
        foreach ($threads as $thread) {
            $thread->posts_count = ($value = $data_posts_count->where('thread_id', $thread->id)->first()) ? $value->total_posts : 0;
        }
        // add links paginate
        $threads->links = ($threads_fill->total() != $threads_fill->count()) ? $threads_fill->links() : '';
        return view('forum::' . config('laraforum.template') . '.discuss.index')
            ->with(['threads' => $threads, 'user' => $user, 'message' => $message, 'search' => $search, 'channels' => $channels]);
    }

    protected function highlightKey($threads, $search_key, $color = 'rgba(255, 191, 30, 0.44)')
    {
        $key = trim($search_key, '%');
        $keys = explode('%', $key);
        $keys = array_unique($keys);
        foreach ($threads as $thread) {
            $thread->title_with_highlight = $this->highlightStr($keys, $thread->title, $color);
            $thread->body_with_highlight = $this->highlightStr($keys, $thread->body, $color);
        }
        return $threads;
    }

    protected function highlightStr($source_keys, $str, $color)
    {
        $start_highlight = "<span style='background-color:$color'>";
        $end_highlight = "</span>";
        $keys = [];
        foreach ($source_keys as $source_key) {
            $keys[] = $start_highlight . $source_key . $end_highlight;
        }
        return str_ireplace($source_keys, $keys, $str);
    }

    private function getOneFillerName($request)
    {
        if (Auth::check()) {
            if ($request->has('me'))
                return 'me';
            if ($request->has('contributed_to'))
                return 'contributed_to';
            if ($request->has('favourites'))
                return 'favourites';
        }
        if ($request->has('trending'))
            return 'trending';
        if ($request->has('popular'))
            return 'popular';
        if ($request->has('answered'))
            return 'answered';
        if ($request->has('search'))
            return 'search';
        if ($request->has('dynamic_search'))
            return 'dynamic_search';
        if ($request->has('channel'))
            return 'channel';
        return 'nothing';
    }

    /**
     * @param StoreDiscuss $request
     */
    public function store(StoreDiscussRequest $request)
    {
        $user = Auth::user();
        if ($time = $this->isIntervalLimit($user->id))
            return redirect()->back()
                ->withErrors(['msg' => "You need wait more " . (round($time / 60)) . " minutes to create new conversation"]);
        $data = $request->all();
        $thread = Thread::where('title', $data['title'])->first();
        if ($thread)
            return redirect()->back()->withErrors(['title.unique' => 'This discuss is exist you can find it in search bar']);
        $thread = new Thread;
        $thread->user_id = $user->id;
        $thread->channel_id = $data['channel'];
        $thread->title = $data['title'];
        $thread->body = $data['body'];
        $thread->save();
        return redirect()->route('discuss.show', [$thread->channel->name, $thread->slug]);
    }

    protected function isIntervalLimit($user_id)
    {
        $limit_time = config('laraforum.threads_interval');//minutes
        //select latest thread user created
        $last_created = DB::table('threads')->select(DB::raw('MAX(created_at) as time'))
            ->where('user_id', $user_id)
            ->first();
        if (!($last_created->time))
            return 0;
        if ($time = Carbon::now()->diffInSeconds(new Carbon($last_created->time)) >= $limit_time)
            return 0;
        return $limit_time - $time;
    }

    public function edit($thread_id)
    {
        Helper::getDiscussTemplateRequire($user, $channels);
        $user = Auth::user();
        if ($user) {
            $user = User::with('profile')->find($user->id);
        }
        if (!$user)
            abort(401);
        $thread = Thread::with(['user', 'channel'])->find($thread_id);
        if (!$thread)
            abort(401);
        if ($thread->user->id != $user->id)
            abort(401);
        $channels_select = [];
        $channels_select[0] = 'Pick a Channel...';
        foreach ($channels as $channel) {
            $channels_select[$channel->id] = title_case($channel->name);
        }
        return view('forum::' . config('laraforum.template') . '.discuss.edit', compact(['user', 'channels', 'channels_select', 'thread']));
    }

    public function update(StoreDiscussRequest $request, $thread_id)
    {
        $user = Auth::user();
        if (!$user)
            abort(401);
        $thread = Thread::with(['user', 'channel'])->find($thread_id);
        if (!$thread)
            abort(401);
        if ($thread->user->id != $user->id)
            abort(401);
        $data = $request->all();
        if ($data['title'] != $thread->title) {
            $thread_check = Thread::where('title', $data['title'])->first();
            if ($thread_check)
                return redirect()->back()->withErrors(['title.unique' => 'This discuss is exist you can find it in search bar']);
        }
        $thread->channel_id = $data['channel'];
        $thread->slug = null;//trigger to update slug
        $thread->title = $data['title'];
        $thread->body = $data['body'];
        $thread->author_updated_at = Carbon::now();
        $thread->save();
        return redirect()->route('discuss.show', [$thread->channel->name, $thread->slug]);
    }

    public function create(Request $request)
    {
        Helper::getDiscussTemplateRequire($user, $channels);
        $channel_selected = ($request->has('channel')) ? $channels->where('name', $request->input('channel'))->first() : '';
        $channel_selected = ($channel_selected) ? $channel_selected->id : 0;
        $channels_select = [];
        $channels_select[0] = 'Pick a Channel...';
        foreach ($channels as $channel) {
            $channels_select[$channel->id] = title_case($channel->name);
        }
        return view('forum::' . config('laraforum.template') . '.discuss.create', compact(['channel_selected', 'channels_select', 'user', 'channels']));
    }

    public function setBestAnswer($thread_id, $post_id)
    {
        $user = Auth::user();
        $thread = Thread::with('user')->find($thread_id);
        if (!$thread || $thread->user->id != $user->id)
            abort(401);
        $post = Post::with('thread')->find($post_id);
        if (!$post || $thread->id != $post->thread->id)
            abort(401);
        DB::table('threads')->where('id', $thread_id)->update(['best_answer_id' => $post->id]);
        return redirect()->back();
    }

    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => 'required'
        ]);
        if ($validator->fails())
            abort(401);
    }

    public function managerDestroy($thread_id)
    {
        Helper::getDiscussTemplateRequire($user);
        if (!$user)
            abort(401);
        $thread = Thread::find($thread_id);
        if (!$thread)
            abort(404);
        $thread->load(['channel']);
        if (!Helper::isAdmin($user) && !$thread->channel->isManager($user->id))
            abort(401);
        $posts_id = DB::table('posts')->select('id')->where('thread_id', $thread->id)->get();
        $posts_id = ($posts_id->count() > 0) ? $posts_id->pluck('id') : [];
        DB::table('posts')->where('thread_id', $thread->id)->delete();
        DB::table('user_reaches')->where('thread_id', $thread->id)->delete();
        DB::table('user_likes')->whereIn('post_id', $posts_id)->delete();
        $thread->delete();
        return redirect()->route('discuss.index')->with(['message' => "Discuss Deleted"]);
    }
}
