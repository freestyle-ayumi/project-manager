<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('プロジェクト詳細') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="font-bold text-xl mb-4">{{ $project->name }} の詳細</h3>

                    <div class="mb-4">
                        <p class="font-bold">プロジェクト名:</p>
                        <p>{{ $project->name }}</p>
                    </div>

                    <div class="mb-4">
                        <p class="font-bold">顧客名:</p>
                        <p>{{ $project->client->name ?? 'N/A' }}</p>
                    </div>

                    <div class="mb-4">
                        <p class="font-bold">担当者:</p>
                        <p>{{ $project->user->name ?? 'N/A' }}</p>
                    </div>

                    <div class="mb-4">
                        <p class="font-bold">ステータス:</p>
                        <p>
                            @if ($project->status)
                                <span class="badge status-{{ $project->status->id }}">
                                    {{ $project->status->name }}
                                </span>
                            @else
                                <span class="badge badge-secondary">ステータスなし</span>
                            @endif
                        </p>
                    </div>

                    <div class="mb-4">
                        <p class="font-bold">開始日:</p>
                        <p>{{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('Y/m/d') : 'N/A' }}</p>
                    </div>

                    <div class="mb-4">
                        <p class="font-bold">終了日:</p>
                        <p>{{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('Y/m/d') : 'N/A' }}</p>
                    </div>

                    <div class="mb-4">
                        <p class="font-bold">説明:</p>
                        <p>{{ $project->description ?? 'N/A' }}</p>
                    </div>

                    <div class="mt-6 flex space-x-4">
                        <a href="{{ route('projects.edit', $project) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('編集') }}
                        </a>
                        <a href="{{ route('projects.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('一覧に戻る') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>