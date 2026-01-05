<?php

namespace App\Http\Controllers;

use App\Models\ProjectKeywordFlag;
use Illuminate\Http\Request;

class ProjectKeywordFlagController extends Controller
{
    /**
     * キーワードフラグ一覧
     */
    public function index()
    {
        $keywordFlags = ProjectKeywordFlag::paginate(10);
        return view('keyword_flags.index', compact('keywordFlags'));
    }

    /**
     * 新規作成フォーム
     */
    public function create()
    {
        return view('keyword_flags.create');
    }

    /**
     * キーワードフラグ作成
     */
    public function store(Request $request)
    {
        $request->validate([
            'keyword' => 'required|string|max:255|unique:project_keyword_flags,keyword',
        ]);

        ProjectKeywordFlag::create([
            'keyword' => $request->keyword,
        ]);

        return redirect()->route('admin.keyword_flags.index')
                         ->with('success', 'キーワードを登録しました');
    }

    /**
     * 編集フォーム
     */
    public function edit(ProjectKeywordFlag $keywordFlag)
    {
        return view('keyword_flags.edit', compact('keywordFlag'));
    }

    /**
     * キーワードフラグ更新
     */
    public function update(Request $request, ProjectKeywordFlag $keywordFlag)
    {
        $request->validate([
            'keyword' => 'required|string|max:255|unique:project_keyword_flags,keyword,' . $keywordFlag->id,
        ]);

        $keywordFlag->update([
            'keyword' => $request->keyword,
        ]);

        return redirect()->route('admin.keyword_flags.index')
                         ->with('success', 'キーワードを更新しました');
    }

    /**
     * キーワードフラグ削除
     */
    public function destroy(ProjectKeywordFlag $keywordFlag)
    {
        $keywordFlag->delete();
        return redirect()->route('admin.keyword_flags.index')
                         ->with('success', 'キーワードを削除しました');
    }
}
