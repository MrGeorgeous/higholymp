# ProgrammeSearcher

*ProgrammeSearcher* is class created to search and filter programmes.

### Interface

#### ProgrammeSearcher(db_name : string)
Init by filename of database.

#### searchProgrammes(town : [int], is_paid : int, years : [int, int], codes : [string]) -> [`Programme`]

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
$ps->searchProgrammes([0,3], 0, [3,6], ['01.00.00', '45.03.00', '38.03.05']);
```

#### getPrivilegesForProgramme(programme : `Programme`, year : int) -> [`PrivilegeTable`]

Returns full review of programme privileges for specified year.

#### getOlympiadsForPrivilege(privilege : `Privilege`) -> [`Olympiad`]

Returns all olympiads which are suitable for specified privilege.

#### getPrivilegesByAchievements(programmes : [`Programme`], achieves : [`Achievement`]) -> [`Privilege`]

Returns all privileges that are covered with specified achievements.

#### getListOfProg
