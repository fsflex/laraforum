<?php

namespace FsFlex\LaraForum\Models;

use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Thread extends Model
{
    use Sluggable;
    /**
     * @return user created this thread
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return all posts in thread ;
     */
    public function posts()
    {
        return $this->hasMany(Post::class)->withCount('likes');
    }

    /**
     * @return users favourited this thread
     */
    public function favourites()
    {
        $rawPivot = 'reach_id in (select id from reaches where name = "favourite")';
        return $this->belongsToMany(User::class, 'user_reaches')->whereRaw($rawPivot);
    }

    /**
     * @return users reached this thread with reach
     */
    public function reaches($reachName)
    {
        $rawPivot = 'reach_id in (select id from reaches where name = "' . $reachName . '")';
        return $this->belongsToMany(User::class, 'user_reaches')->whereRaw($rawPivot);
    }
    public function anyReachesBy($user_id)
    {
        return DB::table('user_reaches')
            ->where('user_id',$user_id)
            ->where('thread_id',$this->id)
            ->rightJoin('reaches','user_reaches.reach_id','reaches.id');
    }
    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }
    /**
     * @return all record of likes in table user_like connect to this thread
     */
    public function likes()
    {
        return $this->hasManyThrough(Likes::class, Post::class, 'thread_id', 'post_id');
    }

    /**
     * @return last post
     */
    public function last_post()
    {
        return $this->belongsTo(Post::class,'last_post_id');
    }
    /**
     * @return post is best answer in thread;
     */
    public function answer()
    {
        return $this->belongsTo(Post::class,'best_answer_id')->withCount('likes');
    }


    /**
     * @return nomaly order
     */
   public function scopeOrderByNomal($query)
    {
        return $query->orderBy('updated_at','desc');
    }

    /**
     * @return threads has answered
     */
    public function scopeAnswered($query, $flag)
    {
        if ($flag == 0)
            return $query->whereNull('best_answer_id');
        return $query->whereNotNull('best_answer_id');
    }

    /**
     * @return threads in channel
     */
    public function scopeInChannel($query, $channelName)
    {
        $selectChannelId = "(select id from channels where name ='" . $channelName . "')";
        return $query->whereRaw('channel_id IN ' . $selectChannelId);
    }

    /**
     * @return any threads has $search_key in title or body
     */
    public function scopeWithContain($query,$searh_key)
    {
        return $query->where('title','like',$searh_key)->orWhere('body','like',$searh_key);
    }
    /**
     * @param $query
     * @param $userId
     * @return threads contributed by user
     */
    public function scopeContributesBy($query,$userId)
    {
        $userConnect="(SELECT thread_id FROM posts WHERE user_id = '".$userId."' GROUP BY thread_id)";
        $userCreated="(SELECT id FROM threads WHERE user_id = '".$userId."' )";
        return $query->whereRaw('id IN '.$userConnect)->whereRaw('id NOT IN '.$userCreated);
    }
    public function getAuthorUpdatedAtAttribute($value)
    {
        if($value!=null)
            return new Carbon($value);
        else
            return null;
    }

    /**
     * update last post id if exist
     * @return bool
     */
    public function updateLastPostId()
    {
        $last_post_id = DB::table('posts')->select('id')
            ->where('thread_id',$this->id)->orderBy('id','DESC')
            ->first();
        $this->last_post_id = ($last_post_id)? $last_post_id->id : null;
        $this->save();
        return 1;
    }

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {

        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }
}

