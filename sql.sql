


DROP PROCEDURE IF EXISTS `INITBNSTRENDS`;

CREATE PROCEDURE `INITBNSTRENDS`()
BEGIN
		DECLARE fetchSeqOk BOOLEAN;
		DECLARE _COM_ID INT;

		DECLARE _COM_ID_CURSOR CURSOR FOR SELECT id FROM e_enterprise WHERE user_id IS NOT NULL AND status<>'-3';#企业游标

		DECLARE CONTINUE HANDLER FOR NOT FOUND SET fetchSeqOk = true;#结束标识
		SET fetchSeqOk = FALSE;

    OPEN _COM_ID_CURSOR;
    _COM_ID_CURSOR:LOOP
				IF fetchSeqOk THEN
						LEAVE _COM_ID_CURSOR;
				ELSE
						FETCH _COM_ID_CURSOR INTO _COM_ID;#把企业ID值覆给_COM_ID
						#处理资讯
						BEGIN
								DECLARE _INFO_ID INT;
								DECLARE _INFO_SHORTSUBJECT VARCHAR(255);
								DECLARE _INFO_IMGPATH VARCHAR(128);
								DECLARE _INFO_ISSUEDATE DATETIME;
								DECLARE fetchSeqOk BOOLEAN DEFAULT 'inner'; 
								DECLARE _INFO_CURSOR CURSOR FOR SELECT id,shortsubject,micropic,issuedate FROM hc_info WHERE issue_id=_COM_ID AND issuedate>'2012-01-01' AND status<>'-3';
								DECLARE CONTINUE HANDLER FOR NOT FOUND SET fetchSeqOk = true;#结束标识
								SET fetchSeqOk = FALSE;
								OPEN _INFO_CURSOR;
								_INFO_CURSOR:LOOP
										FETCH _INFO_CURSOR INTO _INFO_ID,_INFO_SHORTSUBJECT,_INFO_IMGPATH,_INFO_ISSUEDATE;
										IF fetchSeqOk THEN
												LEAVE _INFO_CURSOR;
										ELSE
												INSERT INTO bns_trends(OBJECT_ID,OBJECT_TYPE,SUMMARY,IMG_PATH,COM_ID,VOTE_NUM,COMMEND_NUM,COMMENT_NUM,CREATE_TIME,STATUS)
												VALUES(_INFO_ID,'3',_INFO_SHORTSUBJECT,_INFO_IMGPATH,_COM_ID,0,0,0,_INFO_ISSUEDATE,'1');
										END IF;
								END LOOP;
						END;

						#处理供应
						BEGIN
								DECLARE _SALE_ID INT;
								DECLARE _SALE_TITLE VARCHAR(255);
								DECLARE _SALE_IMGPATH VARCHAR(128);
								DECLARE _SALE_ISSUEDATE DATETIME;
								DECLARE fetchSeqOk BOOLEAN DEFAULT 'inner'; 
								DECLARE _SALE_CURSOR CURSOR FOR SELECT id,title,picurl,issuedate FROM e_product_sale WHERE e_id=_COM_ID AND issuedate>'2012-01-01' AND status<>'-3';
								DECLARE CONTINUE HANDLER FOR NOT FOUND SET fetchSeqOk = true;#结束标识
								SET fetchSeqOk = FALSE;
								OPEN _SALE_CURSOR;
								_SALE_CURSOR:LOOP
										FETCH _SALE_CURSOR INTO _SALE_ID,_SALE_TITLE,_SALE_IMGPATH,_SALE_ISSUEDATE;
										IF fetchSeqOk THEN
												LEAVE _SALE_CURSOR;
										ELSE												
												INSERT INTO bns_trends(OBJECT_ID,OBJECT_TYPE,SUMMARY,IMG_PATH,COM_ID,VOTE_NUM,COMMEND_NUM,COMMENT_NUM,CREATE_TIME,STATUS)
												VALUES(_SALE_ID,'1',_SALE_TITLE,_SALE_IMGPATH,_COM_ID,0,0,0,_SALE_ISSUEDATE,'1');
										END IF;
								END LOOP;
						END;

						#处理求购
						BEGIN
								DECLARE _BUY_ID INT;
								DECLARE _BUY_TITLE VARCHAR(255);
								DECLARE _BUY_IMGPATH VARCHAR(128);
								DECLARE _BUY_ISSUEDATE DATETIME;
								DECLARE fetchSeqOk BOOLEAN DEFAULT 'inner'; 
								DECLARE _BUY_CURSOR CURSOR FOR SELECT id,title,picurl,issuedate FROM e_product_buy WHERE e_id=_COM_ID AND status<>'-3';
								DECLARE CONTINUE HANDLER FOR NOT FOUND SET fetchSeqOk = true;#结束标识
								SET fetchSeqOk = FALSE;
								OPEN _BUY_CURSOR;
								_BUY_CURSOR:LOOP
										FETCH _BUY_CURSOR INTO _BUY_ID,_BUY_TITLE,_BUY_IMGPATH,_BUY_ISSUEDATE;
										IF fetchSeqOk THEN
												LEAVE _BUY_CURSOR;
										ELSE												
												INSERT INTO bns_trends(OBJECT_ID,OBJECT_TYPE,SUMMARY,IMG_PATH,COM_ID,VOTE_NUM,COMMEND_NUM,COMMENT_NUM,CREATE_TIME,STATUS)
												VALUES(_BUY_ID,'1',_BUY_TITLE,_BUY_IMGPATH,_COM_ID,0,0,0,_BUY_ISSUEDATE,'1');
										END IF;
								END LOOP;
						END;
				END IF;
		END LOOP;
END;






--User info selector
--Selcct User info by uid
--Return : 1.base info ;2.payway; 3.address 4.redpacket info 
--1.base info 

Consists of a pair of COLUMNS
ignore columns
key value 

desc information_schema.`COLUMNS` 

SELECT * FROM information_schema.`COLUMNS` WHERE TABLE_SCHEMA = 'gzb_ol_0824' AND table_name = 'gzb_user_info'

prepare stmt from @update_sql; 
	EXECUTE stmt;     
	deallocate prepare stmt;   

desc information_schema.`COLUMNS`

DROP TABLE `__select`
CREATE TABLE `__select`
AS
SELECT * FROM gzb_user_info WHERE uid= 89 limit 1

SELECT * FROM information_schema.`COLUMNS` WHERE TABLE_SCHEMA = 'gzb_ol_0824' AND table_name = '__select'


CALL select_info('phone','18767135799');

=========================================================================================================
SELECT * FROM __tmp

##__tmp 表的操作

DROP PROCEDURE IF EXISTS `__tmp_process`;
CREATE PROCEDURE `__tmp_process` (
	IN _in_operation VARCHAR(30)
)
BEGIN
	IF _in_operation = 'DROP' THEN 
		DROP TABLE IF EXISTS `__tmp`;
	ELSEIF _in_operation = 'CLEAR' THEN 
		DELETE FROM __tmp;
		ALTER TABLE __tmp AUTO_INCREMENT = 1;
	ELSEIF _in_operation = 'SELECT' THEN 
		SELECT *  FROM __tmp;
	ELSEIF _in_operation = 'CREATE' THEN 
		DROP TABLE IF EXISTS `__tmp`;
		CREATE TABLE `__tmp`( 
			id int(11) unsigned NOT NULL AUTO_INCREMENT ,
			_key_1 VARCHAR(100) NULL ,
			_key_2 VARCHAR(100) NULL ,
			_key_3 VARCHAR(100) NULL ,
			_key_4 VARCHAR(100) NULL ,
			_key_5 VARCHAR(100) NULL ,
			_key_6 VARCHAR(100) NULL ,
			_key_7 VARCHAR(100) NULL ,
			_key_8 VARCHAR(100) NULL ,
			_key_9 VARCHAR(100) NULL ,
			PRIMARY KEY(id)
		) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='临时表';
	ELSEIF _in_operation = 'INIT' THEN 
		BEGIN
			DECLARE _max int ;
			DECLARE _i int ;
	
			SET _i = 0;
			SET _max = 30;
			SET @_sql = 'INSERT INTO __tmp (`_key_1`)VALUES(NULL)';
			SET _max = _max - 1;
			_cur_loop:LOOP
				IF _i = _max THEN
					LEAVE _cur_loop;
				ELSE
					SET _i = _i + 1; 
					SET @_sql = CONCAT( @_sql ,',(NULL)');
				END IF;
			END LOOP;
			prepare stmt from @_sql; 
			EXECUTE stmt;     
			deallocate prepare stmt;   
		END;
	END IF;
END


SELECT SUBSTRING('asd',0,-1)

SELECT SUBSTR('asd',-1,0)


CALL __tmp_process('INIT');

CALL __tmp_process('DROP');

CALL __tmp_process('CREATE');

CALL __tmp_process('SELECT');

CALL __tmp_process('CLEAR');

SELECT database();

SELECT company,QUOTE(company) FROM gzb_user_info WHERE uid = 105045;

SELECT company FROM gzb_user_info WHERE uid = 105045;


##将临时表一行写入tmp表的一列
DROP PROCEDURE IF EXISTS `__tmp_table_select_first`;
CREATE PROCEDURE `__tmp_table_select_first` (
	in _in_table_name VARCHAR (30),
	in _in_key_name VARCHAR (30)
)
BEGIN
	declare done int;  
	declare _COLUMN_NAME VARCHAR(40);
	DECLARE _key VARCHAR(20) ; 
	DECLARE _i int ; 
	
	declare cur_test CURSOR for 
		SELECT COLUMN_NAME FROM information_schema.`COLUMNS` WHERE TABLE_SCHEMA = DATABASE() AND table_name = _in_table_name;
  declare continue handler FOR SQLSTATE '02000' SET done = 1;  

	set _i = 0 ;
	SET _key = _in_key_name;
	OPEN cur_test;
	_cur_loop:LOOP
		FETCH cur_test INTO _COLUMN_NAME;
		IF done = 1 THEN
			LEAVE _cur_loop;
		ELSE
			SET _i = _i + 1; 
			SET @sql = CONCAT('SELECT `',_COLUMN_NAME,'` INTO @dt FROM ',_in_table_name,' limit 1 ');
			prepare stmt from @sql; 
			EXECUTE stmt;     
			deallocate prepare stmt;  
			
			IF @dt is NULL THEN
				SET @dt = 'NULL';
			END IF;
			SET @value = QUOTE( CONCAT(_COLUMN_NAME,'=',@dt));
			SET @sql = CONCAT('UPDATE __tmp SET ',_key,'=',@value,' WHERE id = ',_i);
			prepare stmt from @sql; 
			EXECUTE stmt;     
			deallocate prepare stmt;   
		END IF;
	END LOOP;
	close cur_test;   
END;



CALL __tmp_table_select_first('gzb_user_info','_key_2');



DROP PROCEDURE IF EXISTS `test`;
CREATE PROCEDURE 	`test`(
	IN _in_uid int 
)  
BEGIN   
	CALL __tmp_process('CREATE');
	CALL __tmp_process('INIT');
	DROP VIEW IF EXISTS `__tmp_select_01`;
	SET @sql = CONCAT('CREATE VIEW __tmp_select_01 AS SELECT * FROM gzb_user_info WHERE uid = ',_in_uid);
	prepare stmt from @sql; 
	EXECUTE stmt;      
	CALL __tmp_table_select_first('__tmp_select_01','_key_1');

	DROP VIEW IF EXISTS `__tmp_select_01`;
	SET @sql = CONCAT('CREATE VIEW __tmp_select_01 AS SELECT * FROM gzb_user_account WHERE uid = ',_in_uid);
	prepare stmt from @sql; 
	EXECUTE stmt;     
	CALL __tmp_table_select_first('__tmp_select_01','_key_2');

	DROP VIEW IF EXISTS `__tmp_select_01`;
	SET @sql = CONCAT('CREATE VIEW __tmp_select_01 AS SELECT * FROM gzb_user_address WHERE type = 1 AND uid =',_in_uid);
	prepare stmt from @sql; 
	EXECUTE stmt;     
	deallocate prepare stmt; 
	CALL __tmp_table_select_first('__tmp_select_01','_key_3');
	DROP VIEW IF EXISTS `__tmp_select_01`;
	CALL __tmp_process('SELECT');
END;

CALL test(105045);


SET @sql = 'SELECT `name` INTO @dt FROM gzb_user_info WHERE `name` > "" limit 1 ;';
			##INSERT INTO `__tmp` (_key)VALUES(_COLUMN_NAME);
	prepare stmt from @sql; 
	EXECUTE stmt;     
	deallocate prepare stmt;   
	SELECT @dt;



show variables like '%char%'

DROP PROCEDURE IF EXISTS `test`;
CREATE PROCEDURE 	`test`()  
BEGIN   
	DECLARE forward_seconds varchar(222);  

	#SELECT `name` INTO forward_seconds FROM gzb_user_info WHERE `name` > '' limit 1 ;
	SELECT forward_seconds;
	SET @sql = 'SELECT `name` INTO @dt FROM gzb_user_info WHERE `name` > "" limit 1 ;';
			##INSERT INTO `__tmp` (_key)VALUES(_COLUMN_NAME);
	prepare stmt from @sql; 
	EXECUTE stmt;     
	deallocate prepare stmt;   
	SELECT @dt;
END;
CALL test();



SELECT * FROM __tmp

SELECT CONCAT('name=',name) AS name FROM gzb_user_info WHERE uid = 18 limit 1

=========================================================================================================