<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guess the Track - API Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .section { margin: 20px 0; padding: 20px; border: 1px solid #ddd; }
        button { margin: 5px; padding: 10px; }
        input, select { margin: 5px; padding: 8px; }
        pre { background: #f4f4f4; padding: 10px; overflow-x: auto; }
        .response { margin-top: 10px; }
    </style>
</head>
<body>
    <h1>Guess the Track - API Test Interface</h1>

    <div class="section">
        <h2>Authentication</h2>
        <input type="email" id="email" placeholder="Email" value="test@example.com">
        <input type="password" id="password" placeholder="Password" value="password">
        <button onclick="register()">Register</button>
        <button onclick="login()">Login</button>
        <button onclick="logout()">Logout</button>
        <div class="response" id="auth-response"></div>
    </div>

    <div class="section">
        <h2>Rooms</h2>
        <input type="text" id="room-name" placeholder="Room Name" value="My Game Room">
        <button onclick="createRoom()">Create Room</button>
        <button onclick="getRooms()">Get My Rooms</button>
        <br><br>
        <input type="email" id="invite-email" placeholder="User Email to Invite">
        <select id="invite-room-select">
            <option value="">Select Room</option>
        </select>
        <button onclick="inviteUser()">Invite User</button>
        <br><br>
        <select id="invitation-room-select">
            <option value="">Select Room</option>
        </select>
        <button onclick="acceptInvitation()">Accept Invitation</button>
        <button onclick="declineInvitation()">Decline Invitation</button>
        <div class="response" id="rooms-response"></div>
    </div>

    <div class="section">
        <h2>Game Settings</h2>
        <select id="room-select">
            <option value="">Select Room</option>
        </select>
        <input type="number" id="rounds" placeholder="Rounds" value="5">
        <input type="text" id="genre" placeholder="Genre" value="pop">
        <button onclick="createGameSetting()">Create Game Setting</button>
        <button onclick="getGameSettings()">Get Game Settings</button>
        <div class="response" id="settings-response"></div>
    </div>

    <div class="section">
        <h2>Songs</h2>
        <input type="text" id="title" placeholder="Title" value="Bohemian Rhapsody">
        <input type="text" id="singer" placeholder="Singer" value="Queen">
        <input type="number" id="year" placeholder="Year" value="1975">
        <button onclick="createSong()">Create Song</button>
        <button onclick="getSongs()">Get Songs</button>
        <div class="response" id="songs-response"></div>
    </div>

    <div class="section">
        <h2>Games</h2>
        <select id="setting-select">
            <option value="">Select Game Setting</option>
        </select>
        <button onclick="createGame()">Create Game</button>
        <button onclick="getGames()">Get Games</button>
        <button onclick="getGameScores()">Get Game Scores</button>
        <div class="response" id="games-response"></div>
    </div>

    <div class="section">
        <h2>Rounds</h2>
        <select id="game-select">
            <option value="">Select Game</option>
        </select>
        <select id="round-info-select">
            <option value="">Select Round Info</option>
        </select>
        <button onclick="createRound()">Create Round</button>
        <button onclick="getRounds()">Get Rounds</button>
        <button onclick="calculateRoundScores()">Calculate Scores</button>
        <div class="response" id="rounds-response"></div>
    </div>

    <div class="section">
        <h2>Round Infos</h2>
        <select id="correct-song-select">
            <option value="">Select Correct Song</option>
        </select>
        <button onclick="createRoundInfo()">Create Round Info</button>
        <button onclick="getRoundInfos()">Get Round Infos</button>
        <div class="response" id="round-infos-response"></div>
    </div>

    <div class="section">
        <h2>User Answers</h2>
        <select id="round-info-answer-select">
            <option value="">Select Round Info</option>
        </select>
        <select id="user-song-select">
            <option value="">Select User Song Guess</option>
        </select>
        <button onclick="createUserAnswer()">Submit Answer</button>
        <button onclick="getUserAnswers()">Get Answers</button>
        <div class="response" id="answers-response"></div>
    </div>

    <script>
        let token = localStorage.getItem('token') || '';
        const baseUrl = 'http://localhost:8000/api';

        function setAuthHeader(xhr) {
            if (token) {
                xhr.setRequestHeader('Authorization', `Bearer ${token}`);
            }
        }

        function displayResponse(elementId, data) {
            document.getElementById(elementId).innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
        }

        async function apiCall(method, endpoint, data = null) {
            const url = baseUrl + endpoint;
            const config = {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                }
            };

            if (token) {
                config.headers['Authorization'] = `Bearer ${token}`;
            }

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

        async function register() {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            const result = await apiCall('POST', '/register', {
                name: 'Test User',
                email: email,
                password: password,
                password_confirmation: password
            });

            displayResponse('auth-response', result);
        }

        async function login() {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            const result = await apiCall('POST', '/login', {
                email: email,
                password: password
            });

            if (result.data.token) {
                token = result.data.token;
                localStorage.setItem('token', token);
            }

            displayResponse('auth-response', result);
        }

        async function createGameSetting() {
            const roomId = document.getElementById('room-select').value;
            const rounds = document.getElementById('rounds').value;
            const genre = document.getElementById('genre').value;

            const result = await apiCall('POST', '/game-settings', {
                room_id: roomId,
                rounds_count: rounds,
                genre: genre
            });

            displayResponse('settings-response', result);
            if (result.status === 201) {
                getGameSettings();
            }
        }

        async function getGameSettings() {
            const result = await apiCall('GET', '/game-settings');
            displayResponse('settings-response', result);

            if (result.data && Array.isArray(result.data)) {
                const select = document.getElementById('setting-select');
                select.innerHTML = '<option value="">Select Game Setting</option>';
                result.data.forEach(setting => {
                    select.innerHTML += `<option value="${setting.id}">Setting ${setting.id} - ${setting.genre}</option>`;
                });
            }
        }

        async function createSong() {
            const title = document.getElementById('title').value;
            const singer = document.getElementById('singer').value;
            const year = document.getElementById('year').value;

            const result = await apiCall('POST', '/song-infos', {
                title: title,
                singer: singer,
                year: parseInt(year)
            });

            displayResponse('songs-response', result);
            if (result.status === 201) {
                getSongs();
            }
        }

        async function getSongs() {
            const result = await apiCall('GET', '/song-infos');
            displayResponse('songs-response', result);

            if (result.data && Array.isArray(result.data)) {
                const correctSelect = document.getElementById('correct-song-select');
                const userSelect = document.getElementById('user-song-select');

                correctSelect.innerHTML = '<option value="">Select Correct Song</option>';
                userSelect.innerHTML = '<option value="">Select User Song Guess</option>';

                result.data.forEach(song => {
                    const option = `<option value="${song.id}">${song.title} - ${song.singer}</option>`;
                    correctSelect.innerHTML += option;
                    userSelect.innerHTML += option;
                });
            }
        }

        async function createGame() {
            const settingId = document.getElementById('setting-select').value;

            const result = await apiCall('POST', '/games', {
                room_id: 1, // Assuming room 1 exists
                game_setting_id: settingId
            });

            displayResponse('games-response', result);
            if (result.status === 201) {
                getGames();
            }
        }

        async function getGames() {
            const result = await apiCall('GET', '/games');
            displayResponse('games-response', result);

            if (result.data && Array.isArray(result.data)) {
                const select = document.getElementById('game-select');
                select.innerHTML = '<option value="">Select Game</option>';
                result.data.forEach(game => {
                    select.innerHTML += `<option value="${game.id}">Game ${game.id}</option>`;
                });
            }
        }

        async function getGameScores() {
            const gameId = document.getElementById('game-select').value;
            if (!gameId) {
                displayResponse('games-response', { error: 'Select a game first' });
                return;
            }

            const result = await apiCall('GET', `/games/${gameId}/total-scores`);
            displayResponse('games-response', result);
        }

        async function createRoundInfo() {
            const songId = document.getElementById('correct-song-select').value;

            const result = await apiCall('POST', '/round-infos', {
                correct_song_info_id: songId
            });

            displayResponse('round-infos-response', result);
            if (result.status === 201) {
                getRoundInfos();
            }
        }

        async function getRoundInfos() {
            const result = await apiCall('GET', '/round-infos');
            displayResponse('round-infos-response', result);

            if (result.data && Array.isArray(result.data)) {
                const roundSelect = document.getElementById('round-info-select');
                const answerSelect = document.getElementById('round-info-answer-select');

                roundSelect.innerHTML = '<option value="">Select Round Info</option>';
                answerSelect.innerHTML = '<option value="">Select Round Info</option>';

                result.data.forEach(roundInfo => {
                    const option = `<option value="${roundInfo.id}">Round Info ${roundInfo.id}</option>`;
                    roundSelect.innerHTML += option;
                    answerSelect.innerHTML += option;
                });
            }
        }

        async function createRound() {
            const gameId = document.getElementById('game-select').value;
            const roundInfoId = document.getElementById('round-info-select').value;

            const result = await apiCall('POST', '/rounds', {
                game_id: gameId,
                round_info_id: roundInfoId
            });

            displayResponse('rounds-response', result);
            if (result.status === 201) {
                getRounds();
            }
        }

        async function getRounds() {
            const result = await apiCall('GET', '/rounds');
            displayResponse('rounds-response', result);
        }

        async function calculateRoundScores() {
            const roundId = document.getElementById('game-select').value; // Using game select for simplicity
            if (!roundId) {
                displayResponse('rounds-response', { error: 'Select a round first' });
                return;
            }

            const result = await apiCall('POST', `/rounds/${roundId}/calculate-scores`);
            displayResponse('rounds-response', result);
        }

        async function logout() {
            token = null;
            localStorage.removeItem('token');
            displayResponse('auth-response', { message: 'Logged out successfully' });
        }

        async function createRoom() {
            const name = document.getElementById('room-name').value;

            const result = await apiCall('POST', '/rooms', {
                name: name
            });

            displayResponse('rooms-response', result);
            if (result.status === 201) {
                getRooms();
            }
        }

        async function getRooms() {
            const result = await apiCall('GET', '/rooms');
            displayResponse('rooms-response', result);

            if (result.data && result.data.success) {
                const rooms = result.data.success;
                const inviteSelect = document.getElementById('invite-room-select');
                const invitationSelect = document.getElementById('invitation-room-select');
                const roomSelect = document.getElementById('room-select');

                inviteSelect.innerHTML = '<option value="">Select Room</option>';
                invitationSelect.innerHTML = '<option value="">Select Room</option>';
                roomSelect.innerHTML = '<option value="">Select Room</option>';

                rooms.forEach(room => {
                    const optionText = `${room.name} (${room.pivot?.status || 'owner'})`;
                    inviteSelect.innerHTML += `<option value="${room.id}">${optionText}</option>`;
                    invitationSelect.innerHTML += `<option value="${room.id}">${optionText}</option>`;
                    roomSelect.innerHTML += `<option value="${room.id}">${optionText}</option>`;
                });
            }
        }

        async function inviteUser() {
            const email = document.getElementById('invite-email').value;
            const roomId = document.getElementById('invite-room-select').value;

            if (!roomId) {
                displayResponse('rooms-response', { error: 'Select a room first' });
                return;
            }

            const result = await apiCall('POST', `/rooms/${roomId}/invite`, {
                email: email
            });

            displayResponse('rooms-response', result);
        }

        async function acceptInvitation() {
            const roomId = document.getElementById('invitation-room-select').value;

            if (!roomId) {
                displayResponse('rooms-response', { error: 'Select a room first' });
                return;
            }

            const result = await apiCall('POST', `/rooms/${roomId}/accept-invitation`);
            displayResponse('rooms-response', result);
            if (result.status === 200) {
                getRooms();
            }
        }

        async function declineInvitation() {
            const roomId = document.getElementById('invitation-room-select').value;

            if (!roomId) {
                displayResponse('rooms-response', { error: 'Select a room first' });
                return;
            }

            const result = await apiCall('POST', `/rooms/${roomId}/decline-invitation`);
            displayResponse('rooms-response', result);
            if (result.status === 200) {
                getRooms();
            }
        }
            });

            displayResponse('answers-response', result);
            if (result.status === 201) {
                getUserAnswers();
            }
        }

        async function getUserAnswers() {
            const result = await apiCall('GET', '/user-answers');
            displayResponse('answers-response', result);
        }

        // Load initial data
        window.onload = function() {
            const savedToken = localStorage.getItem('token');
            if (savedToken) {
                token = savedToken;
            }
            getRooms();
            getGameSettings();
            getSongs();
            getGames();
            getRoundInfos();
            getUserAnswers();
        };
    </script>
</body>
</html>