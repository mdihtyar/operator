-- 1. List of registered users (active, disabled)
CREATE TABLE IF NOT EXISTS `tbl_users` (
    id INT NOT NULL AUTO_INCREMENT,
    login VARCHAR(50) NOT NULL,
    pass varchar(50) NOT NULL,
    comment VARCHAR(100) NOT NULL DEFAULT "",
    disable_user TINYINT NOT NULL DEFAULT 0,
    admin_user TINYINT NOT NULL DEFAULT 0,

    PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS `tbl_sessions` (
    id VARCHAR(100) NOT NULL,
    id_owner INT NOT NULL,
    when_created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    user_agent VARCHAR(500) NOT NULL,
    remote_addr VARCHAR(15) NOT NULL,

    PRIMARY KEY(id)
);

CREATE TABLE IF NOT EXISTS `tbl_call_logs` (
    id INT NOT NULL AUTO_INCREMENT,
    remote_number VARCHAR(13) NOT NULL,
    when_created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    when_tried DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    when_processed DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    calling_count INT NOT NULL DEFAULT 0,
    tried_count INT NOT NULL DEFAULT 0,

    PRIMARY KEY(id)
);

CREATE TABLE IF NOT EXISTS `tbl_trunk_line` (
    id INT NOT NULL AUTO_INCREMENT,
    channel_description VARCHAR(100),
    current_state TINYINT NOT NULL DEFAULT 0,
    phone_number varchar(13),
    way tinyint not null default 0,

    PRIMARY KEY(id)
);

CREATE TABLE IF NOT EXISTS `tbl_phone_book` (
    id INT NOT NULL AUTO_INCREMENT,
    phone_number VARCHAR(13) NOT NULL,
    name VARCHAR(100) NOT NULL,
    importance tinyint not null default 0,

    PRIMARY KEY(id)
);
