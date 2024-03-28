ALTER TABLE powerunit_temp
    MODIFY code varchar(50) null,
    MODIFY status int(10) unsigned null,
    MODIFY plate_owner int(10) unsigned null,
    MODIFY form2290 tinyint(1) unsigned null default '0',
    MODIFY ifta_filing_owner int(10) unsigned null;
