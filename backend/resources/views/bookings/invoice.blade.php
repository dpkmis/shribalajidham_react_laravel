
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 6px; }
        th { background: #f5f5f5; }
        .text-right { text-align: right; }
        .no-border td { border: none; }
    </style>
</head>
<body>

<h2>{{ $booking['property']['name'] }}</h2>
<p>{{ $booking['property']['meta']['location'] }}</p>

<hr>

<table class="no-border">
    <tr>
        <td>
            <strong>Guest:</strong><br>
            {{ $booking['guest']['full_name'] }}<br>
            {{ $booking['guest']['phone'] }}<br>
            {{ $booking['guest']['email'] }}
        </td>
        <td class="text-right">
            <strong>Invoice</strong><br>
            Booking Ref: {{ $booking['booking_reference'] }}<br>
            Date: {{ \Carbon\Carbon::parse($booking['created_at'])->format('d M Y') }}<br>
            Status: {{ ucfirst($booking['payment_status']) }}
        </td>
    </tr>
</table>

---

<h4>Booking Details</h4>
<table>
    <tr>
        <th>Check-in</th>
        <th>Check-out</th>
        <th>Nights</th>
        <th>Source</th>
    </tr>
    <tr>
        <td>{{ \Carbon\Carbon::parse($booking['checkin_date'])->format('d M Y') }}</td>
        <td>{{ \Carbon\Carbon::parse($booking['checkout_date'])->format('d M Y') }}</td>
        <td>{{ $booking['nights'] }}</td>
        <td>{{ ucfirst($booking['source']) }}</td>
    </tr>
</table>

---

<h4>Room Charges</h4>
<table>
    <tr>
        <th>Room Type</th>
        <th>Nights</th>
        <th>Rate / Night</th>
        <th>Total</th>
    </tr>
    @foreach($booking['bookingRooms'] as $room)
    <tr>
        <td>{{ $room['room_type']['name'] }}</td>
        <td>{{ $room['nights'] }}</td>
        <td class="text-right">₹{{ number_format($room['rate_per_night_cents'] / 100, 2) }}</td>
        <td class="text-right">₹{{ number_format($room['final_rate_cents'] / 100, 2) }}</td>
    </tr>
    @endforeach
</table>

---

@if(count($booking['charges']) > 0)
<h4>Additional Charges</h4>
<table>
    <tr>
        <th>Description</th>
        <th>Qty</th>
        <th>Total</th>
    </tr>
    @foreach($booking['charges'] as $charge)
    <tr>
        <td>{{ $charge['description'] }}</td>
        <td>{{ $charge['quantity'] }}</td>
        <td class="text-right">₹{{ number_format($charge['total_cents'] / 100, 2) }}</td>
    </tr>
    @endforeach
</table>
@endif

---

<h4>Payment Summary</h4>
<table>
    <tr>
        <td>Room Charges</td>
        <td class="text-right">₹{{ number_format($booking['room_charges_cents'] / 100, 2) }}</td>
    </tr>
    <tr>
        <td>Additional Charges</td>
        <td class="text-right">₹{{ number_format($booking['additional_charges_cents'] / 100, 2) }}</td>
    </tr>
    <tr>
        <td>Discount</td>
        <td class="text-right">₹{{ number_format($booking['discount_amount_cents'] / 100, 2) }}</td>
    </tr>
    <tr>
        <td>Tax</td>
        <td class="text-right">₹{{ number_format($booking['tax_amount_cents'] / 100, 2) }}</td>
    </tr>
    <tr>
        <th>Total</th>
        <th class="text-right">₹{{ number_format($booking['total_amount'], 2) }}</th>
    </tr>
    <tr>
        <td>Paid</td>
        <td class="text-right">₹{{ number_format($booking['paid_amount'], 2) }}</td>
    </tr>
    <tr>
        <th>Balance</th>
        <th class="text-right">₹{{ number_format($booking['balance_amount'], 2) }}</th>
    </tr>
</table>

---

<p style="text-align:center;margin-top:20px;">
    Thank you for staying with us!
</p>

</body>
</html>
