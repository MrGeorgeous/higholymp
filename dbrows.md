# DB Row Objects

### `Programme`

id | school | name | code | programme_link | is_paid | years | worldwide
--- | --- | --- | --- | --- | --- | --- | ---
A_I | [see schools](schools.md) | Full name | XX.XX.XX | URL | 1 or 0 | duration of programme | 1 or 0

### `Privilege`

id | school | programme | class | subject | level | first | second | third | ege_subject | year | closed
--- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | ---
A_I | [see schools](schools.md) | `Programme`.id | 9 / 10 / 11 / 9-11 / 10-11 | [see profiles](profiles.md) | 1 / 2 / 3 | 1 / 0 | 1 / 0 | 1 / 0 | [see profiles](profiles.md#ege-subjects) | 20XX | quantity of closed privileges

### `Olympiad`

id | name | level 
--- | --- | --- 
A_I | Full name | 1 / 2 / 3

### `Achievement`

id | olympiad | class | place | year
--- | --- | --- | --- | ---
A_I | `Olympiad` | 9 / 10 / 11 | 1 / 2 / 3 | 20XX
