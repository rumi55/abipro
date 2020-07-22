
SELECT a.id AS account_id, c.department_id, a.company_id, SUM(c.balance) AS balance
FROM accounts a 
	LEFT JOIN accounts b ON a.id=b.account_parent_id
	LEFT JOIN balances c ON b.id=c.account_id
WHERE 
	a.company_id=c.company_id
GROUP BY a.id, a.company_id, c.department_id

UNION

SELECT a.id AS account_id, c.department_id, a.company_id, c.balance AS balance
FROM accounts a 
	LEFT JOIN balances c ON a.id=c.account_id
WHERE 
	a.company_id=c.company_id
