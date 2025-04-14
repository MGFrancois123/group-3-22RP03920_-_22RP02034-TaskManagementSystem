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

        /* Glassmorphism Register Box */
        .register-box {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(12px);
            border-radius: 15px;
            padding: 40px;
            width: 380px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .register-box h2 {
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

        /* Register Button */
        .register-btn {
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

        .register-btn:hover {
            background: linear-gradient(90deg, #dd2476, #ff512f);
        }

        /* Links */
        .login-link {
            color: #fff;
            font-size: 14px;
            text-decoration: none;
            display: block;
            margin-top: 10px;
        }

        .login-link:hover {
            text-decoration: underline;
        }
    </style>

    <div class="overlay">
        <div class="register-box">
            <h2>📝 Create an Account</h2>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name -->
                <div class="input-group">
                    <label for="name">📛 Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                    <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-500 text-sm" />
                </div>

                <!-- Email Address -->
                <div class="input-group">
                    <label for="email">📧 Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm" />
                </div>

                <!-- Password -->
                <div class="input-group">
                    <label for="password">🔑 Password</label>
                    <input id="password" type="password" name="password" required autocomplete="new-password">
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-sm" />
                </div>

                <!-- Confirm Password -->
                <div class="input-group">
                    <label for="password_confirmation">🔐 Confirm Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-500 text-sm" />
                </div>

                <!-- Register Button -->
                <button type="submit" class="register-btn">🚀 Register</button>

                <!-- Login Link -->
                <a href="{{ route('login') }}" class="login-link">🔙 Already registered? Login here</a>
            </form>
        </div>
    </div>
</x-guest-layout>
