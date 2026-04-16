<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guess the Track - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .data-card {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .status-accepted { background: #d1fae5; color: #065f46; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-owner { background: #dbeafe; color: #1e40af; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-800">Guess the Track Dashboard</h1>
                <div class="flex space-x-4">
                    <button onclick="logout()" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        Logout
                    </button>
                    <a href="/test" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-block">
                        API Test Interface
                    </a>
                </div>
            </div>
            <div id="user-info" class="mt-4 text-gray-600">
                Loading user information...
            </div>
        </div>

        <!-- Loading Indicator -->
        <div id="loading" class="text-center py-8">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
            <p class="mt-2 text-gray-600">Loading your data...</p>
        </div>

        <!-- Error Message -->
        <div id="error-message" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 hidden">
            <strong class="font-bold">Error:</strong>
            <span class="block sm:inline" id="error-text"></span>
        </div>

        <!-- Rooms Section -->
        <div class="data-card">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-gray-800">My Rooms</h2>
                <button onclick="showCreateRoomModal()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Create Room
                </button>
            </div>
            <div id="rooms-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Rooms will be loaded here -->
            </div>
        </div>

        <!-- Game Settings Section -->
        <div class="data-card">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-gray-800">Game Settings</h2>
                <button onclick="showCreateGameSettingModal()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Create Game Setting
                </button>
            </div>
            <div id="game-settings-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Game settings will be loaded here -->
            </div>
        </div>

        <!-- Games Section -->
        <div class="data-card">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-gray-800">Games</h2>
                <button onclick="showCreateGameModal()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Create Game
                </button>
            </div>
            <div id="games-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Games will be loaded here -->
            </div>
        </div>

        <!-- Songs Section -->
        <div class="data-card">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-gray-800">Songs</h2>
                <button onclick="showCreateSongModal()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Add Song
                </button>
            </div>
            <div id="songs-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Songs will be loaded here -->
            </div>
        </div>

        <!-- Rounds Section -->
        <div class="data-card">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Rounds</h2>
            <div id="rounds-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Rounds will be loaded here -->
            </div>
        </div>

        <!-- User Answers Section -->
        <div class="data-card">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">My Answers</h2>
            <div id="user-answers-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- User answers will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Create Room Modal -->
    <div id="create-room-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Create New Room</h3>
                <form onsubmit="createRoom(event)">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Room Name</label>
                        <input type="text" id="room-name" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeModal('create-room-modal')" class="px-4 py-2 bg-gray-500 text-white rounded-md">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Create Game Setting Modal -->
    <div id="create-game-setting-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Create Game Setting</h3>
                <form onsubmit="createGameSetting(event)">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Room</label>
                        <select id="game-setting-room" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                            <option value="">Select Room</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Rounds Count</label>
                        <input type="number" id="rounds-count" class="w-full px-3 py-2 border border-gray-300 rounded-md" min="1" value="5" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Genre</label>
                        <input type="text" id="genre" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="pop, rock, etc." required>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeModal('create-game-setting-modal')" class="px-4 py-2 bg-gray-500 text-white rounded-md">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Create Game Modal -->
    <div id="create-game-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Create New Game</h3>
                <form onsubmit="createGame(event)">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Room</label>
                        <select id="game-room" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                            <option value="">Select Room</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Game Setting</label>
                        <select id="game-setting" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                            <option value="">Select Game Setting</option>
                        </select>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeModal('create-game-modal')" class="px-4 py-2 bg-gray-500 text-white rounded-md">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Create Song Modal -->
    <div id="create-song-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Add New Song</h3>
                <form onsubmit="createSong(event)">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Title</label>
                        <input type="text" id="song-title" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Singer</label>
                        <input type="text" id="song-singer" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Year</label>
                        <input type="number" id="song-year" class="w-full px-3 py-2 border border-gray-300 rounded-md" min="1900" max="2030" required>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeModal('create-song-modal')" class="px-4 py-2 bg-gray-500 text-white rounded-md">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md">Add Song</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const baseUrl = 'http://localhost:8000/api';
        let token = localStorage.getItem('auth_token');
        let userData = {};

        // Check authentication
        if (!token) {
            window.location.href = '/login';
        }

        async function apiCall(method, endpoint, data = null) {
            const url = baseUrl + endpoint;
            const config = {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            };

            if (data) {
                config.body = JSON.stringify(data);
            }

            try {
                const response = await fetch(url, config);
                const result = await response.json();
                return { status: response.status, data: result };
            } catch (error) {
                return { status: 0, data: { error: error.message } };
            }
        }

        function logout() {
            localStorage.removeItem('auth_token');
            window.location.href = '/login';
        }

        function showCreateRoomModal() {
            document.getElementById('create-room-modal').classList.remove('hidden');
        }

        function showCreateGameSettingModal() {
            loadRoomsForSelect('game-setting-room');
            document.getElementById('create-game-setting-modal').classList.remove('hidden');
        }

        function showCreateGameModal() {
            loadRoomsForSelect('game-room');
            loadGameSettingsForSelect();
            document.getElementById('create-game-modal').classList.remove('hidden');
        }

        function showCreateSongModal() {
            document.getElementById('create-song-modal').classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        async function createRoom(event) {
            event.preventDefault();
            const name = document.getElementById('room-name').value;

            const result = await apiCall('POST', '/rooms', { name });
            if (result.status === 201) {
                closeModal('create-room-modal');
                loadDashboardData();
            } else {
                alert('Error creating room: ' + JSON.stringify(result.data));
            }
        }

        async function createGameSetting(event) {
            event.preventDefault();
            const roomId = document.getElementById('game-setting-room').value;
            const roundsCount = document.getElementById('rounds-count').value;
            const genre = document.getElementById('genre').value;

            const result = await apiCall('POST', '/game-settings', {
                room_id: roomId,
                rounds_count: roundsCount,
                genre: genre
            });

            if (result.status === 201) {
                closeModal('create-game-setting-modal');
                loadDashboardData();
            } else {
                alert('Error creating game setting: ' + JSON.stringify(result.data));
            }
        }

        async function createGame(event) {
            event.preventDefault();
            const roomId = document.getElementById('game-room').value;
            const gameSettingId = document.getElementById('game-setting').value;

            const result = await apiCall('POST', '/games', {
                room_id: roomId,
                game_setting_id: gameSettingId
            });

            if (result.status === 201) {
                closeModal('create-game-modal');
                loadDashboardData();
            } else {
                alert('Error creating game: ' + JSON.stringify(result.data));
            }
        }

        async function createSong(event) {
            event.preventDefault();
            const title = document.getElementById('song-title').value;
            const singer = document.getElementById('song-singer').value;
            const year = document.getElementById('song-year').value;

            const result = await apiCall('POST', '/song-infos', {
                title, singer, year
            });

            if (result.status === 201) {
                closeModal('create-song-modal');
                loadDashboardData();
            } else {
                alert('Error adding song: ' + JSON.stringify(result.data));
            }
        }

        async function loadRoomsForSelect(selectId) {
            const result = await apiCall('GET', '/rooms');
            const select = document.getElementById(selectId);
            select.innerHTML = '<option value="">Select Room</option>';

            if (result.status === 200) {
                const roomsData = result.data?.success || result.data || [];
                roomsData.forEach(room => {
                    select.innerHTML += `<option value="${room.id}">${room.name}</option>`;
                });
            }
        }

        async function loadGameSettingsForSelect() {
            const result = await apiCall('GET', '/game-settings');
            const select = document.getElementById('game-setting');
            select.innerHTML = '<option value="">Select Game Setting</option>';

            if (result.status === 200) {
                const settingsData = result.data?.success || result.data || [];
                settingsData.forEach(setting => {
                    select.innerHTML += `<option value="${setting.id}">Setting ${setting.id} - ${setting.genre}</option>`;
                });
            }
        }

        function renderRooms(rooms) {
            const container = document.getElementById('rooms-list');
            container.innerHTML = '';

            if (!rooms || rooms.length === 0) {
                container.innerHTML = '<p class="text-gray-500 col-span-full">No rooms found. Create your first room!</p>';
                return;
            }

            rooms.forEach(room => {
                const statusClass = room.pivot?.role === 'owner' ? 'status-owner' :
                                  room.pivot?.status === 'accepted' ? 'status-accepted' : 'status-pending';
                const statusText = room.pivot?.role === 'owner' ? 'Owner' :
                                 room.pivot?.status === 'accepted' ? 'Member' : 'Pending';

                container.innerHTML += `
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-lg">${room.name}</h3>
                        <p class="text-sm text-gray-600">Created by: ${room.creator?.name || 'Unknown'}</p>
                        <span class="status-badge ${statusClass}">${statusText}</span>
                        <div class="mt-2">
                            <button onclick="viewRoom(${room.id})" class="text-blue-500 hover:text-blue-700 text-sm">View Details</button>
                        </div>
                    </div>
                `;
            });
        }

        function renderGameSettings(settings) {
            const container = document.getElementById('game-settings-list');
            container.innerHTML = '';

            if (!settings || settings.length === 0) {
                container.innerHTML = '<p class="text-gray-500 col-span-full">No game settings found.</p>';
                return;
            }

            settings.forEach(setting => {
                container.innerHTML += `
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-semibold">Game Setting #${setting.id}</h3>
                        <p class="text-sm text-gray-600">Room: ${setting.room?.name || 'Unknown'}</p>
                        <p class="text-sm">Rounds: ${setting.rounds_count}</p>
                        <p class="text-sm">Genre: ${setting.genre}</p>
                    </div>
                `;
            });
        }

        function renderGames(games) {
            const container = document.getElementById('games-list');
            container.innerHTML = '';

            if (!games || games.length === 0) {
                container.innerHTML = '<p class="text-gray-500 col-span-full">No games found.</p>';
                return;
            }

            games.forEach(game => {
                container.innerHTML += `
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-semibold">Game #${game.id}</h3>
                        <p class="text-sm text-gray-600">Room: ${game.room?.name || 'Unknown'}</p>
                        <p class="text-sm">Rounds: ${game.game_setting?.rounds_count || 'N/A'}</p>
                        <p class="text-sm">Genre: ${game.game_setting?.genre || 'N/A'}</p>
                    </div>
                `;
            });
        }

        function renderSongs(songs) {
            const container = document.getElementById('songs-list');
            container.innerHTML = '';

            if (!songs || songs.length === 0) {
                container.innerHTML = '<p class="text-gray-500 col-span-full">No songs found.</p>';
                return;
            }

            songs.forEach(song => {
                container.innerHTML += `
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-semibold">${song.title}</h3>
                        <p class="text-sm text-gray-600">By ${song.singer}</p>
                        <p class="text-sm">${song.year}</p>
                    </div>
                `;
            });
        }

        function renderRounds(rounds) {
            const container = document.getElementById('rounds-list');
            container.innerHTML = '';

            if (!rounds || rounds.length === 0) {
                container.innerHTML = '<p class="text-gray-500 col-span-full">No rounds found.</p>';
                return;
            }

            rounds.forEach(round => {
                container.innerHTML += `
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-semibold">Round #${round.id}</h3>
                        <p class="text-sm text-gray-600">Game: ${round.game?.id || 'Unknown'}</p>
                        <p class="text-sm">Round Number: ${round.round_number}</p>
                    </div>
                `;
            });
        }

        function renderUserAnswers(answers) {
            const container = document.getElementById('user-answers-list');
            container.innerHTML = '';

            if (!answers || answers.length === 0) {
                container.innerHTML = '<p class="text-gray-500 col-span-full">No answers found.</p>';
                return;
            }

            answers.forEach(answer => {
                container.innerHTML += `
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-semibold">Answer #${answer.id}</h3>
                        <p class="text-sm text-gray-600">Round Info: ${answer.round_info_id}</p>
                        <p class="text-sm">Song: ${answer.song_info?.title || 'Unknown'}</p>
                    </div>
                `;
            });
        }

        async function loadDashboardData() {
            document.getElementById('loading').classList.remove('hidden');
            document.getElementById('error-message').classList.add('hidden');

            try {
                // Load all data in parallel
                const [roomsResult, gameSettingsResult, gamesResult, songsResult, roundsResult, userAnswersResult] = await Promise.all([
                    apiCall('GET', '/rooms'),
                    apiCall('GET', '/game-settings'),
                    apiCall('GET', '/games'),
                    apiCall('GET', '/song-infos'),
                    apiCall('GET', '/rounds'),
                    apiCall('GET', '/user-answers')
                ]);

                // Check for authentication errors
                if (roomsResult.status === 401) {
                    logout();
                    return;
                }

                // Render data - handle both response formats
                const roomsData = roomsResult.data?.success || roomsResult.data || [];
                const gameSettingsData = gameSettingsResult.data?.success || gameSettingsResult.data || [];
                const gamesData = gamesResult.data?.success || gamesResult.data || [];
                const songsData = songsResult.data?.success || songsResult.data || [];
                const roundsData = roundsResult.data?.success || roundsResult.data || [];
                const userAnswersData = userAnswersResult.data?.success || userAnswersResult.data || [];

                renderRooms(roomsData);
                renderGameSettings(gameSettingsData);
                renderGames(gamesData);
                renderSongs(songsData);
                renderRounds(roundsData);
                renderUserAnswers(userAnswersData);

                // Update user info
                if (roomsResult.data?.success?.length > 0) {
                    const userInfo = document.getElementById('user-info');
                    userInfo.textContent = `Welcome! You have ${roomsResult.data.success.length} rooms.`;
                }

            } catch (error) {
                document.getElementById('error-message').classList.remove('hidden');
                document.getElementById('error-text').textContent = error.message;
            } finally {
                document.getElementById('loading').classList.add('hidden');
            }
        }

        function viewRoom(roomId) {
            // Could implement room details view here
            alert(`Room details for room ${roomId} - Feature coming soon!`);
        }

        // Load data when page loads
        window.onload = loadDashboardData;
    </script>
</body>
</html>