SELECT a.id, a.sequence, a.account_no, a.has_children, a.tree_level, a.account_parent_id,
u.balance_date, u.department_id, u.company_id, u.opening_balance, SUM(u.debit), SUM(u.credit) 
FROM accounts a
LEFT JOIN(
SELECT 
a.account_parent_id AS account_id, NULL AS account_parent_id, trans_date, balance_date,
department_id, company_id, SUM(opening_balance) AS opening_balance, SUM(debit) AS debit, SUM(credit) AS credit
FROM vw_transaction_balance a
GROUP BY a.account_parent_id, trans_date, balance_date,
department_id, company_id

UNION

SELECT 
account_id, account_parent_id, trans_date, balance_date,
department_id, company_id, opening_balance, debit,credit
FROM vw_transaction_balance a
) u ON a.id=u.account_id

WHERE trans_date<='2019-01-31' AND department_id IS NULL
GROUP BY a.id, a.sequence, a.account_no, a.has_children, a.tree_level, a.account_parent_id,
u.balance_date, u.department_id, u.company_id, u.opening_balance
ORDER BY sequence