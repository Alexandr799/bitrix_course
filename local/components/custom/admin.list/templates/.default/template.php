
<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<table>
    <tr>
        <th>Логин</th>
        <th>Email</th>
        <th>Имя</th>
        <th>Фамилия</th>
    </tr>
    <?php foreach ($arResult['ADMINS'] as $user) : ?>
        <tr>
            <td><?= $user['LOGIN'] ?></td>
            <td><?= $user['EMAIL'] ?></td>
            <td><?= $user['NAME'] ?></td>
            <td><?= $user['LAST_NAME'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>

