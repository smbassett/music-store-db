USE AMS; 
SELECT database();

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
(15, "Chestnuts", "CD", "rap", "Apple", 2014, 15, 30),
(16, "Grandmas on Weed", "DVD", "pop", "Chick Flickers", 2011, 15, 30),
(17, "Never Gonna Let You Down", "CD", "new age", "EZ Records", 1992, 15, 20),
(18, "Transformers 1", "DVD", "pop", "Apple", 1998, 25, 25),
(19, "Titanic", "DVD", "new age", "Justin Bieber", 1998, 25, 30),
(20, "The Proposal", "DVD", "classical", "Warner Bros", 1990, 25, 10);