SELECT a.account_parent_id AS account_id, budget_year, department_id, b.company_id,
SUM(jan) AS jan, SUM(feb) AS feb, SUM(mar) AS mar, SUM(apr) AS apr, SUM(may) AS may, SUM(jun) AS jun, SUM(jul) AS jul,
SUM(aug) AS aug, SUM(sep) AS sep, SUM(`oct`) AS `oct`, SUM(nov) AS nov, SUM(`dec`) AS `dec`, SUM(total)

FROM budgets b, accounts a 
WHERE b.account_id=a.id
GROUP BY account_parent_id, budget_year, department_id, company_id
union
SELECT account_id, budget_year, department_id, company_id,
jan, feb, mar, apr, may, jun, jul, aug, sep, `oct`, nov, `dec`, total

FROM budgets