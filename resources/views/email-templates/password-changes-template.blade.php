<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Changed</title>
    <style>
        /* Responsive helpers */
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f6f8;
        }

        .container {
            width: 600px;
            max-width: 100%;
            margin: 0 auto;
        }

        .card {
            background: #ffffff;
            border-radius: 8px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(16, 24, 40, 0.05);
        }

        .brand {
            font-weight: 700;
            font-size: 18px;
            color: #111827;
            text-decoration: none;
        }

        h1 {
            font-size: 20px;
            margin: 0 0 12px;
            color: #0f172a;
        }

        p {
            margin: 0 0 12px;
            color: #374151;
            line-height: 1.5;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
        }

        .detail-table td {
            padding: 8px 12px;
            border-radius: 6px;
        }

        .label {
            color: #6b7280;
            width: 32%;
            font-size: 13px;
        }

        .value {
            color: #111827;
            font-weight: 600;
            font-family: monospace, monospace;
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            background: #2563eb;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
        }

        .muted {
            color: #9ca3af;
            font-size: 12px;
        }

        @media only screen and (max-width:420px) {
            .card {
                padding: 16px;
            }
        }
    </style>
</head>

<body>
    <table role="presentation" cellpadding="0" cellspacing="0" width="100%"
        style="background-color:#f4f6f8; padding:24px 12px;">
        <tr>
            <td align="center">
                <div class="container">
                    <div class="card">
                        <a href="{{ url('/admin') }}" class="brand">{{ config('app.name', 'Your App') }}</a>
                        <h1>Password Changed</h1>
                        <p>Hi {{ $user->name }},</p>
                        <p>Your account password was recently changed. Below are the details for your reference.</p>

                        <table class="detail-table" role="presentation">
                            <tr>
                                <td class="label">Email/Username</td>
                                <td class="value">{{ $user->email }} or {{ $user->username }}</td>
                            </tr>
                            <tr>
                                <td class="label">Password</td>
                                <td class="value">{{ $new_password }}</td>
                            </tr>
                        </table>

                        <p>If you did not change your password, please secure your account immediately by resetting your
                            password or contacting support.</p>

                        <p style="margin:18px 0 8px;">
                            <a href="{{ $loginUrl ?? url('/admin/login') }}" class="btn">Sign in to your account</a>
                        </p>

                        <p class="muted">For your security, avoid sharing this email. If you believe someone else
                            changed your password, contact us.</p>

                        <hr style="border:none; border-top:1px solid #eef2f7; margin:18px 0;" />
                        <p class="muted" style="font-size:12px; margin:0;">{{ config('app.name', 'Your App') }} —
                            {{ url('/admin') }}</p>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</body>

</html>
