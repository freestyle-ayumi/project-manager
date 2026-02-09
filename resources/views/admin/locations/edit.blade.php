<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-base sm:text-lg text-gray-800 leading-tight">
            登録地編集: {{ $location->name }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden sm:rounded-lg p-6">
                <form action="{{ route('admin.locations.update', $location) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-2">
                        <!-- 登録地名 -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">登録地名<span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $location->name) }}" 
                                   class="block w-full py-1.5 border-gray-300 rounded-md focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            @error('name')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 緯度・経度（現在地取得ボタン付き） -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="latitude" class="block text-sm font-medium text-gray-700">緯度 (latitude) <span class="text-red-500">*</span></label>
                                <div class="relative rounded-md">
                                    <input type="number" step="any" name="latitude" id="latitude" value="{{ old('latitude', $location->latitude) }}" 
                                           class="block w-full py-1.5 border-gray-300 rounded-md focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                    @error('latitude')
                                        <p class="text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label for="longitude" class="block text-sm font-medium text-gray-700">経度 (longitude) <span class="text-red-500">*</span></label>
                                <div class="relative rounded-md">
                                    <input type="number" step="any" name="longitude" id="longitude" value="{{ old('longitude', $location->longitude) }}" 
                                           class="block w-full py-1.5 border-gray-300 rounded-md focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                    @error('longitude')
                                        <p class="text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- 現在地取得ボタン -->
                        <div class="mt-4">
                            <button type="button" id="get-current-location" class="inline-flex items-center pl-3 pr-4 py-1.5 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <svg class="w-5 h-5 mr-0.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                現在地を取得
                            </button>
                            <p class="text-sm text-gray-500">ボタンを押すと現在地の緯度・経度が自動入力されます（ブラウザの位置情報許可が必要です）。</p>
                        </div>

                        <!-- 許容距離 -->
                        <div>
                            <label for="allowed_radius" class="block text-sm font-medium text-gray-700">許容距離（メートル）<span class="text-red-500">*</span></label>
                            <input type="number" name="allowed_radius" id="allowed_radius" value="{{ old('allowed_radius', $location->allowed_radius) }}" min="1" max="5000" 
                                   class="block w-full py-1.5 border-gray-300 rounded-md focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            <p class="text-sm text-gray-500">この距離内の場合に打刻が可能になります（例: 100m）</p>
                            @error('allowed_radius')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end space-x-4">
                        <a href="{{ route('admin.locations.index') }}" class="items-center px-4 py-2 ms-4 text-xs bg-gray-600 border-transparent rounded-md hover:bg-gray-700 text-white font-semibold focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:ring-offset-1 transition ease-in-out duration-150">
                            キャンセル
                        </a>
                        <button type="submit" class="items-center px-4 py-2 ms-4 text-xs bg-blue-600 border-transparent rounded-md hover:bg-blue-700 text-white font-semibold focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:ring-offset-1 transition ease-in-out duration-150">
                            更新
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Flatpickrの日本語ローカライズ（ローカルファイル） -->
    <script src="{{ asset('js/flatpickr-ja.js') }}"></script>

    <!-- 現在地取得スクリプト -->
    <script>
        document.getElementById('get-current-location').addEventListener('click', function() {
            if (!navigator.geolocation) {
                alert('このブラウザでは位置情報が取得できません。');
                return;
            }

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude.toFixed(8);
                    const lng = position.coords.longitude.toFixed(8);
                    
                    document.getElementById('latitude').value = lat;
                    document.getElementById('longitude').value = lng;
                    
                    alert(`現在地を取得しました！\n緯度: ${lat}\n経度: ${lng}`);
                },
                function(error) {
                    let msg = '位置情報の取得に失敗しました。';
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            msg += '\n位置情報のアクセスが許可されていません。ブラウザ設定を確認してください。';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            msg += '\n位置情報が利用できません。';
                            break;
                        case error.TIMEOUT:
                            msg += '\n取得がタイムアウトしました。もう一度お試しください。';
                            break;
                        default:
                            msg += '\n不明なエラーです。';
                    }
                    alert(msg);
                },
                {
                    enableHighAccuracy: true,  // 高精度モード（GPS優先）
                    timeout: 15000,           // 15秒でタイムアウト
                    maximumAge: 0             // キャッシュを使わない（最新位置）
                }
            );
        });
    </script>
</x-app-layout>