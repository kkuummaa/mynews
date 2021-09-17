<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Profile;

class ProfileController extends Controller
{
    //
    public function add()
    {
        return view('admin.profile.create');
    }
    
    public function create(Request $request) // Requestはブラウザから送られるユーザーの情報全てを含んだオブジェクト。それを$request変数に代入して使う
    {
        // Varidationを行う
        $this->validate($request, Profile::$rules); // DEBUGBAR_ENABLED=null  # デフォルト。APP_DEBUGに応じて決まる
        
        $profile = new Profile; //Profileのデータベースを$newsに入れる。
        $form = $request->all(); //$requestの情報を全て取得
        
        // フォームから送信されてきた_tokenを削除する
        unset($form['_token']);
        
        // データベースに保存する
        $profile->fill($form); // ここで本文とかタイトルとかをnewsに入れる
        $profile->save();
        
        
        // admin/news/createにリダイレクトする
        return redirect('admin/profile/create');
    }
    
    // 投稿したニュース一覧
    public function index(Request $request)
    {
        $cond_title = $request->cond_title;
        if ($cond_title != '') {
        // 検索されたら検索結果を取得する
        $posts = Profile::where('title', $cond_title)->get(); //　newsテーブルの中のtitleカラムで$cond_title（ユーザーが入力した文字）に一致するレコードをすべて取得することができます。
        } else {
            // それ以外はすべてのニュースを取得する
            $posts = Profile::all();
        }
        return view('admin.profile.index', ['posts' => $posts, 'cond_title' => $cond_title]); // index.blade.phpのファイルに取得したレコード（$posts）と、ユーザーが入力した文字列（$cond_title）を渡し、ページを開きます。
        // ControllerではModelに対して where メソッドを指定して検索しています。
    }
    
    public function edit(Request $request)
    {
        // Profile Modelからデータを取得する
        $profile = Profile::find($request->id);
        if (empty($profile)) {
            abort(404);
        }

        return view('admin.profile.edit', ['profile_form' => $profile]);
    }
    
    public function update(Request $request)
    {
        // Validationをかける
        $this->validate($request, Profile::$rules);
        // Profile Modelからデータを取得する
        $profile = Profile::find($request->id);
        // 送信されてきたフォームデータを格納する
        $profile_form = $request->all();
        
        unset($profile_form['remove']);
        unset($profile_form['_token']);

        // 該当するデータを上書きして保存する
        $profile->fill($profile_form)->save();
        
        return redirect('admin/profile');
    }
    

}
