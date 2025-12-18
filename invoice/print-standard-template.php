<!-- Standard A5 Invoice Layout Template -->
<div class="standard-content a5-container bg-background-light dark:bg-background-dark text-slate-800 dark:text-slate-200" <?php echo ($printType === 'receipt') ? 'style="display:none;"' : ''; ?>>
    <div class="flex flex-col h-full">
        <header>
            <div class="flex justify-between items-start">
                <div class="flex items-center space-x-4">
                    <!-- Logo Image -->
                    <img src="../logo.png" alt="Logo" class="h-13 w-13 object-contain">
                    <div>
                        <h1 class="text-xl font-bold text-slate-900 dark:text-white"><?php echo $ERP_COMPANY_NAME; ?></h1>
                        <p class="text-xs text-slate-500 dark:text-slate-400"><?php echo $ERP_COMPANY_ADDRESS; ?></p>
                        <p class="text-xs text-slate-500 dark:text-slate-400"><?php echo $ERP_COMPANY_PHONE; ?></p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">www.srijaya.lk</p>
                    </div>
                </div>
                <div class="text-right pt-5 pr-5">
                    <h2 class="text-3xl font-bold uppercase text-slate-900 dark:text-white">Invoice</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400">No. <?php echo $bill_no; ?></p>
                </div>
            </div>
            <div class="mt-4 border-t-4 border-primary"></div>
        </header>
        <section class="mt-6 grid grid-cols-3 gap-4">
            <div>
                <h3 class="text-xs font-semibold uppercase text-slate-500 dark:text-slate-400 green-underline">Billed To</h3>
                <p class="mt-1 text-xs font-medium text-slate-700 dark:text-slate-300"><?php echo $customer; ?></p>
                <p class="text-xs text-slate-600 dark:text-slate-400"><?php echo $tele; ?></p>
            </div>
            <div></div>
            <div class="text-right">
                <h3 class="text-xs font-semibold uppercase text-slate-500 dark:text-slate-400 green-underline">Invoice Details</h3>
                <div class="mt-1 space-y-1 text-xs">
                    <div class="flex justify-end space-x-2">
                        <span class="text-slate-600 dark:text-slate-400">Date:</span>
                        <span class="font-medium text-slate-700 dark:text-slate-300"><?php echo $date; ?></span>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <span class="text-slate-600 dark:text-slate-400 whitespace-nowrap">Billed by:</span>
                        <span class="font-medium text-slate-700 dark:text-slate-300 whitespace-nowrap"><?php echo $cashier_name; ?></span>
                    </div>
                </div>
            </div>
        </section>
        <section class="mt-8">
            <div class="flow-root">
                <div class="-mx-2 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="inline-block min-w-full px-2 py-2 align-middle sm:px-6 lg:px-8">
                        <table class="min-w-full">
                            <thead class="border-b-2 border-slate-200 dark:border-slate-700">
                                <tr>
                                    <th class="py-1 pl-2 pr-1 text-left text-xs font-semibold text-slate-900 dark:text-white sm:pl-0" scope="col">Description</th>
                                    <th class="px-1 py-1 text-right text-xs font-semibold text-slate-900 dark:text-white" scope="col">Price</th>
                                    <th class="px-1 py-1 text-right text-xs font-semibold text-slate-900 dark:text-white" scope="col">Quantity</th>
                                    <th class="py-1 pl-1 pr-2 text-right text-xs font-semibold text-slate-900 dark:text-white sm:pr-0" scope="col">Total</th>
                                </tr>
                            </thead>
                            <tbody class="text-slate-700 dark:text-slate-300">
                                <?php
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
                                ?>
                                    <tr class="border-b border-slate-200 dark:border-slate-800">
                                        <td class="py-2 pl-2 pr-1 text-sm font-medium sm:pl-0">
                                            <?php echo $product_name; ?>
                                            <?php if ($discount_applied && $individual_discount_mode == 1): ?>
                                                <br><span class="text-xs text-green-600 italic">(Disc: <?php echo $display_discount; ?>)</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-1 py-2 text-sm text-right">
                                            <?php if ($discount_applied && $individual_discount_mode == 1): ?>
                                                <span class="line-through text-xs text-gray-400"><?php echo number_format($price, 2); ?></span><br>
                                                <span class="font-bold"><?php echo number_format($amount / $quantity, 2); ?></span>
                                            <?php else: ?>
                                                <?php echo number_format($price, 2); ?>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-1 py-2 text-sm text-right"><?php echo $quantity; ?></td>
                                        <td class="py-2 pl-1 pr-2 text-sm font-medium text-primary sm:pr-0 text-right"><?php echo number_format($amount, 2); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Two-column layout for savings message and summary -->
            <div class="mt-4 ml-2 grid grid-cols-2 gap-8">
                <!-- Left column: You total saved message -->
                <div class="flex items-start">
                    <?php if ($has_individual_discounts && $total_discount > 0 && $individual_discount_mode == 1): ?>
                        <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border-l-4 border-green-500">
                            <p class="text-sm font-bold text-green-700 dark:text-green-300">
                                🎉 You total saved: Rs.<?php echo number_format($total_discount, 2); ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Right column: Summary table -->
                <div>
                    <table class="min-w-full">
                        <tbody>
                            <?php
                            // Logic for row visibility

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

                            //past incorrect logic removed lines
                            //                             $show_advance = ($advance > 0 && $advance != $display_total); // Hide advance if equals total
                            //                             $show_balance = ($advance > 0 && $calculated_balance > 0); // Only show if balance > 0


                            // Standard visibility based on payment status
                            // If fully paid (Simple/Cash bill), hide Advance/Balance
                            // If NOT fully paid (Credit/Partial), show Advance (even if 0) and Balance
                            $show_advance = ($full_paid == 0);
                            $show_balance = ($full_paid == 0);

                            // Calculate balance using corrected total for display
                            $calculated_balance = $display_total - $advance;

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
                                <tr class="bill-summary-row">
                                    <th class="pt-2 pl-4 pr-3 text-right text-sm font-normal text-slate-500 dark:text-slate-400 sm:pl-0" colspan="1" scope="row">Sub Total</th>
                                    <td class="pt-2 pl-3 pr-4 text-right text-sm font-normal text-slate-500 dark:text-slate-400 sm:pr-0"><?php echo number_format($sub_total, 2); ?></td>
                                </tr>
                            <?php endif; ?>

                            <?php if ($show_discount): ?>
                                <tr class="bill-summary-row">
                                    <th class="pt-1 pl-4 pr-3 text-right text-sm font-normal text-slate-500 dark:text-slate-400 sm:pl-0" colspan="1" scope="row">Discount</th>
                                    <td class="pt-1 pl-3 pr-4 text-right text-sm font-normal text-green-600 sm:pr-0">-<?php echo number_format($display_discount, 2); ?></td>
                                </tr>
                            <?php endif; ?>

                            <?php if ($show_total): ?>
                                <tr class="bill-summary-row">
                                    <th class="pt-2 pl-4 pr-3 text-right text-base font-semibold text-slate-900 dark:text-white sm:pl-0" colspan="1" scope="row">Total</th>
                                    <td class="pt-2 pl-3 pr-4 text-right text-base font-semibold text-primary sm:pr-0"><?php echo number_format($display_total, 2); ?></td>
                                </tr>
                            <?php endif; ?>

                            <?php if ($show_advance): ?>
                                <tr class="bill-summary-row">
                                    <th class="pt-1 pl-4 pr-3 text-right text-sm font-normal text-slate-500 dark:text-slate-400 sm:pl-0" colspan="1" scope="row">Advance</th>
                                    <td class="pt-1 pl-3 pr-4 text-right text-sm font-normal text-slate-500 dark:text-slate-400 sm:pr-0"><?php echo number_format($advance, 2); ?></td>
                                </tr>
                            <?php endif; ?>

                            <?php if ($show_balance): ?>
                                <tr class="bill-summary-row">
                                    <th class="pt-2 pl-4 pr-3 text-right text-base font-semibold text-slate-900 dark:text-white sm:pl-0" colspan="1" scope="row">Balance</th>
                                    <td class="pt-2 pl-3 pr-4 text-right text-base font-semibold text-primary sm:pr-0"><?php echo number_format($calculated_balance, 2); ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
        <footer class="mt-auto pt-6 text-center">
            <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">Thank you! Come again.</p>
            <div class="mt-2 border-t border-slate-200 dark:border-slate-700 pt-2">
                <p class="text-xs text-slate-500 dark:text-slate-400">Software by <b>CyexTech Solutions</b></p>
                <p class="text-xs text-slate-500 dark:text-slate-400">Visit: <b>www.CyexTech.com</b></p>
            </div>
        </footer>
    </div>
</div>