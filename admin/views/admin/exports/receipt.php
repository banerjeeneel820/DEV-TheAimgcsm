<h2 style="text-align:center; margin-bottom:10px;">
    THE AIMGCSM Receipt RECORDS
</h2>
<h3 style="margin-bottom:10px;">
    <?=$criteriaText?>
</h3>

<table border="1" cellpadding="5" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>SL No.</th>
            <th>Receipt ID</th>
            <th>Student Details</th>
            <th>Receipt Details(Rs.)</th>
            <th>Extra Fees</th>
            <th>Course & Franchise</th>
            <th>Student ID & Result</th>
        </tr>
    </thead>

    <tbody style="text-align: center;">
        <?php foreach ($receipts as $index => $receipt) { ?>
            <tr>
                <td><?= $index + 1 ?></td>

                <td>
                    <?= $receipt["receipt_id"] ?><br>
                    <?= $receipt["created_at"] ?>
                </td>

                <td>
                    <?= $receipt["stu_name"] ?><br>
                    <?= $receipt["stu_phone"] ?>
                </td>

                <td>
                    Receipt Amount: <?= $receipt["receipt_amount"] ?><br>
                    Late Fine: <?= $receipt["late_fine"] ?><br>
                    Extra Fees: <?= $receipt["extra_fees"] ?>
                </td>

                <td>
                    <?= $receipt["extra_fees_description"] ?><br>
                </td>

                <td>
                    <?= $receipt["course_title"] ?><br>
                    <?= $receipt["center_name"] ?>
                </td>

                <td>
                    <?= $receipt["stu_id"] ?><br>
                    <?= $receipt["stu_result"] ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>