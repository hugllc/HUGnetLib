SET @oldhex='FC0000', @olddec='16515072', @newhex='FC0100', @newdec='16515328';
UPDATE devices SET id=@newdec, DeviceID=@newhex WHERE DeviceID=@oldhex;
UPDATE e00393700_history SET TestID=@newdec WHERE TestID=@olddec;
UPDATE eDEFAULT_history SET TestID=@newdec WHERE TestID=@olddec;
UPDATE eTEST_history SET TestID=@newdec WHERE TestID=@olddec;
UPDATE sensors SET dev=@newdec WHERE dev=@olddec;

