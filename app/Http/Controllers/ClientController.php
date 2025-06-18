<?php

namespace App\Http\Controllers;

use App\Models\Client; // Client モデルのみ使用するはず

use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * クライアント一覧を表示する
     */
    public function index()
    {
        $clients = Client::all();
        return view('clients.index', compact('clients'));
    }
}