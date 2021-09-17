<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// News Modelを使用
use App\News;

class NewsController extends Controller
{
    //
    public function add()
    {
        return view('admin.news.create');
    }
    
    public function create(Request $request) // Requestはブラウザから送られるユーザーの情報全てを含んだオブジェクト。それを$request変数に代入して使う
    {
        // Varidationを行う
        $this->validate($request, News::$rules); // DEBUGBAR_ENABLED=null  # デフォルト。APP_DEBUGに応じて決まる
        
        $news = new News; //newsのデータベースを$newsに入れる。
        $form = $request->all(); //$requestの情報を全て取得
        
        // フォームから画像が送信されてきたら、保存して、$news->image_pathに画像のパスを保存する
        if (isset($form['image'])) {
            $path = $request->file('image')->store('public/image'); //送られてきた画像をpublic/imageに保存; store()は画像ファイルを他と被らない名前を使って保存する
            $news->image_path = basename($path); // databaseのimage_pathカラムに画像のパスを保存; database()で画像ファイルの名前だけ取り出す
        } else {
            $news->image_path = null;
        }
        
        // フォームから送信されてきた_tokenを削除する
        unset($form['_token']);
        // フォームから送信されてきたimageを削除する
        unset($form['image']);
        
        // データベースに保存する
        $news->fill($form); // ここで本文とかタイトルとかをnewsに入れる
        $news->save();
        
        
        // admin/news/createにリダイレクトする
        return redirect('admin/news/create');
    }
    
    // 投稿したニュース一覧
    public function index(Request $request)
    {
        $cond_title = $request->cond_title;
        if ($cond_title != '') {
        // 検索されたら検索結果を取得する
        $posts = News::where('title', $cond_title)->get(); //　newsテーブルの中のtitleカラムで$cond_title（ユーザーが入力した文字）に一致するレコードをすべて取得することができます。
        } else {
            // それ以外はすべてのニュースを取得する
            $posts = News::all();
        }
        return view('admin.news.index', ['posts' => $posts, 'cond_title' => $cond_title]); // index.blade.phpのファイルに取得したレコード（$posts）と、ユーザーが入力した文字列（$cond_title）を渡し、ページを開きます。
        // ControllerではModelに対して where メソッドを指定して検索しています。
    }
    
    public function edit(Request $request)
    {
        // News Modelからデータ取得
        $news = News::find($request->id); // $newsにNewのidが一致する場所の情報代入
        if (empty($news)) {
            abort(404);
        }
        return view('admin.news.edit', ['news_form' => $news]);
    }
    
    public function update(Request $request)
    {
        // Validationをかける
        $this->validate($request, News::$rules);
        // News Modelからデータを取得する
        $news = News::find($request->id);
        // 送信されてきたフォームデータを格納する
        $news_form = $request->all();
        if ($request->remove == 'true') {
            $news_form['image_path'] = null;
        } elseif ($request->file('image')) {
            $path = $request->file('image')->store('public/image');
            $news_form['image_path'] = basename($path);
        } else {
            $news_form['image_path'] = $news->image_path;
        }
        
        
        unset($news_form['image']);
        unset($news_form['remove']);
        unset($news_form['_token']);

        
        // 該当するデータを上書きして保存する
        $news->fill($news_form)->save();
        
        return redirect('admin/news');
    }
}
