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

        /* Glassmorphism Forgot Password Box */
        .forgot-password-box {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(12px);
            border-radius: 15px;
            padding: 40px;
            width: 380px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .forgot-password-box h2 {
            color: #fff;
            font-size: 26px;
            font-weight: bold;
            margin-bottom: 25px;
        }

        /* Text */
        .text {
            color: #fff;
            font-size: 16px;
            margin-bottom: 20px;
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

        /* Submit Button */
        .submit-btn {
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

        .submit-btn:hover {
            background: linear-gradient(90deg, #dd2476, #ff512f);
        }

        /* Back to Login Link */
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
        <div class="forgot-password-box">
            <h2>🔑 Forgot Password</h2>

            <div class="text">
                {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Email Address -->
                <div class="input-group">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm" />
                </div>

                <!-- Submit Button -->
                <button type="submit" class="submit-btn">
                    {{ __('Email Password Reset Link') }}
                </button>

                <!-- Back to Login Link -->
                <a href="{{ route('login') }}" class="login-link">🔙 Back to Login</a>
            </form>
        </div>
    </div>
</x-guest-layout>
