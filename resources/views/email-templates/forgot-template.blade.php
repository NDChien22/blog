<!DOCTYPE html>
<html>

<body style="font-family:Arial, sans-serif; background:#f5f5f5; padding:20px;">

    <table width="100%" cellpadding="0" cellspacing="0"
        style="max-width:500px; margin:auto; background:#ffffff; padding:20px; border-radius:6px;">
        <tr>
            <td style="font-size:16px; color:#333;">
                <p>Hello {{ $user->name }},</p>
                <p>Click the button below to reset your password:</p>
            </td>
        </tr>

        <tr>
            <td align="center" style="padding:20px 0;">
                <a href="{{ $actionlink }}" target="_blank"
                    style="background:#007bff; color:#ffffff; padding:12px 20px; text-decoration:none; border-radius:4px; font-weight:bold;">
                    Reset Password
                </a>
                <p>
                    This link is valid for 15 minutes.
                </p>
            </td>
        </tr>

        <tr>
            <td style="font-size:14px; color:#555;">
                <p>If the button doesn’t work, use this link:</p>
                <p style="word-break:break-all;">
                    <a href="{{ $actionlink }}" style="color:#007bff;">{{ $actionlink }}</a>
                </p>
            </td>
        </tr>
    </table>

</body>

</html>
