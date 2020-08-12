select `p`.`id` AS `journal_id`,`q`.`id` AS `journal_detail_id`,`p`.`company_id` AS `company_id`,
`s`.`id` AS `department_id`,`s`.`name` AS `department_name`,
date_format(`p`.`trans_date`,'%Y-%m') AS `period`,`p`.`trans_date` AS `trans_date`,`p`.`trans_no` AS `trans_no`,
`p`.`description` AS `journal_description`,
`q`.`sequence` AS `sequence`,`q`.`created_at` AS `created_at`,`q`.`description` AS `description`,`q`.`tags` AS `tags`,
`q`.`debit` AS `debit`,`q`.`credit` AS `credit`,
`r`.`account_id` AS `account_id`,`r`.`sequence` AS `account_sequence`,`r`.`account_no` AS `account_no`,
`r`.`account_name` AS `account_name`,`r`.`account_parent_id` AS `account_parent_id`,`r`.`account_parent_no` AS `account_parent_no`,
`r`.`account_parent_name` AS `account_parent_name`,`r`.`account_type_id` AS `account_type_id`,
`r`.`account_type_name` AS `account_type_name`,`r`.`account_type_group` AS `account_type_group`,
`r`.`account_mapping` AS `account_mapping`,`r`.`type` AS `type`,(`r`.`debit_sign` * `q`.`debit`) AS `debit_sign`,
(`r`.`credit_sign` * `q`.`credit`) AS `credit_sign`,
((`r`.`debit_sign` * `q`.`debit`) + (`r`.`credit_sign` * `q`.`credit`)) AS `total`,
`p`.`total` AS `balance`,`r`.`opening_balance` AS `opening_balance`,`u`.`name` AS `created_by`
from
(
    (
        (
            (`journals` `p` join `journal_details` `q` on((`p`.`id` = `q`.`journal_id`)))
            join (
                select `a`.`company_id` AS `company_id`,`a`.`id` AS `account_id`,`a`.`sequence` AS `sequence`,
                `a`.`account_no` AS `account_no`,`a`.`account_name` AS `account_name`,`a`.`type` AS `type`,
                `a`.`account_mapping` AS `account_mapping`,
                `b`.`id` AS `account_parent_id`,`b`.`account_no` AS `account_parent_no`,`b`.`account_name` AS `account_parent_name`,
                `c`.`id` AS `account_type_id`,`c`.`name` AS `account_type_name`,`c`.`group` AS `account_type_group`,
                `c`.`credit_sign` AS `credit_sign`,`c`.`debit_sign` AS `debit_sign`,
                ((`a`.`op_debit` * `c`.`debit_sign`) + (`a`.`op_credit` * `c`.`credit_sign`)) AS `opening_balance`
                from ((`accounts` `a` join `account_types` `c` on((`c`.`id` = `a`.`account_type_id`)))
                left join `accounts` `b` on((`b`.`id` = `a`.`account_parent_id`)))
                where (`a`.`has_children` = 0)) `r` on(((`q`.`account_id` = `r`.`account_id`) and (`p`.`company_id` = `r`.`company_id`))))
                left join `departments` `s` on((`s`.`id` = `q`.`department_id`)))
                left join `users` `u` on((`u`.`id` = `p`.`created_by`))) where (`p`.`is_processed` = 0 AND `p`.`status`='approved')
