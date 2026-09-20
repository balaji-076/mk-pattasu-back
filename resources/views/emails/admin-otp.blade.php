<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Password Verification OTP</title>
</head>

<body style="
  margin:0;
  padding:0;
  background-color:#f6f6f6;
  font-family: Arial, Helvetica, sans-serif;
">

<table width="100%" cellpadding="0" cellspacing="0" style="padding:16px;">
  <tr>
    <td align="center">

      <!-- MAIN CARD -->
      <table width="100%" cellpadding="0" cellspacing="0" style="
        max-width:480px;
        background:#ffffff;
        border-radius:8px;
        padding:24px;
      ">

        <!-- HEADER -->
        <tr>
          <td align="center">
            <h2 style="
              margin:0;
              font-size:20px;
              color:#111;
            ">
              KMV Traders & Fireworks
            </h2>

            <p style="
              margin:8px 0 20px;
              font-size:14px;
              color:#555;
            ">
              Password Verification Code
            </p>
          </td>
        </tr>

        <!-- BODY -->
        <tr>
          <td align="center">

            <p style="
              font-size:15px;
              color:#333;
              margin-bottom:16px;
            ">
              Use the OTP below to verify your identity and set your password
            </p>

            <!-- OTP BOX -->
            <div style="
              display:inline-block;
              padding:14px 24px;
              font-size:32px;
              font-weight:bold;
              letter-spacing:8px;
              background:#f2f2f2;
              color:#000;
              border-radius:6px;
              margin-bottom:16px;
            ">
              {{ $otp }}
            </div>

            <p style="
              font-size:13px;
              color:#666;
              margin:0;
            ">
              This OTP is valid for <strong>2 minutes</strong>
            </p>

          </td>
        </tr>

        <!-- DIVIDER -->
        <tr>
          <td style="padding:20px 0;">
            <hr style="border:none; border-top:1px solid #eee;">
          </td>
        </tr>

        <!-- FOOTER -->
        <tr>
          <td align="center" style="
            font-size:12px;
            color:#777;
          ">
            <p style="margin:6px 0;">
              Do not share this OTP with anyone
            </p>
            <p style="margin:0;">
              If you did not request this, please ignore this email
            </p>
          </td>
        </tr>

      </table>

      <!-- COPYRIGHT -->
      <p style="
        font-size:11px;
        color:#999;
        margin-top:12px;
        text-align:center;
      ">
        © {{ date('Y') }} KMV Traders & Fireworks. All rights reserved.
      </p>

    </td>
  </tr>
</table>

</body>
</html>
