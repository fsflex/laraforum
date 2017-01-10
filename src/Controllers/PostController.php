<?php

namespace FsFlex\LaraForum\Controllers;

use FsFlex\LaraForum\Models\Post;
use FsFlex\LaraForum\Models\Thread;
use FsFlex\LaraForum\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    protected $redirect_on_fail = 'discuss.index';

    public function store(Request $request, $thread_id)
    {
        $user = Auth::user();
        if ($user) {
            $user = User::find($user->id);
        }
        if (!$user)
            abort(401);
        if (!$thread = Thread::find($thread_id))
            abort(401);
        if ($time = $this->isIntervalLimit($user->id))
            return redirect()->back()->withErrors(['msg' => "Please relax !"]);
        $validator = Validator::make($request->all(), [
            'body' => 'required|max:60000'
        ]);
        if ($validator->fails())
            return redirect()->back()->withErrors(['msg' => "Please don't store a book in there!"]);
        $body = $request->input('body');
        $post = new Post;
        $post->user_id = $user->id;
        $post->thread_id = $thread_id;
        $post->body = $body;
        $post->save();
        $thread->updateLastPostId();
        return redirect()->back();
    }

    /**
     * @return check is user spam.
     */
    protected function isIntervalLimit($user_id)
    {
        //select lastest time post of user
        $last_created = DB::table('posts')->select(DB::raw('MAX(created_at) AS time'))
            ->where('user_id',$user_id)
            ->first();
        $limit_time = config('laraforum.posts_interval');//second
        if (!($last_created))
            return 0;
        if ($time = Carbon::now()->diffInSeconds(new Carbon($last_created->time)) >= $limit_time)
            return 0;
        return $limit_time - $time;
    }

    public function edit($post_id)
    {
        $user = Auth::user();
        if ($user) {
            $user = User::with('profile')->find($user->id);
        }
        if (!$user)
            return redirect()->back();
        $post = Post::where('id', $post_id)->with('user')->first();
        if ($post && $post->user->id == $user->id)
            return redirect()->back()->withInput(['post_editing_id' => $post->id]);
        return redirect()->back();
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $user = User::with('profile')->find($user->id);
        }
        if (!$user)
            abort(401);
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'body' => 'required|max:60000'
        ]);
        if ($validator->fails())
            abort(401);
        $post = Post::where('id', $request->input('id'))->with('user')->first();
        if ($post->user->id != $user->id)
            abort(401);
        $post->body = $request->input('body');
        $post->save();
        return redirect()->back();
    }

    public function destroy($post_id)
    {
        $user = Auth::user();
        if ($user) {
            $user = User::with('profile')->find($user->id);
        }
        if (!$user)
            abort(401);
        $post = Post::where('id', $post_id)->with(['user', 'thread'])->first();
        if ($post == null || $post->user->id != $user->id)
            abort(401);
        $thread = $post->thread;
        $post->delete();
        $thread->updateLastPostId();
        return redirect()->back();
    }


}
