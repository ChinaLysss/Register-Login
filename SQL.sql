CREATE TABLE `users` (
    `UID` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户UID',
    `Username` varchar(50) NOT NULL COMMENT '用户名',
    `Password` varchar(255) NOT NULL COMMENT '密码',
    `TOKEN` varchar(64) NOT NULL COMMENT 'token令牌',
    `creat_time` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '注册时间',
    PRIMARY KEY (`UID`),
    UNIQUE KEY `Username` (`Username`),
    UNIQUE KEY `TOKEN` (`TOKEN`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='UserData';