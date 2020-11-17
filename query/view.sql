-- vw_accounts
select `a`.`id` AS `id`,`a`.`sequence` AS `sequence`,`a`.`account_no` AS `account_no`,`a`.`account_name` AS `account_name`,`a`.`account_name_en` AS `account_name_en`,`a`.`has_children` AS `has_children`,`a`.`tree_level` AS `tree_level`,`a`.`account_parent_id` AS `account_parent_id`,`a`.`company_id` AS `company_id`,`a`.`account_type_id` AS `account_type_id`,`a`.`account_mapping` AS `account_mapping`,`a`.`op_debit` AS `op_debit`,`a`.`op_credit` AS `op_credit`,`a`.`op_date` AS `op_date`,`a`.`type` AS `type`,`a`.`is_default` AS `is_default`,`a`.`created_at` AS `created_at`,`a`.`updated_at` AS `updated_at`,`a`.`deleted_at` AS `deleted_at`,`a`.`created_by` AS `created_by`,`a`.`deleted_by` AS `deleted_by`,`a`.`updated_by` AS `updated_by`,`b`.`name` AS `account_type`,`b`.`group` AS `account_group`,`b`.`debit_sign` AS `debit_sign`,`b`.`credit_sign` AS `credit_sign`,`a`.`op_date` AS `balance_date`,((`a`.`op_debit` * `b`.`debit_sign`) + (`a`.`op_credit` * `b`.`credit_sign`)) AS `balance` from (`accounts` `a` join `account_types` `b` on((`a`.`account_type_id` = `b`.`id`)))


--vw_balance
select `a`.`id` AS `id`,`a`.`sequence` AS `sequence`,`a`.`account_no` AS `account_no`,`a`.`has_children` AS `has_children`,`a`.`tree_level` AS `tree_level`,`a`.`account_parent_id` AS `account_parent_id`,`u`.`trans_date` AS `trans_date`,`u`.`balance_date` AS `balance_date`,`u`.`department_id` AS `department_id`,`u`.`company_id` AS `company_id`,`u`.`opening_balance` AS `opening_balance`,`u`.`debit` AS `debit`,`u`.`credit` AS `credit` from (`abipro-fresh`.`accounts` `a` left join (select `a`.`account_parent_id` AS `account_id`,NULL AS `account_parent_id`,`a`.`trans_date` AS `trans_date`,`a`.`balance_date` AS `balance_date`,`a`.`department_id` AS `department_id`,`a`.`company_id` AS `company_id`,sum(`a`.`opening_balance`) AS `opening_balance`,sum(`a`.`debit`) AS `debit`,sum(`a`.`credit`) AS `credit` from `abipro-fresh`.`vw_transaction_balance` `a` group by `a`.`account_parent_id`,`a`.`trans_date`,`a`.`balance_date`,`a`.`department_id`,`a`.`company_id` union select `a`.`account_id` AS `account_id`,`a`.`account_parent_id` AS `account_parent_id`,`a`.`trans_date` AS `trans_date`,`a`.`balance_date` AS `balance_date`,`a`.`department_id` AS `department_id`,`a`.`company_id` AS `company_id`,`a`.`opening_balance` AS `opening_balance`,`a`.`debit` AS `debit`,`a`.`credit` AS `credit` from `abipro-fresh`.`vw_transaction_balance` `a`) `u` on((`a`.`id` = `u`.`account_id`)))

--vw_budgets
select `a`.`account_parent_id` AS `account_id`,`b`.`budget_year` AS `budget_year`,`b`.`department_id` AS `department_id`,`b`.`company_id` AS `company_id`,sum(`b`.`jan`) AS `jan`,sum(`b`.`feb`) AS `feb`,sum(`b`.`mar`) AS `mar`,sum(`b`.`apr`) AS `apr`,sum(`b`.`may`) AS `may`,sum(`b`.`jun`) AS `jun`,sum(`b`.`jul`) AS `jul`,sum(`b`.`aug`) AS `aug`,sum(`b`.`sep`) AS `sep`,sum(`b`.`oct`) AS `oct`,sum(`b`.`nov`) AS `nov`,sum(`b`.`dec`) AS `dec`,sum(`b`.`total`) AS `SUM(total)` from (`budgets` `b` join `accounts` `a`) where (`b`.`account_id` = `a`.`id`) group by `a`.`account_parent_id`,`b`.`budget_year`,`b`.`department_id`,`b`.`company_id` union select `budgets`.`account_id` AS `account_id`,`budgets`.`budget_year` AS `budget_year`,`budgets`.`department_id` AS `department_id`,`budgets`.`company_id` AS `company_id`,`budgets`.`jan` AS `jan`,`budgets`.`feb` AS `feb`,`budgets`.`mar` AS `mar`,`budgets`.`apr` AS `apr`,`budgets`.`may` AS `may`,`budgets`.`jun` AS `jun`,`budgets`.`jul` AS `jul`,`budgets`.`aug` AS `aug`,`budgets`.`sep` AS `sep`,`budgets`.`oct` AS `oct`,`budgets`.`nov` AS `nov`,`budgets`.`dec` AS `dec`,`budgets`.`total` AS `total` from `budgets`


--vw_journals
select `p`.`id` AS `journal_id`,`p`.`numbering_id` AS `numbering_id`,`q`.`id` AS `journal_detail_id`,`p`.`company_id` AS `company_id`,`s`.`id` AS `department_id`,`s`.`custom_id` AS `department_custom_id`,`s`.`name` AS `department_name`,date_format(`p`.`trans_date`,'%Y-%m') AS `period`,`p`.`trans_date` AS `trans_date`,`p`.`trans_no` AS `trans_no`,`p`.`description` AS `journal_description`,`q`.`sequence` AS `sequence`,`q`.`created_at` AS `created_at`,`q`.`description` AS `description`,`q`.`tags` AS `tags`,`q`.`debit` AS `debit`,`q`.`credit` AS `credit`,`r`.`account_id` AS `account_id`,`r`.`sequence` AS `account_sequence`,`r`.`account_no` AS `account_no`,`r`.`account_name` AS `account_name`,`r`.`account_parent_id` AS `account_parent_id`,`r`.`account_parent_no` AS `account_parent_no`,`r`.`account_parent_name` AS `account_parent_name`,`r`.`account_type_id` AS `account_type_id`,`r`.`account_type_name` AS `account_type_name`,`r`.`account_type_group` AS `account_type_group`,`r`.`account_mapping` AS `account_mapping`,`r`.`type` AS `type`,(`r`.`debit_sign` * `q`.`debit`) AS `debit_sign`,(`r`.`credit_sign` * `q`.`credit`) AS `credit_sign`,((`r`.`debit_sign` * `q`.`debit`) + (`r`.`credit_sign` * `q`.`credit`)) AS `total`,`p`.`total` AS `balance`,`r`.`opening_balance` AS `opening_balance`,`u`.`name` AS `created_by` from ((((`abipro-fresh`.`journals` `p` join `abipro-fresh`.`journal_details` `q` on((`p`.`id` = `q`.`journal_id`))) join (select `a`.`company_id` AS `company_id`,`a`.`id` AS `account_id`,`a`.`sequence` AS `sequence`,`a`.`account_no` AS `account_no`,`a`.`account_name` AS `account_name`,`a`.`type` AS `type`,`a`.`account_mapping` AS `account_mapping`,`b`.`id` AS `account_parent_id`,`b`.`account_no` AS `account_parent_no`,`b`.`account_name` AS `account_parent_name`,`c`.`id` AS `account_type_id`,`c`.`name` AS `account_type_name`,`c`.`group` AS `account_type_group`,`c`.`credit_sign` AS `credit_sign`,`c`.`debit_sign` AS `debit_sign`,((`a`.`op_debit` * `c`.`debit_sign`) + (`a`.`op_credit` * `c`.`credit_sign`)) AS `opening_balance` from ((`abipro-fresh`.`accounts` `a` join `abipro-fresh`.`account_types` `c` on((`c`.`id` = `a`.`account_type_id`))) left join `abipro-fresh`.`accounts` `b` on((`b`.`id` = `a`.`account_parent_id`))) where (`a`.`has_children` = 0)) `r` on(((`q`.`account_id` = `r`.`account_id`) and (`p`.`company_id` = `r`.`company_id`)))) left join `abipro-fresh`.`departments` `s` on((`s`.`id` = `q`.`department_id`))) left join `abipro-fresh`.`users` `u` on((`u`.`id` = `p`.`created_by`))) where (`p`.`is_processed` = 1)

--vw_ledger
select `p`.`id` AS `journal_id`,`q`.`id` AS `journal_detail_id`,`p`.`company_id` AS `company_id`,`s`.`id` AS `department_id`,`s`.`custom_id` AS `department_custom_id`,`s`.`name` AS `department_name`,date_format(`p`.`trans_date`,'%Y-%m') AS `period`,`p`.`trans_date` AS `trans_date`,`p`.`trans_no` AS `trans_no`,`p`.`description` AS `journal_description`,`q`.`sequence` AS `sequence`,`q`.`created_at` AS `created_at`,`q`.`description` AS `description`,`q`.`tags` AS `tags`,`q`.`debit` AS `debit`,`q`.`credit` AS `credit`,`r`.`account_id` AS `account_id`,`r`.`sequence` AS `account_sequence`,`r`.`account_no` AS `account_no`,`r`.`account_name` AS `account_name`,`r`.`account_parent_id` AS `account_parent_id`,`r`.`account_parent_no` AS `account_parent_no`,`r`.`account_parent_name` AS `account_parent_name`,`r`.`account_type_id` AS `account_type_id`,`r`.`account_type_name` AS `account_type_name`,`r`.`account_type_group` AS `account_type_group`,`r`.`account_mapping` AS `account_mapping`,`r`.`type` AS `type`,(`r`.`debit_sign` * `q`.`debit`) AS `debit_sign`,(`r`.`credit_sign` * `q`.`credit`) AS `credit_sign`,((`r`.`debit_sign` * `q`.`debit`) + (`r`.`credit_sign` * `q`.`credit`)) AS `total`,`p`.`total` AS `balance`,`r`.`opening_balance` AS `opening_balance`,`u`.`name` AS `created_by` from ((((`abipro`.`journals` `p` join `abipro`.`journal_details` `q` on((`p`.`id` = `q`.`journal_id`))) join (select `a`.`company_id` AS `company_id`,`a`.`id` AS `account_id`,`a`.`sequence` AS `sequence`,`a`.`account_no` AS `account_no`,`a`.`account_name` AS `account_name`,`a`.`type` AS `type`,`a`.`account_mapping` AS `account_mapping`,`b`.`id` AS `account_parent_id`,`b`.`account_no` AS `account_parent_no`,`b`.`account_name` AS `account_parent_name`,`c`.`id` AS `account_type_id`,`c`.`name` AS `account_type_name`,`c`.`group` AS `account_type_group`,`c`.`credit_sign` AS `credit_sign`,`c`.`debit_sign` AS `debit_sign`,((`a`.`op_debit` * `c`.`debit_sign`) + (`a`.`op_credit` * `c`.`credit_sign`)) AS `opening_balance` from ((`abipro`.`accounts` `a` join `abipro`.`account_types` `c` on((`c`.`id` = `a`.`account_type_id`))) left join `abipro`.`accounts` `b` on((`b`.`id` = `a`.`account_parent_id`))) where (`a`.`has_children` = 0)) `r` on(((`q`.`account_id` = `r`.`account_id`) and (`p`.`company_id` = `r`.`company_id`)))) left join `abipro`.`departments` `s` on((`s`.`id` = `q`.`department_id`))) left join `abipro`.`users` `u` on((`u`.`id` = `p`.`created_by`))) where (`p`.`is_voucher` = 0)

-- vw_opening_balance
select `a`.`id` AS `account_id`,`c`.`department_id` AS `department_id`,`a`.`company_id` AS `company_id`,sum(`c`.`balance`) AS `balance` from ((`accounts` `a` left join `accounts` `b` on((`a`.`id` = `b`.`account_parent_id`))) left join `balances` `c` on((`b`.`id` = `c`.`account_id`))) where (`a`.`company_id` = `c`.`company_id`) group by `a`.`id`,`a`.`company_id`,`c`.`department_id` union select `a`.`id` AS `account_id`,`c`.`department_id` AS `department_id`,`a`.`company_id` AS `company_id`,`c`.`balance` AS `balance` from (`accounts` `a` left join `balances` `c` on((`a`.`id` = `c`.`account_id`))) where (`a`.`company_id` = `c`.`company_id`)

--- vw_transaction_balance
select `a`.`id` AS `account_id`,`a`.`account_parent_id` AS `account_parent_id`,`d`.`id` AS `account_type_id`,`d`.`group` AS `account_type_group`,`c`.`trans_date` AS `trans_date`,`b`.`balance_date` AS `balance_date`,`c`.`department_id` AS `department_id`,`a`.`company_id` AS `company_id`,`b`.`balance` AS `opening_balance`,(sum(`c`.`debit`) * `d`.`debit_sign`) AS `debit`,(sum(`c`.`credit`) * `d`.`credit_sign`) AS `credit`,((sum(`c`.`debit`) * `d`.`debit_sign`) + (sum(`c`.`credit`) * `d`.`credit_sign`)) AS `total` from ((((`accounts` `a` join `account_types` `d` on((`a`.`account_type_id` = `d`.`id`))) left join `balances` `b` on((`a`.`id` = `b`.`account_id`))) left join `journal_details` `c` on((`a`.`id` = `c`.`account_id`))) left join `journals` `e` on((`e`.`id` = `c`.`journal_id`))) where ((`a`.`has_children` = 0) and isnull(`b`.`department_id`) and (`e`.`is_processed` = 1)) group by `a`.`id`,`a`.`account_parent_id`,`d`.`id`,`d`.`group`,`c`.`trans_date`,`b`.`balance_date`,`c`.`department_id`,`a`.`company_id`,`b`.`balance`,`d`.`debit_sign`,`d`.`credit_sign`


--- vw_voucher
select `p`.`id` AS `journal_id`,`p`.`numbering_id` AS `numbering_id`,`q`.`id` AS `journal_detail_id`,`p`.`company_id` AS `company_id`,`s`.`id` AS `department_id`,`s`.`custom_id` AS `department_custom_id`,`s`.`name` AS `department_name`,date_format(`p`.`trans_date`,'%Y-%m') AS `period`,`p`.`trans_date` AS `trans_date`,`p`.`trans_no` AS `trans_no`,`p`.`description` AS `journal_description`,`q`.`sequence` AS `sequence`,`q`.`created_at` AS `created_at`,`q`.`description` AS `description`,`q`.`tags` AS `tags`,`q`.`debit` AS `debit`,`q`.`credit` AS `credit`,`r`.`account_id` AS `account_id`,`r`.`sequence` AS `account_sequence`,`r`.`account_no` AS `account_no`,`r`.`account_name` AS `account_name`,`r`.`account_parent_id` AS `account_parent_id`,`r`.`account_parent_no` AS `account_parent_no`,`r`.`account_parent_name` AS `account_parent_name`,`r`.`account_type_id` AS `account_type_id`,`r`.`account_type_name` AS `account_type_name`,`r`.`account_type_group` AS `account_type_group`,`r`.`account_mapping` AS `account_mapping`,`r`.`type` AS `type`,(`r`.`debit_sign` * `q`.`debit`) AS `debit_sign`,(`r`.`credit_sign` * `q`.`credit`) AS `credit_sign`,((`r`.`debit_sign` * `q`.`debit`) + (`r`.`credit_sign` * `q`.`credit`)) AS `total`,`p`.`total` AS `balance`,`r`.`opening_balance` AS `opening_balance`,`u`.`name` AS `created_by` from ((((`abipro-fresh`.`journals` `p` join `abipro-fresh`.`journal_details` `q` on((`p`.`id` = `q`.`journal_id`))) join (select `a`.`company_id` AS `company_id`,`a`.`id` AS `account_id`,`a`.`sequence` AS `sequence`,`a`.`account_no` AS `account_no`,`a`.`account_name` AS `account_name`,`a`.`type` AS `type`,`a`.`account_mapping` AS `account_mapping`,`b`.`id` AS `account_parent_id`,`b`.`account_no` AS `account_parent_no`,`b`.`account_name` AS `account_parent_name`,`c`.`id` AS `account_type_id`,`c`.`name` AS `account_type_name`,`c`.`group` AS `account_type_group`,`c`.`credit_sign` AS `credit_sign`,`c`.`debit_sign` AS `debit_sign`,((`a`.`op_debit` * `c`.`debit_sign`) + (`a`.`op_credit` * `c`.`credit_sign`)) AS `opening_balance` from ((`abipro-fresh`.`accounts` `a` join `abipro-fresh`.`account_types` `c` on((`c`.`id` = `a`.`account_type_id`))) left join `abipro-fresh`.`accounts` `b` on((`b`.`id` = `a`.`account_parent_id`))) where (`a`.`has_children` = 0)) `r` on(((`q`.`account_id` = `r`.`account_id`) and (`p`.`company_id` = `r`.`company_id`)))) left join `abipro-fresh`.`departments` `s` on((`s`.`id` = `q`.`department_id`))) left join `abipro-fresh`.`users` `u` on((`u`.`id` = `p`.`created_by`))) where ((`p`.`is_processed` = 0) and (`p`.`status` = 'approved'))
