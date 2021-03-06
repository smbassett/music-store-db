USE AMS; 
SELECT database();

INSERT INTO Customer(cid, c_password, username, fullname, address, phone) 
VALUES 
(0, "password", "Crystal", "Crystal To", "8298 Yellow St", "778-319-9806"),
(1, "toocoolforschool", "Chris", "Chris Laporte", "6423 Crazy Ave", "778-382-4893"),
(2, "applez", "Vojin", "Vojin Vukman", "2987 Snowman Ave", "778-289-4892"),
(3, "bananaz", "Scott", "Scott Bassett", "348 Chalkboard St", "604-340-1023"),
(4, "cs304", "George", "George Harrison", "928 Olap St", "604-293-0940"),
(5, "montana", "Hannah", "Hannah Dakota", "8492 Rain Ave", "604-922-0392"),
(6, "never", "Jesus", "Jesus Jones", "192 Granville St", "604-983-0900"),
(7, "gonna", "Idina", "Idina Jones", "209 Burrard St", "778-392-1111"),
(8, "give", "Michael", "Michael Smith", "109 Water St", "778-238-4987"),
(9, "you", "Shelley", "Shelley Peaks", "1098 Main St", "604-204-9844"),
(10, "up", "Asher", "Asher Mash", "2298 W Georgia St", "778-994-4392"),
(11, "never", "Reese", "Reese Winter", "1872 Wallaby Way", "604-102-3049"),
(12, "gonna", "Jennifer", "Jennifer Jones", "123 Standard St", "604-958-2728"),
(13, "let", "Emily", "Emily Yoshida", "109 Student Union Blvd", "604-777-5849"),
(14, "you", "Sue", "Sue Smith", "1198 Molap St", "604-293-0940"),
(15, "down", "Rick", "Rick Richards", "2340 Illuminati Rd", "604-405-0940"),
(16, "hey", "Edgar", "Edgar Dafoe", "324 Yahoo St", "604-958-8876"),
(17, "328408", "R2D2", "R2D2 Unit", "12 Star Wars Rd", "604-443-3829"),
(18, "cyrus", "Miley", "Miley Smith", "238098 Twerk St", "604-832-4493"),
(19, "rocks", "Elvis", "Elvis Presley", "109 Disco Ave", "604-293-0940"),
(20, "rebootcafe", "Arthur", "Arthur Bear", "231 Armadillo Dr", "778-243-2909");

INSERT INTO Item(upc, title, item_type, category, company, item_year, price, stock)
VALUES
(0, "Over the Rainbow", "CD", "instrumental", "Warner Bros", 1998, 10, 25),
(1, "Party Rock Anthem", "CD", "classical", "AMS", 2007, 10, 30),
(2, "Amelie", "DVD", "rap", "EZ Records", 2003, 5, 40),
(3, "I'm Yours", "CD", "country", "ABC Records", 2014, 10, 10),
(4, "Gangnam Style", "CD", "pop", "Gangnam Music", 2013, 50, 2),
(5, "Hairspray", "DVD", "rock", "Chick Flickers", 2010, 20, 6),
(6, "Free Willy", "DVD", "classical", "Warner Bros", 2007, 10, 30),
(7, "The Avengers", "DVD", "classical", "Marvel", 2002, 30, 5),
(8, "Mrs Doubtfire", "DVD", "new age", "Robin Productions", 2010, 20, 10),
(9, "Over the Rainbow", "CD", "classical", "Warner Bros", 1998, 25, 25),
(10, "George Sings", "CD", "country", "CS Records", "2014", 100, 30),
(11, "Reflektor", "CD", "rap", "Justin Bieber", 2010, 25, 30),
(12, "Harry Potter", "DVD", "pop", "Apple", 2010, 25, 15),
(13, "Hunger Games", "DVD", "rap", "Warner Bros", 2014, 25, 30),
(14, "Happy Birthday", "CD", "rap", "Justin Bieber", 2011, 24, 10),
(15, "Chestnuts Roasting on an Open Fire", "CD", "rap", "Apple", 2014, 15, 30),
(16, "Grandmas on Weed", "DVD", "pop", "Chick Flickers", 2011, 15, 30),
(17, "Never Gonna Give You Up", "CD", "new age", "EZ Records", 1992, 15, 20),
(18, "Transformers 1", "DVD", "pop", "Apple", 1998, 25, 25),
(19, "Titanic", "DVD", "new age", "Justin Bieber", 1998, 25, 30),
(20, "The Proposal", "DVD", "classical", "Warner Bros", 1990, 25, 10);

INSERT INTO LeadSinger
VALUES 
(0, "Lemon Drops"),
(1, "LMFAO"),
(3, "Jason Mraz"),
(4, "PSY"),
(9, "George"),
(10, "George"),
(11, "Arcade Fire"),
(14, "George"),
(15, "PSY"),
(17, "Rick Astley");

INSERT INTO `Order`
VALUES
(0, "2014-10-02", 5, 1234, 1234, "2014-10-02", "2014-10-05"),
(2, "2014-10-27", 3, 1234, 1234, "2014-10-30", "2014-10-30"),
(3, "2014-11-20", 4, 1234, 1234, "2014-11-23", "2014-11-23"),
(4, "2014-11-20", 1, 1234, 1234, "2014-11-23", "2014-11-23"),
(5, "2014-11-20", 4, 1234, 1234, "2014-11-23", "2014-11-23"),
(6, "2014-11-24", 0, 1234, 1234, "2014-11-27", NULL),
(7, "2014-11-24", 6, 1234, 1234, "2014-11-27", NULL),
(8, "2014-11-24", 2, 1234, 1234, "2014-11-27", NULL);

INSERT INTO PurchaseItem
VALUES
(0, 4, 2),
(0, 6, 1),
(0, 10, 3),
(2, 15, 1),
(2, 18, 1),
(3, 17, 1),
(4, 1, 10),
(5, 9, 1),
(5, 14, 1),
(6, 18, 2),
(6, 17, 5),
(6, 10, 1),
(6, 1, 2),
(7, 1, 3),
(8, 2, 4);

INSERT INTO `Return`
VALUES
(1, "2014-10-10", 1),
(2, "2014-11-24", 5);

INSERT INTO ReturnItem
VALUES
(1, 1, 1),
(1, 5, 10),
(2, 16, 2);
