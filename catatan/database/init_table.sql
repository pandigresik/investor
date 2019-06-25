CREATE TABLE users (
  id int NOT NULL auto_increment,
  username varchar(12) NOT NULL,
  email varchar(255) NOT NULL,
  password varchar(60) NOT NULL,
  password_salt varchar(255) DEFAULT NULL,
  status varchar(1) default '1',
  created_at datetime NULL ,
  updated_at datetime NULL ,
  PRIMARY KEY (id)
) 
/** username admin, password = 'tes123' */
INSERT INTO users (username,email,password,password_salt)
VALUES ('admin','admin@wjc.co.id','ef7a8fce6f9406466457a18d625b556a','LrRwGemCHxwTOhjTEFKn7OUbL+EU4fzjsdE85zvv93ZfNf69vz7t54hSWm6Ecyi4Fygx+3LxXYDr0NHnNBxU4Q==')

ALTER TABLE users ADD CONSTRAINT uq_users UNIQUE(username)
create table roles(
	id int not null auto_increment,
	role_name varchar(50) not null,
	status varchar(1) default '1',
	created_at datetime NULL,
	updated_at datetime NULL,
	PRIMARY KEY (id)
);
insert into roles(role_name) values('administrator'),('staff');


create table user_role(
	users_id int not null,
	roles_id int not null
);
ALTER TABLE user_role ADD CONSTRAINT uq_user_role UNIQUE (users_id,roles_id);
alter table user_role add CONSTRAINT fk_user_role1 FOREIGN KEY  (users_id) references users(id);
alter table user_role add constraint fk_user_role2 FOREIGN KEY (roles_id) references roles(id);

insert into user_role values (1,1);

create table menus(
	id int not null auto_increment,
	name varchar(30) not null,
	route varchar(60) null,
	icon varchar(30) null,
	status varchar(1) not null default '1',
	parent_id int null default '0',
	descriptions VARCHAR(255),
	primary key(id)
); 

create table permissions(
	id int not null auto_increment,
	name varchar(30) not null,
	route varchar(60) not null,
	menus_id int not null,
	primary key(id)
);
ALTER TABLE permissions ADD CONSTRAINT uq_menu_details UNIQUE (route,menus_id);
alter table permissions add CONSTRAINT fk_menu_details FOREIGN KEY (menus_id) references menus(id);
create table role_menu(
	roles_id int not null,
	menus_id int not null,
	status varchar(1) default '1'
);

ALTER TABLE role_menu ADD CONSTRAINT uq_role_men UNIQUE (roles_id,menus_id);
alter table role_menu add CONSTRAINT fk_role_menu_details FOREIGN KEY (menus_id) references menus(id);
alter table role_menu add CONSTRAINT fk_role_menu_details2 FOREIGN KEY (roles_id) references roles(id);
create table role_permissions(
	roles_id int not null,
	permissions_id int not null,
	status varchar(1) default '1'
);

ALTER TABLE role_permissions ADD CONSTRAINT uq_role_permissions UNIQUE (roles_id,permissions_id);
alter table role_permissions add CONSTRAINT fk_role_permissions FOREIGN KEY (permissions_id) references permissions(id);
alter table role_permissions add CONSTRAINT fk_role_permissions2 FOREIGN KEY (roles_id) references roles(id);


INSERT INTO menus (name, route, icon, status, parent_id, descriptions)
VALUES ('Master', '', 'fa fa-users', '1', 0, NULL);


INSERT INTO menus (name, route, icon, status, parent_id, descriptions)
VALUES ('Transaksi', '', 'fa fa-users', '1', 0, NULL);

INSERT INTO menus (name, route, icon, status, parent_id, descriptions)
VALUES ('Master Menu', 'master/menu', 'fa fa-users', '1', 1, NULL);


INSERT INTO menus (name, route, icon, status, parent_id, descriptions)
VALUES ('Master Role', 'master/role', 'fa fa-bar-chart-o', '1', 1, 'Administrasi role');

INSERT INTO menus (name, route, icon, status, parent_id, descriptions)
VALUES ('Master User', 'master/user', 'fa fa-users', '1', 1, NULL);

INSERT INTO role_menu (roles_id, menus_id, status)
VALUES (1, 1, '1');

INSERT INTO role_menu (roles_id, menus_id, status)
VALUES (1, 2, '1');

INSERT INTO role_menu (roles_id, menus_id, status)
VALUES (1, 3, '1');

INSERT INTO role_menu (roles_id, menus_id, status)
VALUES (1, 4, '1');

INSERT INTO role_menu (roles_id, menus_id, status)
VALUES (1, 5, '1');

CREATE TABLE role_teritori(
  roles_id INT,
  wlkode VARCHAR(4)
)
ALTER TABLE role_teritori ADD CONSTRAINT fk_role_teritori FOREIGN KEY (roles_id) REFERENCES roles(id)
ALTER TABLE role_teritori ADD CONSTRAINT uq_role_teritori UNIQUE KEY (roles_id,wlkode)

create table kirim_faktur(
	ID int auto_increment primary key,
	TGL_RESI date not null,
	NO_RESI varchar(50) not null,
	KETERANGAN varchar(100),
	CREATED_AT datetime not null,
	CREATED_BY int
)engine=innodb DEFAULT CHARSET=utf8

CREATE TABLE kirim_faktur_detail(
	KIRIM_FAKTUR_ID INT NOT NULL,
	NOFAKTUR VARCHAR(10) NOT NULL,
	FOREIGN KEY FK_KIRIM_FAKTUR_DTL1 (KIRIM_FAKTUR_ID) REFERENCES kirim_faktur(ID)
)engine=innodb DEFAULT CHARSET=utf8


CREATE TABLE dbo.m_delegasi
(
	id                INT IDENTITY NOT NULL,
	id_menu			  INT,	
	status            VARCHAR (1) DEFAULT ('A') NOT NULL,
	created_at        DATETIME DEFAULT (getdate()) NULL,
	updated_at        DATETIME DEFAULT (getdate()) NULL,
	PRIMARY KEY (id),
	CONSTRAINT fk_id_menu FOREIGN KEY (id_menu) REFERENCES dbo.menus (id)
)