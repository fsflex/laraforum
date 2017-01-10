<?php

namespace FsFlex\LaraForum\Controllers;

use FsFlex\LaraForum\Helpers\Helper;
use FsFlex\LaraForum\Models\Reach;
use FsFlex\LaraForum\Models\User;
use FsFlex\LaraForum\Models\UserReach;
use Illuminate\Support\Facades\Auth;

class ReachController extends Controller
{
    protected $redirect_on_fail='discuss.index';
    /**
     * @param $target =  [post / thread] type target
     * @param $id = id target
     * @param $reach [like/favourite/notify/dislike]
     */
    public function store($target,$id,$reach)
    {
        $user = Auth::user();
        if(!$user)
            abort(401);
        $user = User::find($user->id);
        switch ($this->validator($target,$id,$reach))
        {
            case 'threads-favourite':{
                $this->threadsReachStore($user,$id,'favourite');
                break;
            }
            case 'threads-dislike':{
                $this->threadsReachStore($user,$id,'dislike');
                break;
            }
            case 'threads-notify':{
                $this->threadsReachStore($user,$id,'notify');
                break;
            }
            case 'posts-like':{
                $this->postsLikeStore($user,$id);
                break;
            }
        }
        return redirect()->back();
    }
    public function destroy($target,$id,$reach)
    {
        $user = Auth::user();
        if(!$user)
            abort(401);
        $user = User::find($user->id);
        switch ($this->validator($target,$id,$reach))
        {
            case 'threads-favourite':{
                $this->threadsReachDestroy($user,$id,'favourite');
                break;
            }
            case 'threads-dislike':{
                $this->threadsReachDestroy($user,$id,'dislike');
                break;
            }
            case 'threads-notify':{
                $this->threadsReachDestroy($user,$id,'notify');
                break;
            }
            case 'posts-like':{
                $this->postsLikeDestroy($user,$id);
                break;
            }
        }
        return redirect()->back();
    }

    /**
     *  Destroy series
     */
    public function threadsReachDestroy($user,$thread_id,$reach_name)
    {
        $reach = Helper::getReaches()->where('name',$reach_name)->first();
        $user_reach = UserReach::where('user_id',$user->id)
            ->where('thread_id',$thread_id)
            ->where('reach_id',$reach->id)
            ->first();
        if($user_reach)
            $user_reach->delete();
    }
    public function postsLikeDestroy($user,$post_id)
    {
        $user->likes()->detach($post_id);
    }
    /**
     * attach series
     */
    public function threadsReachStore($user,$thread_id,$reach_name)
    {
        $reach = Helper::getReaches()->where('name',$reach_name)->first();
        $user->reaches($reach_name)->attach($thread_id,['reach_id'=>$reach->id]);
    }
    public function postsLikeStore($user,$post_id)
    {
        $user->likes()->attach($post_id);
    }
    /**
     * @param $target
     * @param $id
     * @param $reach
     * @return false/string with any err in rules Or string value use for switch case
     */
    protected function validator($target,$id,$reach)
    {
        if(intval($id)!= $id)
            return false;
        $rules = [
            'threads' =>['favourite','notify','dislike'],
            'posts' =>['like']
        ];
        $targets_valid = array_keys($rules);
        if(!in_array($target,$targets_valid))
            return false;
        if(!in_array($reach,$rules[$target]))
            return false;
        return $target.'-'.$reach;
    }
}
