INSERT INTO `gzb_adm_category` ( `cat_name`, `cat_pid`, `cat_level`, `status`, `sort`, `code`, `created`, `updated`) 
VALUES ( 'QQ号码错误', '2', '0', '0', NULL, 'qq_error', now(), NULL);


CALL select_info('uid',165682);

select `id`
, `orderid`
, `pay_price`
, `dalay_price`
, `pay_at` 
from `gzb_order_pay` 
where `uid` = '165708' 
and `orderid` = '1444376065743289' 
and `status` = '0' 
and `pay_at` <= '2016-02-06 00:00:00' order by `pay_at` asc


SELECT * FROM gzb_order_pay p WHERE uid = 165682 AND ((`status` > 0 AND TO_DAYS(payment_at) - TO_DAYS(pay_at) > 5 ) or ( `status` = 0 AND TO_DAYS(now()) - TO_DAYS(pay_at) > 5))

165682



SELECT COUNT(DISTINCT AC.uid) AS count
FROM (`gzb_user_account` AC)
LEFT JOIN `gzb_user_info` I ON `AC`.`uid`=`I`.`uid`
INNER JOIN `gzb_user_credit` C ON `AC`.`uid`=`C`.`uid`
LEFT JOIN `gzb_order_main` M ON `AC`.`uid`=`M`.`uid` 
WHERE AC.account_status =0 and M.uid is null 




CALL select_info('phone','15091894602');


EXPLAIN SELECT  COUNT(DISTINCT AC.uid) AS count
FROM (`gzb_user_account` AC)
LEFT JOIN `gzb_user_info` I ON `AC`.`uid`=`I`.`uid`
INNER JOIN `gzb_user_credit` C ON `AC`.`uid`=`C`.`uid`
LEFT JOIN `gzb_order_main` M ON `AC`.`uid`=`M`.`uid` and M.status<3 and M.uid is null
LEFT JOIN `gzb_user_credit_task` CRT ON `AC`.`uid`=`CRT`.`uid` and CRT.uid is null 
WHERE AC.account_status =0
AND I.identity is not null

EXPLAIN
SELECT  COUNT(DISTINCT AC.uid) AS count
FROM (`gzb_user_account` AC)
LEFT JOIN `gzb_user_info` I ON `AC`.`uid`=`I`.`uid`
INNER JOIN `gzb_user_credit` C ON `AC`.`uid`=`C`.`uid`
LEFT JOIN `gzb_order_main` M ON `AC`.`uid`=`M`.`uid` and M.status<3 and M.uid is null
LEFT JOIN `gzb_user_credit_task` CRT ON `AC`.`uid`=`CRT`.`uid` 
WHERE AC.account_status =0 and  CRT.`status` is null ##(CRT.status  != 0 AND CRT.status  != 1 AND CRT.status  != 2 AND CRT.status  != 3 )
AND I.identity is not null 
SHOW CREATE TABLE gzb_user_credit_task
20284

GROUP BY `AC`.`uid`


SELECT * FROM (
						SELECT `gzb_order_pay`.* FROM `gzb_order_pay`
						RIGHT JOIN `gzb_user_payway_holding` ON `gzb_user_payway_holding`.uid = `gzb_order_pay`.uid
						JOIN `gzb_order_main` ON `gzb_order_pay`.orderid = `gzb_order_main`.orderid
						WHERE `gzb_order_pay`.`pay_at` <= '2015-09-29'
						AND `gzb_order_pay`.status = 0
						AND `gzb_order_main`.`status` = 2
						ORDER BY `gzb_order_pay`.`pay_at` DESC
				)
				pay GROUP BY `orderid`















CALL reset_order_date('1443519189983954',-70);

delete_account 

DROP PROCEDURE IF EXISTS `set_table_date`;
CREATE PROCEDURE 	`set_table_date`(
	IN in_table_name VARCHAR(30),
	IN in_col_name VARCHAR(30),
	IN in_select_col VARCHAR(30),
  IN in_select_val VARCHAR(30),
	IN in_days int(11)
)  
BEGIN   
	DECLARE forward_seconds INT(11);  
	DECLARE exec_sql VARCHAR(500);   
	DECLARE has_updated_at VARCHAR(50);
	DECLARE updated_at_val VARCHAR(50);
	SET forward_seconds = 86400 * in_days;
	SELECT COLUMN_NAME INTO has_updated_at FROM INFORMATION_SCHEMA.COLUMNS  WHERE   table_schema = DATABASE() AND TABLE_NAME = in_table_name AND COLUMN_NAME = 'updated_at';
	IF has_updated_at = 'updated_at' AND in_col_name != 'updated_at' THEN
		SET @update_sql =  CONCAT('UPDATE ',in_table_name,' SET updated_at = updated_at,',in_col_name,' = FROM_UNIXTIME(UNIX_TIMESTAMP(',in_col_name,') + ',forward_seconds,')',' WHERE ',in_select_col,' = "',in_select_val,'"');
	ELSE 
		SET @update_sql =  CONCAT('UPDATE ',in_table_name,' SET ',in_col_name,' = FROM_UNIXTIME(UNIX_TIMESTAMP(',in_col_name,') + ',forward_seconds,')',' WHERE ',in_select_col,' = "',in_select_val,'"');
	END IF;
	prepare stmt from @update_sql; 
	EXECUTE stmt;     
	deallocate prepare stmt;   
END; 

DROP PROCEDURE IF EXISTS `reset_order_date`;
CREATE PROCEDURE 	reset_order_date(IN in_orderid VARCHAR(30),IN in_days INT(11))  
BEGIN  
	START TRANSACTION ;
	CALL set_table_date('gzb_order_main','pay_at','orderid',in_orderid,in_days);
	CALL set_table_date('gzb_order_main','created_at','orderid',in_orderid,in_days);
	CALL set_table_date('gzb_order_main','updated_at','orderid',in_orderid,in_days);

	CALL set_table_date('gzb_order_pay','created_at','orderid',in_orderid,in_days);
	CALL set_table_date('gzb_order_pay','pay_at','orderid',in_orderid,in_days);
	CALL set_table_date('gzb_order_pay','payment_at','orderid',in_orderid,in_days);
	CALL set_table_date('gzb_order_pay','updated_at','orderid',in_orderid,in_days);

	CALL set_table_date('gzb_order_periods','created_at','orderid',in_orderid,in_days);
	CALL set_table_date('gzb_order_periods','updated_at','orderid',in_orderid,in_days);

	CALL set_table_date('gzb_order_pay_task','created_at','orderid',in_orderid,in_days);
	CALL set_table_date('gzb_order_pay_task','updated_at','orderid',in_orderid,in_days);
	CALL set_table_date('gzb_order_task','created_at','orderid',in_orderid,in_days);
	CALL set_table_date('gzb_order_task','updated_at','orderid',in_orderid,in_days);
	UPDATE gzb_order_pay SET dalay_price = 0 WHERE orderid = in_orderid AND pay_at >= now();
	COMMIT;
END; 

SELECT SUBSTRING('123',2)

UPDATE gzb_ump_withholding SET media_id = SUBSTRING(media_id,2)

UPDATE gzb_ump_withholding SET identity_code = SUBSTRING(identity_code,2)

CALL select_info('phone','14122222331');

SELECT * FROM gzb_ol_0810.gzb_adm_actions ORDER BY actionid DESC;
SELECT * FROM gzb_ol_0824.gzb_adm_actions ORDER BY actionid DESC;

SELECT TO_DAYS(now())
13688888888
SELECT * FROM gzb_user_account WHERE uid = 106

CALL select_info('phone','18368251013');

SELECT a.uid,aa.phone FROM 
(
SELECT * 
FROM gzb_order_pay 
WHERE (`status` > 0 AND TO_DAYS(payment_at) - TO_DAYS(pay_at) > 5 ) or ( `status` > 0 AND TO_DAYS(now())
 - TO_DAYS(pay_at) > 5) 
) as  a JOIN gzb_user_account aa on aa.uid = a.uid 


`created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '时间',





SELECT C.uid,A.phone,I.`name`,C.rest_amount FROM (
	SELECT * FROM gzb_user_repayment_cash WHERE status = 1 ORDER BY created_at DESC 
) as C 
LEFT JOIN gzb_user_account A ON A.uid = C.uid
LEFT JOIN gzb_user_info I ON I.uid = c.uid
GROUP BY C.uid
ORDER BY A.created_at;

DISTINCT

D:\wnmp\PHP5\extras\ssl\openssl.cnf

SELECT card ,SUBSTRING(card,2) FROM gzb_user_payway_holding LIMIT 1

UPDATE gzb_user_payway_holding 
SET card = SUBSTRING(card,2)



INSERT INTO `gzb_adm_category` ( `cat_name`, `cat_pid`, `cat_level`, `status`, `sort`, `code`, `created`, `updated`) 
VALUES ( '入职时间错误', '2', '0', '0', NULL, 'join_at_error', now(), NULL);
INSERT INTO `gzb_adm_category` ( `cat_name`, `cat_pid`, `cat_level`, `status`, `sort`, `code`, `created`, `updated`) 
VALUES ( '紧急联系人错误', '2', '0', '0', NULL, 'emergency_contact_error', now(), NULL);

ALTER TABLE gzb_adm_category MODIFY COLUMN `code` varchar(50) DEFAULT NULL COMMENT '代号'

ALTER TABLE `gzb_adm_category` MODIFY COLUMN `code`  varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '代号' AFTER `sort`;


ALTER TABLE gzb_user_info_refund DROP COLUMN `join_at`;
ALTER TABLE gzb_user_info_refund ADD COLUMN `join_at` VARCHAR(30) DEFAULT NULL COMMENT '入职时间' AFTER `company`;
ALTER TABLE gzb_user_info_refund ADD COLUMN `emergency_contact` VARCHAR(30) DEFAULT NULL COMMENT '紧急联系人' AFTER `join_at`;

18767135775

CALL select_info('phone','18510021001');


CALL select_info('phone','18767135775');


CALL select_info('uid',38);

SELECT * FROM (
						SELECT `gzb_order_pay`.* FROM `gzb_order_pay`
						JOIN `gzb_user_payway_holding` ON `gzb_user_payway_holding`.uid = `gzb_order_pay`.uid
						JOIN `gzb_order_main` ON `gzb_order_pay`.orderid = `gzb_order_main`.orderid
						WHERE `gzb_order_pay`.`pay_at` <= '2015-09-14'
						AND `gzb_order_pay`.status = '0'
						AND `gzb_order_main`.`status` = 2
						ORDER BY `gzb_order_pay`.`pay_at` DESC
				)
				pay GROUP BY `orderid`



INSERT INTO `gzb_ol_0824`.`gzb_user_repayment_coupon` ( `uid`, `amount`, `for_amount`, `gain_type`, `pay_order_id`, `pay_task_id`, `status`, `created_at`, `expire_at`) VALUES ( '951', '40', '2000', '1', NULL, NULL, '0', '2015-09-09 17:27:37', '2015-12-08 17:27:37');
INSERT INTO `gzb_ol_0824`.`gzb_user_repayment_coupon` ( `uid`, `amount`, `for_amount`, `gain_type`, `pay_order_id`, `pay_task_id`, `status`, `created_at`, `expire_at`) VALUES ( '951', '60', '3000', '1', NULL, NULL, '0', '2015-09-09 17:27:37', '2015-12-08 17:27:37');
INSERT INTO `gzb_ol_0824`.`gzb_user_repayment_coupon` (`uid`, `amount`, `for_amount`, `gain_type`, `pay_order_id`, `pay_task_id`, `status`, `created_at`, `expire_at`) VALUES ( '951', '80', '4000', '1', NULL, NULL, '0', '2015-09-09 17:27:37', '2015-12-08 17:27:37');
INSERT INTO `gzb_ol_0824`.`gzb_user_repayment_coupon` (`uid`, `amount`, `for_amount`, `gain_type`, `pay_order_id`, `pay_task_id`, `status`, `created_at`, `expire_at`) VALUES ( '951', '100', '5000', '1', NULL, NULL, '0', '2015-09-09 17:27:37', '2015-12-08 17:27:37');


SELECT * FROM (
		SELECT `gzb_order_pay`.* FROM `gzb_order_pay`
		RIGHT JOIN `gzb_user_payway_holding` ON `gzb_user_payway_holding`.uid = `gzb_order_pay`.uid
		JOIN `gzb_order_main` ON `gzb_order_pay`.orderid = `gzb_order_main`.orderid
		WHERE `gzb_order_pay`.`pay_at` <= '2015-09-11'
		AND `gzb_order_pay`.status = 0
		AND `gzb_order_main`.`status` = 2
		ORDER BY `gzb_order_pay`.`pay_at` DESC
)pay GROUP BY `orderid`












CALL select_info('uid','951');












 SELECT  @@FOREIGN_KEY_CHECKS;

CALL delete_account('18767135785');

SET FOREIGN_KEY_CHECKS=0;
ALTER TABLE `gzb_order_pay_task` ADD COLUMN `deduction_price`  decimal(11,2) NOT NULL DEFAULT 0.00 COMMENT '抵扣金额' AFTER `pay_price`;
SET FOREIGN_KEY_CHECKS=1;


DROP TABLE IF EXISTS 	`gzb_user_repayment_coupon`;
CREATE TABLE `gzb_user_repayment_coupon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '账户ID',
  `amount` int(11) DEFAULT 0 COMMENT '金额',
  `for_amount` int(11) DEFAULT 0 COMMENT '适用的还款金额金额',
	`gain_type` SMALLINT(5) DEFAULT 0 COMMENT '获得的事件类别',
  `pay_order_id` bigint(26) DEFAULT NULL COMMENT '所使用的订单流水号',
  `pay_task_id` bigint(26) DEFAULT NULL COMMENT '使用的支付流水表ID',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '使用状态状态0：未使用，1.已使用',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '获得时间',
  `expire_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '过期时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='用户代金券表';

DROP TABLE IF EXISTS 	`gzb_user_repayment_cash`;
CREATE TABLE `gzb_user_repayment_cash` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '账户ID',
  `amount` int(11) DEFAULT 0 COMMENT '金额',
	`gain_type` SMALLINT(5) DEFAULT 0 COMMENT '获得的事件类别',
	`pay_order_id` bigint(26) DEFAULT NULL COMMENT '所使用的订单流水号',
  `pay_task_id` bigint(26) DEFAULT NULL COMMENT '使用的支付流水表ID',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '使用状态状态0：未使用，1.已使用',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '获得时间',
  `expire_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '过期时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='用户还款现金';
  


INSERT INTO `gzb_ol_0824`.`gzb_user_data` ( `uid`, `type`, `info`, `created_at`, `updated_at`, `picurl`, `status`) VALUES ( '165682', '0', NULL, '2015-08-24 10:01:31', '2015-08-24 10:01:31', '/datum/165658/165658_1440381691836102.jpg', '0');
INSERT INTO `gzb_ol_0824`.`gzb_user_data` ( `uid`, `type`, `info`, `created_at`, `updated_at`, `picurl`, `status`) VALUES ( '165682', '1', NULL, '2015-08-24 10:01:45', '2015-08-24 10:01:45', '/datum/165658/165658_1440381705310125.jpg', '0');
INSERT INTO `gzb_ol_0824`.`gzb_user_data` ( `uid`, `type`, `info`, `created_at`, `updated_at`, `picurl`, `status`) VALUES ( '165682', '2', NULL, '2015-08-24 10:03:31', '2015-08-24 10:03:31', '/datum/165658/165658_1440381811234074.jpg', '0');
INSERT INTO `gzb_ol_0824`.`gzb_user_data` ( `uid`, `type`, `info`, `created_at`, `updated_at`, `picurl`, `status`) VALUES ( '165682', '3', NULL, '2015-08-24 10:04:04', '2015-08-24 10:04:04', '/datum/165658/165658_1440381844158044.jpg', '0');






DROP TABLE IF EXISTS 	`gzb_user_repayment_cash`;
CREATE TABLE `gzb_user_repayment_cash` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '账户ID',
  `inout_amount` int(11) DEFAULT 0 COMMENT '收入支出金额，收入为正数，支出为负数',
  `absolute_amount` int(11) DEFAULT 0 COMMENT '金额绝对值',
	`rest_amount` decimal(11,2)  DEFAULT 0 COMMENT '余额',
	`event_type` SMALLINT(5) DEFAULT 0 COMMENT '事件类别，0还款，1分享获得',
  `inout` tinyint(2) NOT NULL DEFAULT 0 COMMENT '收入支出0：支出，1.收入',
  `status` tinyint(2) NOT NULL DEFAULT 0 COMMENT '事件状态0：失败，1.成功',
  `pay_order_id` bigint(26) DEFAULT NULL COMMENT '还款的订单流水号',
  `pay_task_id` bigint(26) DEFAULT NULL COMMENT '还款的支付流水号',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='用户还款现金';
  





deduction_price
 
DROP TABLE IF EXISTS 	`gzb_order_pay_deduction`;
CREATE TABLE `gzb_order_pay_deduction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '账户ID',
	`orderid` bigint(26) NOT NULL COMMENT '订单ID',
  `amount` int(11) DEFAULT 0 COMMENT '金额',
	`gain_type` SMALLINT(5) DEFAULT 0 COMMENT '获得的事件类别',
  `pay_task_id` bigint(26) DEFAULT '0' COMMENT '使用的支付流水表ID',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '使用状态状态0：未使用，1.已使用',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '获得时间',
  `expire_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '过期时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=90150 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='用户还款现金';


CREATE TABLE `gzb_order_pay_task` (
  `id` bigint(26) NOT NULL,
  `uid` int(11) NOT NULL,
  `orderid` bigint(26) NOT NULL COMMENT '订单ID',
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0：月还，1：全部还清，2：银行代扣生成的月还数据',
  `pay_type` tinyint(2) DEFAULT '0' COMMENT '0：支付宝，  1：一键支付， 2：代扣，  3：线下还款',
  `pay_price` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '偿还金额(本金+滞纳金)',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '0：未支付，1：支付成功',
  `payid` varchar(50) DEFAULT NULL COMMENT 'order_pay id',
  `difference` decimal(11,2) DEFAULT '0.00' COMMENT '线下还款差价',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单还款流水';


























UPDATE gzb_user_account SET password = '35c2cb8001987f43564a4a480751bd98',salt ='RAXqq2DE'


create DATABASE gzb_ol_0906


select `gzb_order_main`.`orderid`, `gzb_order_periods`.`payday` 
from `gzb_order_main` 
inner join `gzb_order_periods` on `gzb_order_periods`.`orderid` = `gzb_order_main`.`orderid` 
where `gzb_order_main`.`uid` = '951' order by `gzb_order_main`.`created_at` desc limit 1





SELECT  a.uid,a.phone,d.picurl
FROM gzb_user_data d JOIN gzb_user_account a on a.uid = d.uid
WHERE type >= 30 AND a.uid = 951



SELECT * FROM gzb_area a  
JOIN gzb_area b on ( a.cid != b.cid AND  a.`name` = b.`name`)
WHERE a.type = 2 AND b.type = 2 AND a.cid > b.cid


3194


SELECT CONCAT(a3.name,a2.name,a1.name) as name , a1.cid 
FROM gzb_area a1 
JOIN gzb_area a2 on a1.cup = a2.cid
JOIN gzb_area a3 on a2.cup = a3.cid
WHERE a1.cid = 1209

SELECT MAX(cid) FROM gzb_area 
4781  562
5343

SELECT * FROM gzb_user_address WHERE id = area_id AND type = 1;

SELECT * FROM gzb_user_account WHERE uid in (1442,
1591,
10777,
133806)

SELECT * FROM gzb_user_address WHERE id = area_id AND area_id >= 3571 AND type = 1;

SELECT * FROM gzb_user_address WHERE  area_id >= 3571 ORDER BY updated_at DESC

黑龙江省 哈尔滨市 宾  县 宾州镇南大街64号
SELECT * FROM gzb_area WHERE name like '%仙桃市%'

河南省省直辖神农架济源市荆梁南街1号
黑龙江省 哈尔滨市 宾  县 宾州镇南大街64号

SELECT * FROM gzb_user_address WHERE  area_id >= 3571 AND type = 1;


//代金券，现金红包


CREATE TABLE `gzb_user_redpacket` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '账户ID',
  `amount` int(11) DEFAULT '5' COMMENT '红包金额',
  `type` tinyint(2) DEFAULT '1' COMMENT '红包类别，1 填写兑换码获得，2推广获得，红包3(填写蓝色兑换码获得)，红包4(填写蓝色兑换码后，蓝色天使获得)，红包5(填写蓝色兑换码的用户，填写提现资料后蓝色天使获得)',
  `relation_id` int(11) DEFAULT NULL COMMENT '被推广的uid',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '提现状态0：未提现，1.提现中，2.已提现',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '获得时间',
  `expire_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '过期时间',
  `withdraw_id` int(11) DEFAULT NULL COMMENT '提现审核id',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=122 DEFAULT CHARSET=utf8 COMMENT='用户红包表';























\\\
CREATE TABLE ``

SELECT i.uid,a.phone,i.`name`,i.identity,p.card,credit_status_info,'0' `status`,'' '' message
FROM gzb_user_info i 
JOIN gzb_user_payway p on i.uid = p.uid
JOIN gzb_user_account a on i.uid = a.uid
WHERE i.created_at >= '2015-08-01 00:00:00' 
AND i.created_at <= '2015-08-11 00:00:00'
AND credit_status = 5 

or  credit_status = 3


	SELECT * FROM gzb_ump_commonvalidate 
	WHERE created_at >= '2015-08-01 00:00:00' 
	AND created_at <= '2015-08-11 00:00:00'
	AND ret_code != '0000' AND type = 4

SELECT * FROM (
	SELECT * FROM gzb_ump_commonvalidate 
	WHERE created_at >= '2015-08-01 00:00:00' 
	AND created_at <= '2015-08-11 00:00:00'
	AND ret_code != '0000'
) a
JOIN (
	SELECT * FROM gzb_ump_commonvalidate 
	WHERE created_at >= '2015-08-01 00:00:00' 
	AND created_at <= '2015-08-11 00:00:00'
	AND ret_code != '0000'
) b
on (a.identity_code = b.identity_code AND a.common_id != b.common_id)




CREATE TABLE ``

SELECT i.uid,a.phone,i.`name`,i.identity,p.card,'0' `status`,'' '' message
FROM gzb_user_info i 
JOIN gzb_user_payway p on i.uid = p.uid
JOIN gzb_user_account a on i.uid = a.uid
WHERE i.created_at >= '2015-08-01 00:00:00' 
AND i.created_at <= '2015-08-11 00:00:00'
AND credit_status = 5 or  credit_status = 3





SELECT count(*) 
FROM gzb_user_info i 
WHERE i.created_at >= '2015-08-01 00:00:00' 
AND i.created_at <= '2015-08-11 00:00:00'
AND credit_status = 5 or  credit_status = 3





SELECT FROM_UNIXTIME('1440655200')

165686
165685
165684
140842
CALL select_info('phone','117273');


CALL delete_account('14788046411');

CALL delete_account('18767135780');


CALL delete_account('18767135777');

165686

CALL select_info('uid','165686');

889153
889153
CALL select_info('code','291474');

CALL select_info('phone','18565867390');

13162851087
159007 031077


SELECT * FROM gzb_user_account WHERE uid = 165677





117215

SELECT * FROM gzb_user_redpacket_withdraw_task WHERE uid = 117215

DROP PROCEDURE IF EXISTS `user_order_info_selector`;
CREATE PROCEDURE user_order_info_selector(IN in_uid VARCHAR(20))  


show tables 

SELECT * FROM information_schema.`tables` WHERE TABLE_SCHEMA = 'gzb_ol_0824'

SELECT DISTINCT TABLE_TYPE FROM information_schema.`TABLES`

CREATE VIEW `a_view` as 
SELECT * FROM gzb_user_account limit 5

SELECT * FROM a_view

DROP VIEW `a_view`













DROP PROCEDURE IF EXISTS `INITBNSTRENDS`;

CREATE DEFINER = `huisou`@`%` PROCEDURE `INITBNSTRENDS`()
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


CREATE TABLE `gzb_user_account` (
  `uid` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `username` varchar(30) DEFAULT NULL COMMENT '用户名',
  `account_status` tinyint(2) unsigned DEFAULT '0' COMMENT '账户状态 0 启用 ，1 停用 ， 2 删除',
  `card` varchar(30) DEFAULT NULL COMMENT '手机卡类型',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `phone` (`phone`)
) ENGINE=InnoDB AUTO_INCREMENT=165710 DEFAULT CHARSET=utf8 COMMENT='用户账户表';



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



DROP PROCEDURE IF EXISTS `__init_tmp`;
CREATE PROCEDURE `__init_tmp` ()
BEGIN
	DECLARE _max int ;
	DECLARE _i int ;
	SET _i = 0;
	SET _max = 30;
	DROP TABLE IF EXISTS `__tmp`;
  CREATE TABLE `__tmp`( 
		id int(11) unsigned NOT NULL AUTO_INCREMENT ,
		_key_1 VARCHAR(100) NULL ,
		_key_2 VARCHAR(100) NULL ,
		_key_3 VARCHAR(100) NULL ,
		_key_4 VARCHAR(100) NULL ,
		_key_5 VARCHAR(100) NULL ,
		_key_6 VARCHAR(100) NULL ,
		PRIMARY KEY(id)
	);
	_cur_loop:LOOP
		IF _i = _max THEN
			LEAVE _cur_loop;
		ELSE
			SET _i = _i + 1; 
			INSERT INTO __tmp (`id`)VALUES(_i);
		END IF;
	END LOOP;
END

CALL __init_tmp();
SELECT * FROM __tmp;


DROP PROCEDURE IF EXISTS `proc`;
CREATE PROCEDURE `proc` (
	in _in_table_name VARCHAR (30),
	in _in_key_name VARCHAR (30)
)
BEGIN
	declare done int;  
	declare _RESULT VARCHAR(255);  
	declare tbname varchar(20);  
	declare _COLUMN_NAME VARCHAR(40);

	DECLARE _key VARCHAR(20) ; 
	DECLARE _i int ; 
	
	declare cur_test CURSOR for 
		SELECT COLUMN_NAME FROM information_schema.`COLUMNS` WHERE TABLE_SCHEMA = 'gzb_ol_0824' AND table_name = _in_table_name;
  declare continue handler FOR SQLSTATE '02000' SET done = 1;  

	set _i = 0 ;
	SET _RESULT = '';
	SET _key = _in_key_name;
	OPEN cur_test;
	_cur_loop:LOOP
		FETCH cur_test INTO _COLUMN_NAME;
		IF done = 1 THEN
			LEAVE _cur_loop;
		ELSE
			SET _i = _i + 1; 
			##SET _RESULT = CONCAT(_RESULT,',',_COLUMN_NAME);
			SET @sql = CONCAT('UPDATE __tmp SET ',_key,'="',_COLUMN_NAME,'" WHERE id = ',_i);
			##INSERT INTO `__tmp` (_key)VALUES(_COLUMN_NAME);
			prepare stmt from @sql; 
			EXECUTE stmt;     
			deallocate prepare stmt;   
		END IF;
	END LOOP;
	close cur_test;  
	SET _RESULT = SUBSTR(_RESULT,2);
	##select _RESULT;  
  SELECT _i;
	SELECT * FROM `__tmp`;
	DROP TABLE IF EXISTS `__tmp`;
END;
CALL proc('gzb_user_account','_key_3');







SELECT * FROM __tmp

SELECT CONCAT('name=',name) AS name FROM gzb_user_info WHERE uid = 18 limit 1



SELECT LOCATE('aasd','asdfghjjkl') INTO __tmp;

SELECT POSITION('d' in 'asdfghjjkl');
uid,nickname,name,sex,age,qq,wechat,email,emergency_contact,company,join_at,identity,created_at,updated_at,process_at,audit_status,credit_status,fraudmetrix_status,credit_status_info,credit_status_info
DROP PROCEDURE IF EXISTS `test`;
CREATE PROCEDURE 	`test`()  
BEGIN   
	DECLARE forward_seconds text;  

  SELECT * INTO forward_seconds FROM information_schema.`COLUMNS` WHERE TABLE_SCHEMA = 'gzb_ol_0824' AND table_name = 'gzb_user_info';


END;

CALL test();


DROP PROCEDURE IF EXISTS `set_table_date`;
CREATE PROCEDURE 	`set_table_date`(
	IN in_table_name VARCHAR(30),
	IN in_col_name VARCHAR(30),
	IN in_select_col VARCHAR(30),
  IN in_select_val VARCHAR(30),
	IN in_days int(11)
)  
BEGIN   
	DECLARE forward_seconds INT(11);  
	DECLARE exec_sql VARCHAR(500);   
	DECLARE has_updated_at VARCHAR(50);
	DECLARE updated_at_val VARCHAR(50);
	SET forward_seconds = 86400 * in_days;
	SELECT COLUMN_NAME INTO has_updated_at FROM INFORMATION_SCHEMA.COLUMNS  WHERE   table_schema = DATABASE() AND TABLE_NAME = in_table_name AND COLUMN_NAME = 'updated_at';
	IF has_updated_at = 'updated_at' AND in_col_name != 'updated_at' THEN
		SET @update_sql =  CONCAT('UPDATE ',in_table_name,' SET updated_at = updated_at,',in_col_name,' = FROM_UNIXTIME(UNIX_TIMESTAMP(',in_col_name,') + ',forward_seconds,')',' WHERE ',in_select_col,' = "',in_select_val,'"');
	ELSE 
		SET @update_sql =  CONCAT('UPDATE ',in_table_name,' SET ',in_col_name,' = FROM_UNIXTIME(UNIX_TIMESTAMP(',in_col_name,') + ',forward_seconds,')',' WHERE ',in_select_col,' = "',in_select_val,'"');
	END IF;
	prepare stmt from @update_sql; 
	EXECUTE stmt;     
	deallocate prepare stmt;   
END;




--User order info selector
--Return : 1.a order each line; 2.each consists of {order info ,repayment }

--User redpacket info selector
--Return : 1.withdraw info 2.redpacket list,statistics 

DROP PROCEDURE IF EXISTS `user_info_selector`;
CREATE PROCEDURE user_info_selector(IN in_uid VARCHAR(20))  
BEGIN   
	DECLARE uuid INT(11);   
  DECLARE in_phone VARCHAR(20);  
	DECLARE umy_code VARCHAR(20);   
	DECLARE is_cheat VARCHAR(20);
	DECLARE is_black VARCHAR(20);
  DECLARE is_from_black VARCHAR(20);
	DECLARE is_from_cheat VARCHAR(20);
	DECLARE ufrom_code VARCHAR(20);
	DECLARE ufrom_uid INT(11);
	DECLARE ufrom_phone VARCHAR(20);
  DECLARE uuser_type VARCHAR(20);
  DECLARE upromotion_type VARCHAR(20);
	SELECT uid ,phone INTO uuid,in_phone FROM gzb_user_account WHERE uid = in_uid LIMIT 1;
	IF uuid is not NULL THEN
		#user cheat
		SELECT phone INTO is_cheat FROM gzb_user_cheat WHERE phone = in_phone;
		IF is_cheat IS NOT NULL THEN
			SET is_cheat = 'YES';
		ELSE
			SET is_cheat = 'NO';
		END IF;
	  SELECT user_type INTO uuser_type FROM gzb_user_redpacket_account WHERE uid = uuid;
		IF uuser_type = 1 THEN
			SET uuser_type = 'blue';
		ELSE
			SET uuser_type = 'normal';
		END IF;

		SELECT my_code,from_code,from_uid,type INTO umy_code,ufrom_code,ufrom_uid,upromotion_type  FROM gzb_user_redpacket_code WHERE uid = uuid;
		SELECT credit_status INTO is_black FROM gzb_user_info WHERE uid = uuid;
		#from user cheat
		IF ufrom_code IS NOT NULL THEN
			SELECT phone INTO ufrom_phone FROM gzb_user_account WHERE  uid = ufrom_uid;
			SELECT phone INTO is_from_cheat FROM gzb_user_cheat WHERE phone = ufrom_phone;
			SELECT credit_status INTO is_from_black FROM gzb_user_info WHERE uid = ufrom_uid;
			IF is_from_cheat IS NOT NULL THEN
				SET is_from_cheat = 'YES';
			ELSE
				SET is_from_cheat = 'NO';
			END IF;
		
			IF is_from_black = 3 THEN
				SET is_from_black = 'YES';
			ELSE
				SET is_from_black = 'NO';
			END IF;
		END IF;
		IF is_black = 3 THEN
			SET is_black = 'YES';
		ELSE
			SET is_black = 'NO';
		END IF;

		SELECT 	in_phone  ,
						uuid      ,
						uuser_type,
						upromotion_type,
						umy_code,
						is_cheat,
						is_black   ,
						ufrom_phone,
						ufrom_uid,
						ufrom_code,
						is_from_cheat,
						is_from_black;
	ELSE
		SELECT 'UID Not Found';
	END IF;
END;



DROP PROCEDURE IF EXISTS `set_table_date`;
CREATE PROCEDURE 	`set_table_date`(
	IN in_table_name VARCHAR(30),
	IN in_col_name VARCHAR(30),
	IN in_select_col VARCHAR(30),
  IN in_select_val VARCHAR(30),
	IN in_days int(11)
)  
BEGIN   
	DECLARE forward_seconds INT(11);  
	DECLARE exec_sql VARCHAR(500);   
	DECLARE has_updated_at VARCHAR(50);
	DECLARE updated_at_val VARCHAR(50);
	SET forward_seconds = 86400 * in_days;
	SELECT COLUMN_NAME INTO has_updated_at FROM INFORMATION_SCHEMA.COLUMNS  WHERE   table_schema = DATABASE() AND TABLE_NAME = in_table_name AND COLUMN_NAME = 'updated_at';
	IF has_updated_at = 'updated_at' AND in_col_name != 'updated_at' THEN
		SET @update_sql =  CONCAT('UPDATE ',in_table_name,' SET updated_at = updated_at,',in_col_name,' = FROM_UNIXTIME(UNIX_TIMESTAMP(',in_col_name,') + ',forward_seconds,')',' WHERE ',in_select_col,' = "',in_select_val,'"');
	ELSE 
		SET @update_sql =  CONCAT('UPDATE ',in_table_name,' SET ',in_col_name,' = FROM_UNIXTIME(UNIX_TIMESTAMP(',in_col_name,') + ',forward_seconds,')',' WHERE ',in_select_col,' = "',in_select_val,'"');
	END IF;
	prepare stmt from @update_sql; 
	EXECUTE stmt;     
	deallocate prepare stmt;   
END; 

DROP PROCEDURE IF EXISTS `reset_order_date`;
CREATE PROCEDURE 	reset_order_date(IN in_orderid VARCHAR(30),IN in_days INT(11))  
BEGIN  
	START TRANSACTION ;
	CALL set_table_date('gzb_order_main','pay_at','orderid',in_orderid,in_days);
	CALL set_table_date('gzb_order_main','created_at','orderid',in_orderid,in_days);
	CALL set_table_date('gzb_order_main','updated_at','orderid',in_orderid,in_days);

	CALL set_table_date('gzb_order_pay','created_at','orderid',in_orderid,in_days);
	CALL set_table_date('gzb_order_pay','pay_at','orderid',in_orderid,in_days);
	CALL set_table_date('gzb_order_pay','payment_at','orderid',in_orderid,in_days);
	CALL set_table_date('gzb_order_pay','updated_at','orderid',in_orderid,in_days);

	CALL set_table_date('gzb_order_periods','created_at','orderid',in_orderid,in_days);
	CALL set_table_date('gzb_order_periods','updated_at','orderid',in_orderid,in_days);

	CALL set_table_date('gzb_order_pay_task','created_at','orderid',in_orderid,in_days);
	CALL set_table_date('gzb_order_pay_task','updated_at','orderid',in_orderid,in_days);
	CALL set_table_date('gzb_order_task','created_at','orderid',in_orderid,in_days);
	CALL set_table_date('gzb_order_task','updated_at','orderid',in_orderid,in_days);
	UPDATE gzb_order_pay SET dalay_price = 0 WHERE orderid = in_orderid AND pay_at >= now();
	COMMIT;
END; 




DROP PROCEDURE IF EXISTS `info_selector`;
CREATE PROCEDURE info_selector(IN in_uid VARCHAR(20))  
BEGIN   
	DECLARE uuid INT(11);   
  DECLARE in_phone VARCHAR(20);  
	DECLARE umy_code VARCHAR(20);   
	DECLARE is_cheat VARCHAR(20);
	DECLARE is_black VARCHAR(20);
  DECLARE is_from_black VARCHAR(20);
	DECLARE is_from_cheat VARCHAR(20);
	DECLARE ufrom_code VARCHAR(20);
	DECLARE ufrom_uid INT(11);
	DECLARE ufrom_phone VARCHAR(20);
  DECLARE uuser_type VARCHAR(20);
  DECLARE upromotion_type VARCHAR(20);
	SELECT uid ,phone INTO uuid,in_phone FROM gzb_user_account WHERE uid = in_uid LIMIT 1;
	IF uuid is not NULL THEN
		#user cheat
		SELECT phone INTO is_cheat FROM gzb_user_cheat WHERE phone = in_phone;
		IF is_cheat IS NOT NULL THEN
			SET is_cheat = 'YES';
		ELSE
			SET is_cheat = 'NO';
		END IF;
	  SELECT user_type INTO uuser_type FROM gzb_user_redpacket_account WHERE uid = uuid;
		IF uuser_type = 1 THEN
			SET uuser_type = 'blue';
		ELSE
			SET uuser_type = 'normal';
		END IF;

		SELECT my_code,from_code,from_uid,type INTO umy_code,ufrom_code,ufrom_uid,upromotion_type  FROM gzb_user_redpacket_code WHERE uid = uuid;
		SELECT credit_status INTO is_black FROM gzb_user_info WHERE uid = uuid;
		#from user cheat
		IF ufrom_code IS NOT NULL THEN
			SELECT phone INTO ufrom_phone FROM gzb_user_account WHERE  uid = ufrom_uid;
			SELECT phone INTO is_from_cheat FROM gzb_user_cheat WHERE phone = ufrom_phone;
			SELECT credit_status INTO is_from_black FROM gzb_user_info WHERE uid = ufrom_uid;
			IF is_from_cheat IS NOT NULL THEN
				SET is_from_cheat = 'YES';
			ELSE
				SET is_from_cheat = 'NO';
			END IF;
		
			IF is_from_black = 3 THEN
				SET is_from_black = 'YES';
			ELSE
				SET is_from_black = 'NO';
			END IF;
		END IF;
		IF is_black = 3 THEN
			SET is_black = 'YES';
		ELSE
			SET is_black = 'NO';
		END IF;

		SELECT 	in_phone  ,
						uuid      ,
						uuser_type,
						upromotion_type,
						umy_code,
						is_cheat,
						is_black   ,
						ufrom_phone,
						ufrom_uid,
						ufrom_code,
						is_from_cheat,
						is_from_black;
	ELSE
		SELECT 'UID Not Found';
	END IF;
END;


DROP PROCEDURE IF EXISTS `select_info`;
CREATE PROCEDURE select_info(
	IN in_data_type VARCHAR(20),
	IN in_data VARCHAR(20)
)  
BEGIN   
	DECLARE uuid INT(11);   
	IF in_data_type = 'uid' THEN 
			set uuid = in_data;
	ELSEIF in_data_type = 'phone' THEN 
			SELECT uid INTO uuid FROM gzb_user_account WHERE phone = in_data LIMIT 1;
	ELSEIF in_data_type = 'code' THEN 
			SELECT uid INTO uuid FROM gzb_user_redpacket_code WHERE my_code = in_data LIMIT 1;
	ELSE
			SELECT 'in_data_type Not Validate';
  END IF;
	
	IF uuid is not NULL THEN
		CALL info_selector(uuid);
	ELSE
		SELECT 'UID Not Found';
	END IF;
END;



DROP PROCEDURE IF EXISTS `delete_account`;
CREATE PROCEDURE delete_account(IN in_phone VARCHAR(20))  
BEGIN   
DECLARE uuid INT(11);   
SELECT uid INTO uuid FROM gzb_user_account WHERE phone = in_phone LIMIT 1;
IF uuid is not NULL THEN
	DELETE FROM gzb_user_account WHERE uid = uuid;
	DELETE FROM gzb_user_redpacket_account WHERE uid = uuid;
	DELETE FROM gzb_user_redpacket WHERE uid = uuid;
	UPDATE gzb_user_redpacket_code SET uid = null,from_code=NULL,from_uid = null WHERE uid = uuid;
ELSE
	SELECT 'UID Not Found';
END IF;
END;
CALL delete_account('13567193215');


call delete_account('18767139999');




insert into `gzb_topic_config` (`key`, `value`, `status`, `info`) values ('COUPON_STATUS', '0', '0', '注册送还款券是否开启');
insert into `gzb_topic_config` (`key`, `value`, `status`, `info`) values ('COUPON_EXPIRE_TIME', '7776000', '0', '还款券有效期（秒）');







insert into `gzb_topic_config` (`key`, `value`, `status`, `info`) values ('REDPACKET_TYPE_6_AMOUNT', '0', '0', '红包6(填写兑换码获得)的金额');
insert into `gzb_topic_config` (`key`, `value`, `status`, `info`) values ('REDPACKET_TYPE_7_AMOUNT', '0', '0', '红包7(推广获得)的金额');
insert into `gzb_topic_config` (`key`, `value`, `status`, `info`) values ('REDPACKET_V1_3_1_END_DATE', '0', '0', '1.3.1蓝色天使关系截止时间');
insert into `gzb_topic_config` (`key`, `value`, `status`, `info`) values ('REDPACKET_BLUE_STATUS', '0', '0', '蓝色天使红包开启状态');
insert into `gzb_topic_config` (`key`, `value`, `status`, `info`) values ('REDPACKET_SHOW_CODE', '0', '0', '是否显示兑换码输入框');
insert into `gzb_topic_config` (`key`, `value`, `status`, `info`) values ('REDPACKET_TOPPIC_REGISTE', '0', '0', '注册页面头图');
insert into `gzb_topic_config` (`key`, `value`, `status`, `info`) values ('REDPACKET_TOPPIC_LOGIN', '0', '0', '登录页面头图');
insert into `gzb_topic_config` (`key`, `value`, `status`, `info`) values ('REDPACKET_END_TEXT', '0', '0', '红包结束语');
insert into `gzb_topic_config` (`key`, `value`, `status`, `info`) values ('REDPACKET_SHOW_END_TEXT', '0', '0', '是否显示红包结束语');
insert into `gzb_topic_config` (`key`, `value`, `status`, `info`) values ('REDPACKET_BLUE_EXPIRE', '0', '0', '蓝色天使红包超时时间');


CREATE database gzb_ol_0824

SELECT a.uid,a.phone,i.`name`,i.identity
FROM gzb_user_account a
JOIN gzb_user_info i ON a.uid = i.uid
WHERE phone = '18800658696';
#查询结果： 34958	18800658696	刘娜娜	32132219900126742X
SELECT uid,phone,name,identity,card FROM gzb_user_redpacket_account WHERE identity = '32132219900126742X';
#查询结果： 141417	13861820349	刘娜娜	32132219900126742X	6228480435294668171
UPDATE gzb_user_redpacket_account
	SET 
		identity= null,
		card = NULL
WHERE uid = 141417 AND identity = '32132219900126742X';

SELECT a.uid,a.phone,i.`name`,i.identity
FROM gzb_user_account a
JOIN gzb_user_info i ON a.uid = i.uid
WHERE phone = '18012657202';
#查询结果： 115554	18012657202	张天友	
SELECT uid,phone,name,identity,card FROM gzb_user_redpacket_account WHERE identity = '372924198606100316';
#查询结果： 151686	18888396991	张天友	372924198606100316	6222021001126615985
UPDATE gzb_user_redpacket_account
	SET 
		identity= null,
		card = NULL
WHERE uid = 151686 AND identity = '372924198606100316';


SELECT a.uid,a.phone,i.`name`,i.identity
FROM gzb_user_account a
JOIN gzb_user_info i ON a.uid = i.uid
WHERE phone = '15950413232';
#查询结果： 118338	15950413232	魏艳艳	320321198810291225
SELECT uid,phone,name,identity,card FROM gzb_user_redpacket_account WHERE identity = '320321198810291225';
#查询结果： 150790	13775901623	魏艳艳	320321198810291225	6217001240008699824
UPDATE gzb_user_redpacket_account
	SET 
		identity= null,
		card = NULL
WHERE uid = 150790 AND identity = '320321198810291225';





SELECT * FROM gzb_user_redpacket_account WHERE identity = '372924198606100316';

SELECT * FROM gzb_user_redpacket_account WHERE identity = '32132219900126742X';

SELECT * FROM gzb_user_redpacket_account WHERE identity = '320321198810291225';







CALL select_info('17084991651');



select `gzb_order_main`.`orderid`, `gzb_order_main`.`pay_at` as `playingTime`, 
`gzb_order_periods`.`price`, `gzb_order_periods`.`periods`, `gzb_order_periods`.`periods_price`, 
`gzb_order_pay`.`pay_at` as `shouldPayDay`, `gzb_order_periods`.`tot_price`, 
`gzb_order_pay`.`id` as `orderPayId`, `gzb_order_pay`.`status`, `gzb_order_pay`.`pay_price`,
 `gzb_order_pay`.`early_price`, `gzb_order_pay`.`dalay_price`, `gzb_order_pay`.`payment_at` as `realPayDay`,
 `gzb_order_pay_task`.`pay_type` as `payType`, `gzb_order_pay_task`.`id` as `numericalorder`, 
`gzb_order_pay_task`.`pay_price` as `realMoney`,
 `gzb_order_pay_task`.`difference` as `payDifference`, 
`gzb_order_pay_task`.`pay_type` as `payType` 
from `gzb_order_pay` 
inner join `gzb_user_account` on `gzb_user_account`.`uid` = `gzb_order_pay`.`uid`
inner join `gzb_order_main` on `gzb_order_main`.`orderid` = `gzb_order_pay`.`orderid` 
inner join `gzb_order_periods` on `gzb_order_periods`.`orderid` = `gzb_order_main`.`orderid` 
left join `gzb_order_pay_task` on `gzb_order_pay_task`.`id` = `gzb_order_pay`.`pay_task_id` 
where `gzb_order_pay`.`status` > 0 and `gzb_order_pay_task`.`status` = 1 
and `gzb_order_pay`.`payment_at` >= '2015-08-01 00:00:00' and `gzb_user_account`.`uid` not in (1, 12, 28, 34, 85, 106, 170, 5468, 5478, 5795, 8482, 8525, 103887, 117273, 118124, 27653, 1133, 13)




393
18659386911

SELECT * FROM gzb_user_account WHERE phone="18659386911"

SELECT * FROM gzb_user_account WHERE uid  = 393

SELECT * FROM gzb_user_info WHERE uid  = 393



SELECT 
p.uid,p.orderid,p.mon_price,p.pay_price,p.early_price,p.dalay_price,p.status,p.payment_at,
t.type,t.pay_type,t.id,t.pay_price,t.difference,t.created_at
FROM gzb_order_pay p
JOIN gzb_order_pay_task t on p.pay_task_id = t.id 
WHERE p.`status` >0 AND t.`status` = 1 AND p.orderid = t.orderid










SELECT a.uid,a.phone,b.identity,b.name
FROM gzb_user_account a
JOIN gzb_user_info b on a.uid = b.uid
WHERE a.phone = '18262419098';
##查询结果：105063	18262419098	410724199104142019	郭涛
SELECT uid,name,identity,card FROM gzb_user_redpacket_account WHERE identity = '410724199104142019'  ;
##查询结果：148583	郭涛	410724199104142019	6217850800012578066
UPDATE gzb_user_redpacket_account 
SET 
	identity = NULL,
	card = NULL
WHERE uid = 148583;

SELECT * FROM gzb_user_redpacket_withdraw_task WHERE uid = 148583

SELECT SUM(amount) FROM gzb_user_redpacket WHERE uid = 105063




SELECT * FROM gzb_user_account WHERE phone = '18939426567'

3235

SELECT * FROM gzb_user_data WHERE uid = 3235

CREATE DATABASE gzb_ol_0817


SELECT * FROM gzb_area WHERE name like "%阿拉善盟%"


DROP PROCEDURE IF EXISTS `select_info`;
CREATE PROCEDURE select_info(IN in_phone VARCHAR(20))  
BEGIN   
	DECLARE uuid INT(11);   
	DECLARE umy_code VARCHAR(20);   
	DECLARE is_cheat VARCHAR(20);
	DECLARE is_black VARCHAR(20);
  DECLARE is_from_black VARCHAR(20);
	DECLARE is_from_cheat VARCHAR(20);
	DECLARE ufrom_code VARCHAR(20);
	DECLARE ufrom_uid INT(11);
	DECLARE ufrom_phone VARCHAR(20);
	SELECT uid INTO uuid FROM gzb_user_account WHERE phone = in_phone LIMIT 1;
	IF uuid is not NULL THEN
		#user cheat
		SELECT phone INTO is_cheat FROM gzb_user_cheat WHERE phone = in_phone;
		IF is_cheat IS NOT NULL THEN
			SET is_cheat = 'YES';
		ELSE
			SET is_cheat = 'NO';
		END IF;
		SELECT my_code,from_code,from_uid INTO umy_code,ufrom_code,ufrom_uid  FROM gzb_user_redpacket_code WHERE uid = uuid;
		SELECT credit_status INTO is_black FROM gzb_user_info WHERE uid = uuid;
		#from user cheat
		IF ufrom_code IS NOT NULL THEN
			SELECT phone INTO ufrom_phone FROM gzb_user_account WHERE  uid = ufrom_uid;
			SELECT phone INTO is_from_cheat FROM gzb_user_cheat WHERE phone = ufrom_phone;
			SELECT credit_status INTO is_from_black FROM gzb_user_info WHERE uid = ufrom_uid;
			IF is_from_cheat IS NOT NULL THEN
				SET is_from_cheat = 'YES';
			ELSE
				SET is_from_cheat = 'NO';
			END IF;
		
			IF is_from_black = 3 THEN
				SET is_from_black = 'YES';
			ELSE
				SET is_from_black = 'NO';
			END IF;
		END IF;
		IF is_black = 3 THEN
			SET is_black = 'YES';
		ELSE
			SET is_black = 'NO';
		END IF;

		SELECT 	in_phone  ,
						uuid      ,
						umy_code,
						is_cheat,
						is_black   ,
						ufrom_phone,
						ufrom_uid,
						ufrom_code,
						is_from_cheat,
						is_from_black;
	ELSE
		SELECT 'UID Not Found';
	END IF;
END;

DROP PROCEDURE IF EXISTS `select_info`;
CREATE PROCEDURE select_info(IN in_phone VARCHAR(20))  
BEGIN   
	DECLARE uuid INT(11);   
	DECLARE umy_code VARCHAR(20);   
	DECLARE is_cheat VARCHAR(20);
	
	ELSE
		SELECT 'UID Not Found';
	END IF;
END;

SHOW TABLES

DROP PROCEDURE IF EXISTS `proc`;
CREATE PROCEDURE `proc` ()
BEGIN
	declare done int;  
	declare result varchar(200);  
	declare tbname varchar(20);  
	declare cur_test CURSOR for 
		SELECT uid FROM gzb_user_account  LIMIT 5; 
  declare continue handler FOR SQLSTATE '02000' SET done = 1;  
	DROP TABLE IF EXISTS `tmp_tb`;
	CREATE TABLE `tmp_tb`(
		
	)
	SET result = '';
	open cur_test;  
		repeat  
				fetch cur_test into tbname;  
				SET result = CONCAT(result,',',tbname);
		until done = 1 end repeat;  
	close cur_test;  
	SET result = SUBSTR(result,2);
	select result;  
END;
CALL proc();


 begin  
 ->     declare name varchar(20);  
 ->     declare pass varchar(20);  
 ->     declare done int;  
 ->     declare cur_test CURSOR for select user_name,user_pass from test.users;  
 ->     declare continue handler FOR SQLSTATE '02000' SET done = 1;  
 ->     if param then  
 ->         select concat_ws(',',user_name,user_pass) into result from test.users where id=param;  
 ->     else  
 ->         open cur_test;  
 ->         repeat  
 ->             fetch cur_test into name, pass;  
 ->             select concat_ws(',',result,name,pass) into result;  
 ->         until done end repeat;  
 ->         close cur_test;  
 ->     end if;  
 -> end;| 




DROP PROCEDURE IF EXISTS `select_info`;
CREATE PROCEDURE select_info(IN in_phone VARCHAR(20))  
BEGIN   
	DECLARE uuid INT(11);   
	DECLARE umy_code VARCHAR(20);   
	DECLARE is_cheat VARCHAR(20);
	DECLARE is_black VARCHAR(20);
  DECLARE is_from_black VARCHAR(20);
	DECLARE is_from_cheat VARCHAR(20);
	DECLARE ufrom_code VARCHAR(20);
	DECLARE ufrom_uid INT(11);
	DECLARE ufrom_phone VARCHAR(20);
	SELECT uid INTO uuid FROM gzb_user_account WHERE phone = in_phone LIMIT 1;
	IF uuid is not NULL THEN
		#user cheat
		SELECT phone INTO is_cheat FROM gzb_user_cheat WHERE phone = in_phone;
		IF is_cheat IS NOT NULL THEN
			SET is_cheat = 'YES';
		ELSE
			SET is_cheat = 'NO';
		END IF;
		SELECT my_code,from_code,from_uid INTO umy_code,ufrom_code,ufrom_uid  FROM gzb_user_redpacket_code WHERE uid = uuid;
		SELECT credit_status INTO is_black FROM gzb_user_info WHERE uid = uuid;
		#from user cheat
		IF ufrom_code IS NOT NULL THEN
			SELECT phone INTO ufrom_phone FROM gzb_user_account WHERE  uid = ufrom_uid;
			SELECT phone INTO is_from_cheat FROM gzb_user_cheat WHERE phone = ufrom_phone;
			SELECT credit_status INTO is_from_black FROM gzb_user_info WHERE uid = ufrom_uid;
			IF is_from_cheat IS NOT NULL THEN
				SET is_from_cheat = 'YES';
			ELSE
				SET is_from_cheat = 'NO';
			END IF;
		
			IF is_from_black = 3 THEN
				SET is_from_cheat = 'YES';
			ELSE
				SET is_from_cheat = 'NO';
			END IF;
		END IF;
		IF is_black = 3 THEN
			SET is_black = 'YES';
		ELSE
			SET is_black = 'NO';
		END IF;

		SELECT 	in_phone  ,
						uuid      ,
						umy_code,
						is_cheat,
						is_black   ,
						ufrom_phone,
						ufrom_uid,
						ufrom_code,
						is_from_cheat,
						is_from_black;
	ELSE
		SELECT 'UID Not Found';
	END IF;
END;




DROP PROCEDURE IF EXISTS `delete_account`;
CREATE PROCEDURE delete_account(IN in_phone VARCHAR(20))  
BEGIN   
DECLARE uuid INT(11);   
SELECT uid INTO uuid FROM gzb_user_account WHERE phone = in_phone LIMIT 1;
IF uuid is not NULL THEN
	DELETE FROM gzb_user_account WHERE uid = uuid;
	DELETE FROM gzb_user_redpacket_account WHERE uid = uuid;
	DELETE FROM gzb_user_redpacket WHERE uid = uuid;
	UPDATE gzb_user_redpacket_code SET uid = null,from_code=NULL,from_uid = null WHERE uid = uuid;
ELSE
	SELECT 'UID Not Found';
END IF;
END;
CALL delete_account('13567193215');

CALL delete_account('18767139999');

SHOW VARIABLES like 'SQL_NO_CACHE'

show variables like '%query_cache%'





