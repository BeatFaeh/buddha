-- Einmalige Migration für Installationen, die noch die alten manuell gepflegten
-- Multiple-Choice-Spalten besitzen.
-- Vorher Datenbank-Backup erstellen.

ALTER TABLE tbl_buddhismus
    DROP COLUMN kartentyp,
    DROP COLUMN option_a,
    DROP COLUMN option_b,
    DROP COLUMN option_c,
    DROP COLUMN option_d,
    DROP COLUMN richtige_option;
