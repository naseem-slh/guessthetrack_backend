<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guess the Track - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .response-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1rem;
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
            white-space: pre-wrap;
            max-height: 300px;
            overflow-y: auto;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold text-center mb-6 text-gray-800">Guess the Track</h1>

            <!-- Login Form -->
            <div id="login-form">
                <h2 class="text-xl font-semibold mb-4 text-gray-700">Login</h2>
                <form onsubmit="login(event)">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="login-email">
                            Email
                        </label>
                        <input
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            id="login-email"
                            type="email"
                            placeholder="Enter your email"
                            required
                        >
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="login-password">
                            Password
                        </label>
                        <input
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            id="login-password"
                            type="password"
                            placeholder="Enter your password"
                            required
                        >
                    </div>
                    <div class="flex items-center justify-between">
                        <button
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                            type="submit"
                        >
                            Login
                        </button>
                        <button
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                            type="button"
                            onclick="showRegister()"
                        >
                            Register Instead
                        </button>
                    </div>
                </form>
            </div>

            <!-- Register Form -->
            <div id="register-form" class="hidden">
                <h2 class="text-xl font-semibold mb-4 text-gray-700">Register</h2>
                <form onsubmit="register(event)">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="register-name">
                            Name
                        </label>
                        <input
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            id="register-name"
                            type="text"
                            placeholder="Enter your name"
                            required
                        >
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="register-email">
                            Email
                        </label>
                        <input
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            id="register-email"
                            type="email"
                            placeholder="Enter your email"
                            required
                        >
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="register-password">
                            Password
                        </label>
                        <input
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            id="register-password"
                            type="password"
                            placeholder="Enter your password"
                            required
                        >
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="register-password-confirm">
                            Confirm Password
                        </label>
                        <input
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            id="register-password-confirm"
                            type="password"
                            placeholder="Confirm your password"
                            required
                        >
                    </div>
                    <div class="flex items-center justify-between">
                        <button
                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                            type="submit"
                        >
                            Register
                        </button>
                        <button
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                            type="button"
                            onclick="showLogin()"
                        >
                            Login Instead
                        </button>
                    </div>
                </form>
            </div>

            <!-- Response Display -->
            <div class="mt-6">
                <h3 class="text-lg font-semibold mb-2 text-gray-700">API Response:</h3>
                <div id="response-display" class="response-box">
                    No response yet...
                </div>
            </div>
        </div>
    </div>

    <script>
        const baseUrl = window.location.origin + '/api';
        let token = localStorage.getItem('auth_token');

        // Check if already logged in
        if (token) {
            window.location.href = '/dashboard';
        }

        function showRegister() {
            document.getElementById('login-form').classList.add('hidden');
            document.getElementById('register-form').classList.remove('hidden');
        }

        function showLogin() {
            document.getElementById('register-form').classList.add('hidden');
            document.getElementById('login-form').classList.remove('hidden');
        }

        function displayResponse(response) {
            const responseDiv = document.getElementById('response-display');
            responseDiv.textContent = JSON.stringify(response, null, 2);
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

        async function register(event) {
            event.preventDefault();

            const name = document.getElementById('register-name').value;
            const email = document.getElementById('register-email').value;
            const password = document.getElementById('register-password').value;
            const passwordConfirm = document.getElementById('register-password-confirm').value;

            if (password !== passwordConfirm) {
                displayResponse({ error: 'Passwords do not match' });
                return;
            }

            const result = await apiCall('POST', '/register', {
                name: name,
                email: email,
                password: password,
                password_confirmation: password
            });

            displayResponse(result);

            if (result.status === 200 && result.data.success && result.data.data.token) {
                token = result.data.data.token;
                localStorage.setItem('auth_token', token);
                
                // Get user info and store user ID
                const userResult = await apiCall('GET', '/me');
                if (userResult.status === 200) {
                    localStorage.setItem('user_id', userResult.data.id);
                }
                
                setTimeout(() => {
                    window.location.href = '/dashboard';
                }, 2000);
            }
        }

        async function login(event) {
            event.preventDefault();

            const email = document.getElementById('login-email').value;
            const password = document.getElementById('login-password').value;

            const result = await apiCall('POST', '/login', {
                email: email,
                password: password
            });

            displayResponse(result);

            if (result.status === 200 && result.data.success && result.data.data.token) {
                token = result.data.data.token;
                localStorage.setItem('auth_token', token);
                
                // Get user info and store user ID
                const userResult = await apiCall('GET', '/me');
                if (userResult.status === 200) {
                    localStorage.setItem('user_id', userResult.data.id);
                }
                
                setTimeout(() => {
                    window.location.href = '/dashboard';
                }, 2000);
            }
        }
    </script>
</body>
</html>