<?php

namespace App\Http\Controllers;

use App\Listing;
use Auth;
use Validator;

use Illuminate\Http\Request;

class ListingsController extends Controller
{
    //コンストラクタ （このクラスが呼ばれると最初にこの処理をする）
    public function __construct()
    {
        // ログインしていなかったらログインページに遷移する（この処理を消すとログインしなくてもページを表示する）
        $this->middleware('auth');
    }
    
    public function index()
    {
    	// 条件：user_idが現在ログインしているユーザIDと一致している
        $listings = Listing::where('user_id', Auth::user()->id)
        	// 並び替え順：作成日の昇順
            ->orderBy('created_at', 'asc')
            ->get();
            
        // テンプレート「listing/index.blade.php」を表示します。
        //Listingモデルを介してデータベースからデータを取得したあと、「index.blade.php」にその値を渡しています。
        //受け渡し方はいくつかありますが、今回はview関数の第二引数を使って渡しています。参考URL
        // https://laraweb.net/knowledge/1345/
        return view('listing/index', ['listings' => $listings]);
    }
    
    public function new()
    {
         // テンプレート「listing/new.blade.php」を表示します。
        return view('listing/new');
        
    }
    
    public function store(Request $request)
    {
        //バリデーション（入力値チェック）
        // リスト名に対し「入力必須」と「255文字以下」かどうかのチェックを行っています。
        // https://laraweb.net/knowledge/2100/
        $validator = Validator::make($request->all() , ['list_name' => 'required|max:255', ]);

        //バリデーションの結果がエラーの場合
        if ($validator->fails())
        {
        	// 元の画面へ戻るよう画面遷移します。また戻る際、バリデーションエラーの内容とフォームに入力された値も渡すようにしています。
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        // Listingモデル作成
        $listings = new Listing;
        $listings->title = $request->list_name;
        $listings->user_id = Auth::user()->id;

        $listings->save();
        // 「/」 ルートにリダイレクト
        return redirect('/');
    }
    
    public function edit($listing_id)
    {
        $listing = Listing::find($listing_id);
         // テンプレート「listing/edit.blade.php」を表示します。
        return view('listing/edit', ['listing' => $listing]);
    }
    
    public function update(Request $request)
    {
        //バリデーション（入力値チェック）
        $validator = Validator::make($request->all() , ['list_name' => 'required|max:255', ]);

        //バリデーションの結果がエラーの場合
        if ($validator->fails())
        {
          return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        
        $listing = Listing::find($request->id);
        $listing->title = $request->list_name;
        $listing->save();
        return redirect('/');
    }
    
    public function destroy($listing_id)
    {
        $listing = Listing::find($listing_id);
        $listing->delete();
        return redirect('/');
    }
}
