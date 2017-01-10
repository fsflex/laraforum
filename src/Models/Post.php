<?php

namespace FsFlex\LaraForum\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /**
     * @return user created post
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return thread this comment posted;
     */
    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    /**
     * @return users liked this
     */
    public function likes()
    {
        return $this->belongsToMany(User::class,'user_likes');
    }

    /**
     * @param $userId
     * @return bool is user liked this post
     */
    public function isLikedBy($userId)
    {
        if(!array_key_exists('likes',$this->relations))
            $this->load('likes');
        return $this->getRelation('likes')->where('id',$userId)->count()>0 ? true :false;
    }

    /**
     * @return posts is best answer of any thread
     */
    public function scopeIsBestAnswer($query)
    {
        return $query->whereRaw('id IN (select best_answer_id FROM threads where best_answer_id IS NOT NULL)');
    }
}
