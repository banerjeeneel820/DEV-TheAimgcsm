<h2 style="text-align:center; margin-bottom:10px;">
    THE AIMGCSM STUDENT RECORDS
</h2>

<table border="1" cellpadding="5" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>SL</th>
            <th>Name</th>
            <th>Contact No</th>
            <th>Student ID / Course</th>
            <th>Franchise</th>
            <th>Status / Result</th>
            <th>Receipts</th>
        </tr>
    </thead>

    <tbody style="text-align: center;">
        <?php foreach ($students as $index => $student) { ?>
            <tr>
                <td><?= $index + 1 ?></td>

                <td>
                    <?= $student["stu_name"] ?><br>
                    <?= $student["stu_father_name"] ?>
                </td>

                <td>
                    <?= $student["stu_phone"] ?><br>
                </td>

                <td>
                    <?= $student["stu_id"] ?><br>
                    <?= $student["course_title"] ?>
                </td>

                <td>
                    <?= $student["center_name"] ?>
                </td>

                <td>
                    <?= ucfirst($student["student_status"]) ?><br>
                    <?= ucfirst($student["stu_result"]) ?>
                </td>

                <td><?= $student["receipt_count"] ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>