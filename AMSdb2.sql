CREATE DATABASE AMS; 
USE AMS; 
SELECT database(); 

CREATE TABLE Item(
upc int,
title varchar(30) not null,
item_type varchar(10), 
category varchar(10),
company varchar(30) not null, 
item_year char(4), 
price int not null, 
stock int not null,
PRIMARY KEY (upc));

CREATE TABLE LeadSinger(
upc int,
singer_name varchar(10),
PRIMARY KEY (upc, singer_name),
FOREIGN KEY (upc) REFERENCES Item(upc));

CREATE TABLE HasSong(
upc int,
title varchar(10),
PRIMARY KEY (upc, title),
FOREIGN KEY (upc) REFERENCES Item(upc));

CREATE TABLE `Order`(
receiptID int,
order_date date, 
cid int, 
cardNo int, 
expiryDate date, 
expectedDate date, 
deliveredDate date,
PRIMARY KEY(receiptID),
FOREIGN KEY(cid) REFERENCES Customer(cid));

CREATE TABLE PurchaseItem(
receiptID int, 
upc int, 
quantity int,
PRIMARY KEY(receiptID, upc),
FOREIGN KEY(upc) REFERENCES Item(upc));

CREATE TABLE Customer(
cid int, 
c_password varchar(8),
cname varchar(20),
address varchar(30), 
phone int,
PRIMARY KEY(cid));

CREATE Table `Return`(
retid int, 
return_date date, 
receiptID int,
PRIMARY KEY (retid));

CREATE TABLE ReturnItem(
retid int, 
upc int, 
quantity int,
PRIMARY KEY (retid,upc),
FOREIGN KEY (upc) REFERENCES Item(upc));