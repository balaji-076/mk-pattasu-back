composer require intervention/image

for image optimize

php artisan config:publish cors     //to fix cors error

php artisan make:mail NewOrderPlacedMail


composer require sendinblue/api-v3-sdk

TRUNCATE TABLE users RESTART IDENTITY;


OTP brute-force lock


//telegram setup

search,
/start
/newbot
Bot name: My Shop Orders
Username: my_shop_orders_bot (must end with bot)
BotFather உங்களுக்கு BOT TOKEN குடுக்கும் 123456789:AAHsdjshdjshdjshdjshdjsh


public function store(Request $request)
    {
        $validated = $request->validate([
            'customer.name'     => 'required|string|max:255',
            'customer.phone'    => 'required|string|max:15',
            'customer.email'    => 'nullable|email',

            'customer.address'  => 'required|string',
            'customer.city'     => 'required|string',
            'customer.state'    => 'required|string',
            'customer.postcode' => 'required|string',

            'items'                 => 'required|array|min:1',
            'items.*.id'            => 'required|integer|exists:products,id',
            'items.*.name'          => 'required|string',
            'items.*.discount_rate' => 'required|numeric|min:0',
            'items.*.qty'           => 'required|integer|min:1',

            // 'shippingCost' => 'required|numeric|min:0',
            'total'        => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $cust = $validated['customer'];

            $customer = Customer::updateOrCreate(
                ['mobile' => $cust['phone']],
                [
                    'name'    => $cust['name'],
                    'email'   => $cust['email'] ?? null,
                    'address' => $cust['address'],
                    'city'    => $cust['city'],
                    'state'   => $cust['state'],
                    'pincode' => $cust['postcode'],
                ]
            );

            $order = Orders::create([
                'order_number'     => 'ORD-' . date('Y') . '-' . strtoupper(Str::random(6)),
                'customer_id'      => $customer->id,
                'shipping_address' => $cust['address'],
                'shipping_city'    => $cust['city'],
                'shipping_state'   => $cust['state'],
                'shipping_pincode' => $cust['postcode'],
                // 'shippingCost'     => $validated['shippingCost'],
                'total_amount'     => $validated['total'],
                'payment_status'   => 'pending',
                'order_status'     => 'placed',
            ]);

            $orderItems = [];

            foreach ($validated['items'] as $item) {
                $orderItems[] = [
                    'order_id'      => $order->id,
                    'product_id'    => $item['id'],
                    'product_name'  => $item['name'],
                    'product_price' => $item['discount_rate'],
                    'quantity'      => $item['qty'],
                    'subtotal'      => $item['discount_rate'] * $item['qty'],
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
            }

            OrderItem::insert($orderItems);

            DB::commit();

            /* =============================
               BREVO EMAIL
            ============================= */
            try {
                // Admin mail
                $this->brevo->send( config('mail.admin_order_email'), 'New Order Received',
                    view('emails.new-order', [
                        'order' => $order,
                        'items' => $orderItems,
                        'customer' => $customer,
                    ])->render()
                );

                // Customer mail
                // if ($customer->email) {$this->brevo->send( $customer->email, 'Order Placed Successfully',
                //         view('emails.order-placed-customer', [
                //             'order' => $order,
                //             'items' => $orderItems,
                //             'customer' => $customer,
                //         ])->render()
                //     );
                // }

                Log::info('Order mails sent via Brevo', [ 'order_id' => $order->id ]);
            } catch (\Throwable $e) {
                Log::error('Brevo mail failed', [ 'order_id' => $order->id, 'error' => $e->getMessage()]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Order placed successfully',
                'data' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'total' => $order->total_amount,
                ],
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Order failed', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to place order',
            ], 500);
        }
    } itha order store panra function ok ithu la admin naantha tha yenaku mattu telegram orders received like ipa irukka mail telegram la venum  ipa mail la <!DOCTYPE html>
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
                        {{ $order->shipping_address }}<br>
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
</html> ipdi irukku ,yenakum  content  and track link oda order msg venum in telegram so give the end to end set up implementation  
# mk-pattasu-back
# mk-pattasu-back
