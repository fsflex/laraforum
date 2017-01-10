<?php

namespace FsFlex\LaraForum\Models;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    /**
     * @return threads created with this channel
     */
    public function threads()
    {
        return $this->hasMany(Thread::class);
    }

    public function getNameAttribute($value)
    {
        return strtolower($value);
    }

    public function getManagersAttribute($value)
    {
        if ($value)
            return json_decode($value);
        return null;
    }

    public function setManagersAttribute($value)
    {
        if (is_array($value))
            $this->attributes['managers'] = json_encode($value);
        else
            $this->attributes['managers'] = $value;
    }

    public function isManager($user_id)
    {
        if (is_array($managers = $this->managers))
            return (in_array($user_id, $managers)) ? true : false;
        return false;
    }
}
