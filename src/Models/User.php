<?php

namespace FsFlex\LaraForum\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;


class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * @return model of table user_reaches connect to user
     */
    public function user_reaches()
    {
        return $this->hasMany(UserReach::class);
    }
    public function likes()
    {
        return $this->belongsToMany(Post::class,'user_likes');
    }
    /**
     * @return profile of user
     */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }
    /**
     * @return threads user favourited
     */
    public function favourites()
    {
        $rawPivot = 'reach_id in (select id from reaches where name = "favourite")';
        return $this->belongsToMany(Thread::class,'user_reaches')->whereRaw($rawPivot);
    }
    /**
     * @return threads user created
     */
    public function threads()
    {
        return $this->hasMany(Thread::class);
    }

    /**
     * @return posts user created
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * @return threads user reached with reach
     */
    public function reaches($reachName)
    {
        $rawPivot = 'reach_id in (select id from reaches where name = "' . $reachName . '")';
        return $this->belongsToMany(Thread::class, 'user_reaches')->whereRaw($rawPivot);
    }

    /**
     * @return return total favourites user receive
     */
    public function getFavouritesCount()
    {
        $a = DB::table('threads')->select('id')->where('user_id',$this->id)->pluck('id');
        $favourite_id = '(SELECT id FROM reaches WHERE name = "favourite")';
        return DB::table('user_reaches')->whereIn('thread_id',$a)
            ->whereRaw('(reach_id IN '.$favourite_id.')')
            ->count();
    }
}
