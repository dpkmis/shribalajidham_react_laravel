<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
        }
        h2, h4 {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        th, td {
            border: 1px solid #333;
            padding: 6px;
            vertical-align: top;
        }
        th {
            background-color: #f2f2f2;
        }
        .no-border td {
            border: none;
        }
        .text-right {
            text-align: right;
        }
        hr {
            margin: 10px 0;
        }
    </style>
</head>
<body>


<h2><?php echo e($booking->property->name); ?></h2>
<p>
    <?php echo e($booking->property->meta['location'] ?? ''); ?>

</p>

<hr>


<table class="no-border">
    <tr>
        <td>
            <strong>Guest Details</strong><br>
            <?php echo e($booking->guest->full_name); ?><br>
            Phone: <?php echo e($booking->guest->phone); ?><br>
            Email: <?php echo e($booking->guest->email); ?>

        </td>
        <td class="text-right">
            <strong>Invoice</strong><br>
            Booking Ref: <?php echo e($booking->booking_reference); ?><br>
            Date: <?php echo e($booking->created_at->format('d M Y')); ?><br>
            Status: <?php echo e(ucfirst($booking->payment_status)); ?>

        </td>
    </tr>
</table>

<hr>


<h4>Booking Details</h4>
<table>
    <tr>
        <th>Check-in</th>
        <th>Check-out</th>
        <th>Nights</th>
        <th>Source</th>
    </tr>
    <tr>
        <td><?php echo e($booking->checkin_date->format('d M Y')); ?></td>
        <td><?php echo e($booking->checkout_date->format('d M Y')); ?></td>
        <td><?php echo e($booking->nights); ?></td>
        <td><?php echo e(ucfirst($booking->source)); ?></td>
    </tr>
</table>


<h4>Room Charges</h4>
<table>
    <tr>
        <th>Room Type</th>
        <th>Nights</th>
        <th>Rate / Night</th>
        <th>Total</th>
    </tr>
    <?php $__currentLoopData = $booking->bookingRooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td><?php echo e($room->roomType->name); ?></td>
            <td><?php echo e($room->nights); ?></td>
            <td class="text-right">
                ₹<?php echo e(number_format($room->rate_per_night_cents / 100, 2)); ?>

            </td>
            <td class="text-right">
                ₹<?php echo e(number_format($room->final_rate_cents / 100, 2)); ?>

            </td>
        </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</table>


<?php if($booking->charges->isNotEmpty()): ?>
<h4>Additional Charges</h4>
<table>
    <tr>
        <th>Description</th>
        <th>Quantity</th>
        <th>Total</th>
    </tr>
    <?php $__currentLoopData = $booking->charges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $charge): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td><?php echo e($charge->description); ?></td>
            <td><?php echo e($charge->quantity); ?></td>
            <td class="text-right">
                ₹<?php echo e(number_format($charge->total_cents / 100, 2)); ?>

            </td>
        </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</table>
<?php endif; ?>


<h4>Payment Summary</h4>
<table>
    <tr>
        <td>Room Charges</td>
        <td class="text-right">
            ₹<?php echo e(number_format($booking->room_charges_cents / 100, 2)); ?>

        </td>
    </tr>
    <tr>
        <td>Additional Charges</td>
        <td class="text-right">
            ₹<?php echo e(number_format($booking->additional_charges_cents / 100, 2)); ?>

        </td>
    </tr>
    <tr>
        <td>Discount</td>
        <td class="text-right">
            ₹<?php echo e(number_format($booking->discount_amount_cents / 100, 2)); ?>

        </td>
    </tr>
    <tr>
        <td>Tax</td>
        <td class="text-right">
            ₹<?php echo e(number_format($booking->tax_amount_cents / 100, 2)); ?>

        </td>
    </tr>
    <tr>
        <th>Total</th>
        <th class="text-right">
            ₹<?php echo e(number_format($booking->total_amount, 2)); ?>

        </th>
    </tr>
    <tr>
        <td>Paid</td>
        <td class="text-right">
            ₹<?php echo e(number_format($booking->paid_amount, 2)); ?>

        </td>
    </tr>
    <tr>
        <th>Balance</th>
        <th class="text-right">
            ₹<?php echo e(number_format($booking->balance_amount, 2)); ?>

        </th>
    </tr>
</table>

<hr>

<p style="text-align:center;">
    Thank you for staying with us!
</p>

</body>
</html>
<?php /**PATH /var/www/html/wavestube/resources/views/invoices/pdf.blade.php ENDPATH**/ ?>