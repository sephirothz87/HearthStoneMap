DROP TABLE IF EXISTS `card_groups`;
--	卡组信息管理
CREATE TABLE `card_groups` (
	`id`				INTEGER NOT NULL AUTO_INCREMENT,
	`name`				VARCHAR(20) NOT NULL,
	`class`				INTEGER NOT NULL,
	-- 1 快攻 2 中速 3 OTK 0 黑科技
	`type`				INTEGER NOT NULL DEFAULT 0,
	`enable`			SMALLINT NOT NULL DEFAULT 1,
	-- ===== システムサポート用(共通) ====
	`created_at`		DATETIME,
	`created_by`		INTEGER DEFAULT 0,
	`updated_at`		DATETIME,
	`updated_by`		INTEGER,

	PRIMARY KEY(`id`),
	FOREIGN KEY(`class`) REFERENCES classes(`id`)
);