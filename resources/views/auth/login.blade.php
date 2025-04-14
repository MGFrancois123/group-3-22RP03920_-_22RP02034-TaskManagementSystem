<x-guest-layout>
    <style>
        /* Fullscreen Background */
        body {
            background: url('https://source.unsplash.com/1600x900/?technology,workspace') no-repeat center center;
            background-size: cover;
            font-family: 'Poppins', sans-serif;
        }

        /* Overlay */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #21417c;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Glassmorphism Login Box */
        .login-box {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(12px);
            border-radius: 15px;
            padding: 40px;
            width: 380px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .login-box h2 {
            color: #fff;
            font-size: 26px;
            font-weight: bold;
            margin-bottom: 25px;
        }

        /* Input Group */
        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .input-group label {
            color: #fff;
            font-weight: bold;
            font-size: 14px;
            display: block;
            margin-bottom: 6px;
        }

        .input-group input {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            margin-top: 6px;
            outline: none;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            transition: 0.3s;
        }

        .input-group input:focus {
            background: rgba(255, 255, 255, 0.3);
            transition: 0.3s;
        }

        /* Remember Me */
        .remember-me {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            color: #fff;
            font-size: 14px;
        }

        .remember-me input {
            margin-right: 5px;
        }

        /* Login Button */
        .login-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(90deg, #ff512f, #dd2476);
            border: none;
            border-radius: 8px;
            color: #fff;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        .login-btn:hover {
            background: linear-gradient(90deg, #dd2476, #ff512f);
        }

        /* Links */
        .forgot-password, .register {
            color: #fff;
            font-size: 14px;
            text-decoration: none;
            display: block;
            margin-top: 10px;
        }

        .forgot-password:hover, .register:hover {
            text-decoration: underline;
        }
    </style>

    <div class="overlay">
        <div class="login-box">
            <h2>🔐 Login to Task Management</h2>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <div class="input-group">
                    <label for="email">📧 Email Address</label>
                    <input id="email" type="email" name="email" placeholder="Enter your email" required autofocus>
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm" />
                </div>

                <!-- Password -->
                <div class="input-group">
                    <label for="password">🔑 Password</label>
                    <input id="password" type="password" name="password" placeholder="Enter your password" required>
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-sm" />
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="remember-me">
                    <label>
                        <input type="checkbox" name="remember"> Remember Me
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-password">🔹 Forgot Password?</a>
                    @endif
                </div>

                <!-- Login Button -->
                <button type="submit" class="login-btn">🚀 Log In</button>

                <!-- Register Link -->
                <a href="{{ route('register') }}" class="register">🆕 Create an Account</a>
            </form>
        </div>
    </div>
</x-guest-layout>
