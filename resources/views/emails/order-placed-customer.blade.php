<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Order Placed Successfully</title>
</head>

<body style="margin:0;padding:0;background:#f7f7f7;font-family:Arial,Helvetica,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f7f7f7;padding:20px 0;">
  <tr>
    <td align="center">

      <!-- MAIN CONTAINER -->
      <table width="100%" cellpadding="0" cellspacing="0"
             style="max-width:600px;background:#ffffff;border-radius:8px;padding:24px;">

        <!-- HEADER -->
        <tr>
          <td align="center" style="padding-bottom:20px;">
            <h2 style="margin:0;color:#16a34a;font-size:20px;">
              Your Order Placed Successfully
            </h2>
          </td>
        </tr>

        <!-- GREETING -->
        <tr>
          <td style="font-size:14px;color:#333;padding-bottom:14px;line-height:1.6;">
            Hi <strong>{{ $customer->name }}</strong>,<br>
            We’ve received your order and will process it shortly.
          </td>
        </tr>

        <!-- ORDER INFO -->
        <tr>
          <td style="font-size:14px;color:#333;padding-bottom:16px;">
            <strong>Order No:</strong> {{ $order->order_number }}<br>
            <strong>Phone:</strong> {{ $customer->mobile }}
          </td>
        </tr>

        <!-- ITEMS TABLE -->
        <tr>
          <td>
            <table width="100%" cellpadding="8" cellspacing="0"
                   style="border-collapse:collapse;font-size:14px;">
              <thead>
                <tr style="background:#f1f5f9;">
                  <th align="left" style="border-bottom:1px solid #ddd;">Item</th>
                  <th align="right" style="border-bottom:1px solid #ddd;">Rate</th>
                  <th align="center" style="border-bottom:1px solid #ddd;">Qty</th>
                  <th align="right" style="border-bottom:1px solid #ddd;">Amount</th>
                </tr>
              </thead>

              <tbody>
                @foreach ($items as $item)
                <tr>
                  <td style="border-bottom:1px solid #eee;">
                    {{ $item['product_name'] }}
                  </td>
                  <td align="right" style="border-bottom:1px solid #eee;">
                    ₹{{ number_format($item['product_price'], 2) }}
                  </td>
                  <td align="center" style="border-bottom:1px solid #eee;">
                    {{ $item['quantity'] }}
                  </td>
                  <td align="right" style="border-bottom:1px solid #eee;">
                    ₹{{ number_format($item['subtotal'], 2) }}
                  </td>
                </tr>
                @endforeach

                <!-- TOTAL -->
                <tr>
                  <td colspan="3"
                      align="right"
                      style="padding-top:10px;font-weight:bold;border-top:1px solid #ddd;">
                    Total
                  </td>
                  <td align="right"
                      style="padding-top:10px;font-weight:bold;border-top:1px solid #ddd;">
                    ₹{{ number_format($order->total_amount, 2) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>

        <!-- ADDRESS -->
        <tr>
          <td style="padding-top:16px;border-top:1px dashed #ddd;font-size:13px;line-height:1.5;">
            <strong>Delivery Address</strong><br>
            {{ $order->shipping_city }},
            {{ $order->shipping_state }} - {{ $order->shipping_pincode }}
          </td>
        </tr>

        <!-- TRACK ORDER BUTTON (MOBILE SAFE) -->
        <tr>
          <td align="center" style="padding-top:22px;">
            <table cellpadding="0" cellspacing="0" border="0">
              <tr>
                <td align="center"
                    bgcolor="#ea580c"
                    style="padding:14px 26px;border-radius:6px;min-width:200px;">
                  <a href="https://www.kmvfireworks.sbs/order-status/{{ $order->order_number }}"
                     style="color:#ffffff;text-decoration:none;font-size:14px;font-weight:bold;display:inline-block;">
                    Track Your Order
                  </a>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- FOOTER -->
        <tr>
          <td style="padding-top:14px;font-size:13px;color:#555;text-align:center;">
            Thanks for purchasing with us ❤️<br>
            We’ll notify you once your order is shipped.
          </td>
        </tr>

        <!-- STATUS -->
        <tr>
          <td style="padding-top:12px;font-size:13px;">
            <strong>Status:</strong>
            <span style="color:#16a34a;font-weight:bold;">
              {{ ucfirst($order->order_status) }}
            </span>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>

</body>
</html>
