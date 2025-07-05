<div class="bankDeposit">
    <span>
        To Deposit : <b><?php echo $to_deposit_balance ?></b>
    </span>
    <form action="bankdeposit-submit.php" method="post">
        <input type="text" name="amount" placeholder="Enter Amount">
        <input type="submit" value="Deposit">
    </form>
</div>