# ProgrammeSearcher

*ProgrammeSearcher* is class created to search and filter programmes.

### Interface

#### ProgrammeSearcher(db_name : string)
Init by filename of database.

#### searchProgrammes(town : [int], is_paid : int, years : [int, int], codes : [string]) -> [DB row of `Programmes`]

Searches programmes with following criterias.
If criteria is not set: param is FALSE.

- towns of schools (see [town specs](towns.md))

- is pay programme? (1 or 0)

- year length interval (0 index: from; 1 index: to)

- codes of following type:

  XX.XX.XX: specifies one programme code
  
  XX.XX.00: specifies programme group
  
  XX.00.00: specifies programme category
  
```php
$ps = new ProgrammeSearcher('source.db');
$ps->searchProgrammes([0,3], 0, [3,6], ['01.00.00', '38.03.05']);
```
