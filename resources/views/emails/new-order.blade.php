<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Order</title>
</head>
<body style="margin:0;padding:0;background:#f7f7f7;font-family:Arial,Helvetica,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f7f7f7;padding:20px 0;">
    <tr>
        <td align="center">

            <!-- MAIN CONTAINER -->
            <table width="600" cellpadding="0" cellspacing="0"
                   style="background:#ffffff;border-radius:8px;padding:24px;">

                <!-- HEADER -->
                <tr>
                    <td style="padding-bottom:16px;">
                        <h2 style="margin:0;color:#111;font-size:20px;">
                            New Order Received
                        </h2>
                    </td>
                </tr>

                <!-- ORDER INFO -->
                <tr>
                    <td style="font-size:14px;color:#333;padding-bottom:14px;line-height:1.6;">
                        <strong>Order No:</strong> {{ $order->order_number }}<br>
                        <strong>Customer Name:</strong> {{ $customer->name }}<br>
                        <strong>Phone:</strong> {{ $customer->mobile }}
                    </td>
                </tr>

                <!-- ITEMS + TOTAL TABLE -->
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

                            <!-- TOTAL ROW (Perfectly aligned) -->
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

                <!-- SHIPPING ADDRESS -->
                <tr>
                    <td style="padding-top:16px;border-top:1px dashed #ddd;font-size:13px;line-height:1.5;">
                        <strong>Shipping Address</strong><br>
                        {{ $order->shipping_city }},
                        {{ $order->shipping_state }} - {{ $order->shipping_pincode }}
                    </td>
                </tr>

                <!-- STATUS -->
                <tr>
                    <td style="padding-top:12px;font-size:13px;">
                        <strong>Status:</strong>
                        <span style="color:green;font-weight:bold;">
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
