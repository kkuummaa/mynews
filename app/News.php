<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    //
    protected $guarded = array('id');
    
    public static $rules = array(
        'title' => 'required',
        'body' => 'required',
    );
    
    // News Modelに関連づけを行う
    // これによってNews Modelから $news->histories()のような記述で簡単にアクセスできる。
    public function histories()
    {
        return $this->hasMany('App\History');

    }
}
