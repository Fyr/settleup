CREATE TABLE `email_log` (
	id int(10) unsigned not null auto_increment,
	subject varchar(255) null,
	email varchar(255) not null DEFAULT '',
	created_at DATETIME DEFAULT now(),
	status TINYINT NULL,
	error varchar(255) null,
	constraint `PRIMARY`
	primary key (id)
);
