-- migrate data after creation of new structure

-- basics
insert into `kw_pedigree` (`pedigree_short`, `pedigree_name`, `pedigree_family`,
                           `pedigree_birth`, `pedigree_successes`, `pedigree_sex`, `pedigree_text`)
Select `id` as `pedigree_short`,
       `name` as `pedigree_name`,
       `kennel` as `pedigree_family`,
       `birth` as `pedigree_birth`,
       `trials` as `pedigree_successes`,
       `sex` as `pedigree_sex`,
       `text` as `pedigree_text`
from `kal_pedigree`;

-- parents/children
update `kw_pedigree`
    inner join `kal_pedigree` on `kw_pedigree`.`pedigree_short` = `kal_pedigree`.`id`
    inner join `kw_pedigree` as `child` on `child`.`pedigree_short` = `kal_pedigree`.`father_id`
set `kw_pedigree`.`pedigree_father_id` = `child`.`pedigree_id`;

update `kw_pedigree`
    inner join `kal_pedigree` on `kw_pedigree`.`pedigree_short` = `kal_pedigree`.`id`
    inner join `kw_pedigree` as `child` on `child`.`pedigree_short` = `kal_pedigree`.`mother_id`
set `kw_pedigree`.`pedigree_mother_id` = `child`.`pedigree_id`;
