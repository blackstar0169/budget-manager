## Settings
Starting banque amount
Starting month
Admin credentials

## Outcomes and incomes
table : io
properties : Amout, label, recurrence (monthly, quarterly, yearly)

## Goal
table : date
properties: Date, label, amount

## Future

Display a table of previous month (until app start) and the next 24 month

Date | Predicted | Reality | Difference | Beneficial previous month

**Algo :**
For each month from the last calculated month to nowMonth + 24 month
    currentMonthMoney = previousMonthMoney
    For each registed I/O
        If the current I/O is in this month
            currentMonthMoney += io.amount
    If month < nowMonth
        Display currentMonthMoney and ask for manual adjustment

table : financial_situation
properties : date, created_at, amount, predicted

## Export / Impot backup


