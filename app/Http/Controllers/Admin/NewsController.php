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
    
    public function create(Request $request)
    {
        // Varidationを行う
        $this->validate($request, News::$rules);
        
        $news = new News; //newsのデータベース
        $form = $request->all();
        
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
}
