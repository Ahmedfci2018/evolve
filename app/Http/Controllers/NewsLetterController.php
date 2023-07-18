<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Newsletter;
use Redirect;

class NewsLetterController extends Controller
{
    public function create()
    {
        return view('newsletter');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subscribed_email' => 'required|email'
        ]);
        try{
            if(Newsletter::isSubscribed($request->subscribed_email))
            return Redirect::back()->withErrors(['msg' => 'Email already subscribed']);
            else{
            Newsletter::subscribe($request->subscribed_email);
            return Redirect::back()->withErrors(['msg' => 'Email subscribed']);

    }
        }
        catch (\Exception $e){
            return redirect()->back()->with('error',$e->getMessage());

        }
    }
}