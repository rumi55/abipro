SELECT a.id AS account_id, a.account_parent_id, d.id AS account_type_id,
d.`group` AS account_type_group,
c.trans_date, b.balance_date,
c.department_id, a.company_id,
b.balance AS opening_balance, SUM(c.debit)*d.debit_sign AS debit, SUM(c.credit)*d.credit_sign AS credit,
 SUM(c.debit)*d.debit_sign + SUM(c.credit)*d.credit_sign AS total
FROM accounts a
JOIN account_types d ON a.account_type_id=d.id
LEFT JOIN balances b ON a.id=b.account_id
LEFT JOIN journal_details c ON a.id=c.account_id
LEFT JOIN journals e ON e.id=c.journal_id
WHERE a.has_children=0 AND b.department_id IS NULL AND e.is_processed=1
GROUP BY
a.id,a.account_parent_id, d.id, d.`group`, c.trans_date, b.balance_date,
c.department_id, a.company_id,
b.balance, d.debit_sign,d.credit_sign
