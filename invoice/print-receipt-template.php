<!-- Receipt Layout Template -->
<div class="receipt-content" <?php echo ($printType === 'standard') ? 'style="display:none;"' : ''; ?>>
    <div class="header">
        <div class="logo-img"> <img src="../logo.png" alt="LOGO"> </div>
        <div class="topic">
            <h1><?php echo $ERP_COMPANY_NAME; ?></h1>
            <h2><?php echo $ERP_COMPANY_ADDRESS; ?><br><?php echo $ERP_COMPANY_PHONE; ?></h2>
        </div>
    </div>
    <hr />
    <div class="details">
        <div class="Innerdetails">
            <div style="text-align: left; margin-left: 5px;">
                Bill No : <span class="bill-no"><?php echo $bill_no; ?></span> <br>
                Date : <?php echo $date; ?>
            </div>
            <div style="text-align: right; margin-right: 5px;">
                Cashier : <?php echo $cashier_name; ?> <br>
                Customer : <?php echo $customer; ?>
            </div>
        </div>
    </div>
    <div class="content">
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="price">Price</th>
                    <th class="price">Qty</th>
                    <th class="price">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Reset pointer for receipt loop
                mysqli_data_seek($sales_result, 0);
                while ($row = mysqli_fetch_array($sales_result)) {
                    $product_name = $row['product'];
                    $quantity = $row['qty'];
                    $price = $row['rate'];
                    $discount_value = $row['discount_price'];
                    $amount = $row['amount'];
                    $item_total = $price * $quantity;
                    
                    // Calculate actual discount from the difference
                    $item_discount = $item_total - $amount;
                    $discount_applied = ($item_discount > 0);
                    
                    // Calculate discount percentage or amount for display
                    $display_discount = '';
                    if ($discount_applied && $discount_value > 0) {
                        if ($individual_discount_mode == 1) {
                            // Calculate actual percentage from the discount
                            $actual_discount_percent = ($item_discount / $item_total) * 100;
                            $display_discount = number_format($actual_discount_percent, 2) . '%';
                        } else {
                            $display_discount = 'Rs.' . number_format($discount_value, 2);
                        }
                    }
                    
                    $has_discount = $discount_applied;
                    $display_price = number_format($price, 2);
                    $display_amount = number_format($amount, 2);
                ?>
                <tr class="product-row">
                    <td>
                        <?php echo $product_name; ?>
                        <?php if ($discount_applied && $individual_discount_mode == 1): ?>
                            <br><small><i>(Disc: <?php echo $display_discount; ?>)</i></small>
                        <?php endif; ?>
                    </td>
                    <td class="price">
                        <?php if ($discount_applied && $individual_discount_mode == 1): ?>
                            <span class="promotion"><?php echo number_format($price, 2); ?></span><br>
                            <span class="discount-price"><?php echo number_format($amount / $quantity, 2); ?></span>
                        <?php else: ?>
                            <?php echo $display_price; ?>
                        <?php endif; ?>
                    </td>
                    <td class="price"><?php echo $quantity; ?></td>
                    <td class="price"><?php echo $display_amount; ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php if ($has_individual_discounts && $total_discount > 0 && $individual_discount_mode == 1): ?>
            <div style="text-align: center; margin: 10px 0; padding: 8px; background-color: #f0f9ff; border: 1px dashed #22c55e; border-radius: 5px;">
                <strong style="color: #22c55e; font-size: 14px;">🎉 You total saved: Rs.<?php echo number_format($total_discount, 2); ?></strong>
            </div>
        <?php endif; ?>
        <table>
            <tfoot class="total-summary">
                <?php 
                // Logic for row visibility (Receipt)
                
                // Use total_discount for individual discount mode, otherwise use $discount from database
                $display_discount = ($individual_discount_mode == 1 && $total_discount > 0) ? $total_discount : $discount;
                
                // Recalculate total for correct display (database total may be wrong)
                $calculated_total = $sub_total - $display_discount;
                $display_total = $calculated_total;
                
                // Show discount if there's database discount OR calculated total_discount in individual mode
                $show_discount = ($discount > 0 || ($individual_discount_mode == 1 && $total_discount > 0));
                
                // Show subtotal only if it differs from total (i.e., there's a discount)
                $show_sub_total = ($display_total != $sub_total);
                
                $show_total = true; // Always show total
                
                $show_advance = ($advance > 0 && $advance != $display_total); // Hide advance if equals total
                
                // Calculate balance using corrected total
                $calculated_balance = $display_total - $advance;
                $show_balance = ($advance > 0 && $calculated_balance > 0); // Only show if balance > 0

                // Override: If Advance covers the full Sub Total, revert to Simple Bill format
                if ($advance >= $sub_total) {
                    $show_sub_total = false;
                    $show_discount = false;
                    $show_total = true;
                    $show_advance = false;
                    $show_balance = false;
                }
                ?>

                <?php if ($show_sub_total): ?>
                <tr>
                    <td colspan="3" class="bill_sum">Sub Total :</td>
                    <td class="bill_sum"><?php echo number_format($sub_total, 2); ?></td>
                </tr>
                <?php endif; ?>

                <?php if ($show_discount): ?>
                <tr>
                    <td colspan="3" class="bill_sum">Discount :</td>
                    <td class="bill_sum">-<?php echo number_format($display_discount, 2); ?></td>
                </tr>
                <?php endif; ?>

                <?php if ($show_total): ?>
                <tr class="final-total">
                    <td colspan="3" class="bill_sum">Total :</td>
                    <td class="bill_sum"><?php echo number_format($display_total, 2); ?></td>
                </tr>
                <?php endif; ?>

                <?php if ($show_advance): ?>
                <tr>
                    <td colspan="3" class="bill_sum">Advance :</td>
                    <td class="bill_sum"><?php echo number_format($advance, 2); ?></td>
                </tr>
                <?php endif; ?>

                <?php if ($show_balance): ?>
                <tr class="final-total">
                    <td colspan="3" class="bill_sum">Balance :</td>
                    <td class="bill_sum"><?php echo number_format($calculated_balance, 2); ?></td>
                </tr>
                <?php endif; ?>
            </tfoot>
        </table>
    </div>
    <div class="payment-method">
        Payment: <?php echo $payment_method; ?>
    </div>
    <div class="thank-you">
        Thank you! Come again.
    </div>
    <div class="footer">
        Software by <b>CyexTech Solutions</b> <br /> Visit : <b>www.CyexTech.com</b>
    </div>
</div>
