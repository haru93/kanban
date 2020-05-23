<?php

namespace App\Http\Controllers;

use Auth;
use App\Card;
use App\Listing;
use Validator;

use Illuminate\Http\Request;

class CardsController extends Controller
{
    //コンストラクタ （このクラスが呼ばれると最初にこの処理をする）
    public function __construct()
    {
        // ログインしていなかったらログインページに遷移する（この処理を消すとログインしなくてもページを表示する）
        $this->middleware('auth');
    }
    
    public function new ($listing_id)
    {
         // テンプレート「card/new.blade.php」を表示します。
        return view('card/new', ['listing_id' => $listing_id]);
        
    }

    public function store(Request $request)
    {
        //バリデーション（入力値チェック）
        $validator = Validator::make($request->all() , ['card_title' => 'required|max:255', 'card_memo' => 'required|max:255',]);

        //バリデーションの結果がエラーの場合
        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator->errors())->withInput();
            
        }

        // Card
        $cards = new Card;
        $cards->title = $request->card_title;
        $cards->listing_id = $request->listing_id;
        $cards->memo = $request->card_memo;

        $cards->save();
        // 「/」 ルートにリダイレクト
        return redirect('/');
    }

    public function show($listing_id, $card_id)
    {
        $listing = Listing::find($listing_id);
        $card = Card::find($card_id);
         // テンプレート「card/show.blade.php」を表示します。
        return view('card/show', ['listing' => $listing, 'card' => $card]);
    }

    public function edit($listing_id, $card_id)
    {
        $listings = Listing::where('user_id', Auth::user()->id)->get();
        $listing = Listing::find($listing_id);
        $card = Card::find($card_id);
         // テンプレート「card/edit.blade.php」を表示します。
        return view('card/edit', ['listings' => $listings, 'listing' => $listing, 'card' => $card]);
    }

    public function update(Request $request)
    {
        //バリデーション（入力値チェック）
        $validator = Validator::make($request->all() , ['card_title' => 'required|max:255', 'card_memo' => 'required|max:255',]);

        //バリデーションの結果がエラーの場合
        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        $card = Card::find($request->id);
        $card->title = $request->card_title;
        $card->memo = $request->card_memo;
        $card->listing_id = $request->listing_id;
        $card->save();
        // 「/」 ルートにリダイレクト
        return redirect('/');
    }

    public function destroy($listing_id, $card_id)
    {
        $card = Card::find($card_id);
        $card->delete();
        // 「/」 ルートにリダイレクト
        return redirect('/');
    }
}
