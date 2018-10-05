DROP TABLE IF EXISTS visit;
DROP TABLE IF EXISTS visitor;

CREATE TABLE visitor (
  ip VARCHAR(15) NOT NULL PRIMARY KEY,
  browser VARCHAR (255) NOT NULL,
  os VARCHAR (255) NOT NULL
);

CREATE TABLE visit (
  id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  ip VARCHAR(15) NOT NULL,
  date DATETIME NOT NULL,
  referer VARCHAR (500) NOT NULL,
  path VARCHAR (500) NOT NULL,
  FOREIGN KEY (ip) REFERENCES visitor(ip) ON DELETE CASCADE ON UPDATE CASCADE 
);