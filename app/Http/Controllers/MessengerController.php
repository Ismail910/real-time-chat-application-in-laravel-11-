<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessengerController extends Controller
{
    public function index($id = null)
    {
        $user = Auth::user();
        $friends = User::where('id', '<>', $user->id)->orderBy('name')->paginate();
        $chats = $user->conversations()->with([
            'lastMessage',
            'participants' => function ($query) use ($user) {
                $query->where('id', '<>', $user->id);
            }
        ])->get();
    
        $messages = collect();
    
        if ($id) {
            $chat = $chats->where('id', $id)->first();
            if ($chat) {
                $messages = $chat->messages()->with('user')->paginate();
            }
        }
    
        return view('messenger', compact('friends', 'user', 'chats', 'messages'));
    }
    
}
