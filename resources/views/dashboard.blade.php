<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ダッシュボード') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="mb-4">
                        こんにちは、<span class="font-bold text-blue-600">{{ Auth::user()->name }}</span> さん！
                    </p>
                    <p class="mb-6">
                        プロジェクト管理システムへようこそ。
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="bg-gray-100 p-4 rounded-lg shadow-sm">
                            <h3 class="font-semibold text-lg mb-2">今後のプロジェクト</h3>
                            <p class="text-sm text-gray-600">ここにプロジェクトの概要を表示します。</p>
                            </div>
                        <div class="bg-gray-100 p-4 rounded-lg shadow-sm">
                            <h3 class="font-semibold text-lg mb-2">未承認の経費</h3>
                            <p class="text-sm text-gray-600">ここに未承認の経費一覧を表示します。</p>
                            </div>
                        <div class="bg-gray-100 p-4 rounded-lg shadow-sm">
                            <h3 class="font-semibold text-lg mb-2">割り当てられたタスク</h3>
                            <p class="text-sm text-gray-600">ここにあなたが担当するタスク一覧を表示します。</p>
                            </div>
                    </div>
                    </div>
            </div>
        </div>
    </div>
</x-app-layout>