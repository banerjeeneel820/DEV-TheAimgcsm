<h2 style="text-align:center; margin-bottom:10px;">
    THE AIMGCSM Receipt RECORDS
</h2>
<h3 style="margin-bottom:10px;">
    <?= $criteriaText ?>
</h3>

<table border="1" cellpadding="5" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>SL No.</th>
            <th>Franchise Name/ID</th>
            <th>Owner Name</th>
            <th>Franchise Contact No & Email</th>
            <th>Franchise Address</th>
            <th>Owned Status</th>
            <th>Student Enrolled</th>
        </tr>
    </thead>

    <tbody style="text-align: center;">
        <?php foreach ($franchises as $index => $franchise) { ?>
            <tr>
                <td><?= $index + 1 ?></td>

                <td>
                    <?= $franchise["center_name"] ?><br>
                    <?= $franchise["fran_id"] ?>
                </td>

                <td>
                    <?= $franchise["owner_name"] ?>
                </td>

                <td>
                    <?= $franchise["fran_phone"] ?><br>
                    <?= $franchise["fran_email"] ?>
                </td>

                <td>
                    <?= $franchise["fran_address"] ?><br>
                </td>

                <td><?= $franchise["owned_status"] ?></td>

                <td><?= $franchise["enrolled_student_count"] ?></td>
                
            </tr>
        <?php } ?>
    </tbody>
</table>