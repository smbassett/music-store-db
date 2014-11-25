CREATE DATABASE AMS; 
USE AMS; 
SELECT database(); 

CREATE TABLE Item(
upc int,
title varchar(50) not null,
item_type varchar(10), 
category varchar(20),
company varchar(30) not null, 
item_year int, 
price int not null, 
stock int not null,
PRIMARY KEY (upc));

CREATE TABLE Customer(
cid int, 
username varchar(20),
c_password varchar(20),
fullname varchar(30),
address varchar(50), 
phone varchar(12),
PRIMARY KEY(cid));

CREATE TABLE LeadSinger(
upc int,
singer_name varchar(20),
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
expiryDate int, 
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

CREATE Table `Return`(
retid int, 
return_date date, 
receiptID int,
PRIMARY KEY (retid),
FOREIGN KEY(receiptID) REFERENCES PurchaseItem(receiptID));

CREATE TABLE ReturnItem(
retid int, 
upc int, 
quantity int,
PRIMARY KEY (retid,upc),
FOREIGN KEY (retid) REFERENCES `Return`(retid),
FOREIGN KEY (upc) REFERENCES Item(upc));

CREATE TABLE ShoppingCart(
cid int, 
upc int, 
quantity int,
PRIMARY KEY(cid, upc),
FOREIGN KEY(cid) REFERENCES Customer(cid),
FOREIGN KEY(upc) REFERENCES Item(upc));