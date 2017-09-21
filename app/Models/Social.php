<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Social extends Model
{
    const FACEBOOK = 1;
    const GOOGLE = 2;
    const TWITTER = 3;

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public static function parseSocial($enum)
    {
        switch($enum)
        {
            case self::FACEBOOK:
                return 'Facebook';
            case self::GOOGLE:
                return 'Google';
            case self::TWITTER:
                return 'Twitter';
            default:
                throw \Exception("{$enum} is invalid social provider.");
        }
    }

}
