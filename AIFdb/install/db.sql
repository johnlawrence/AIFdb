CREATE TABLE IF NOT EXISTS analyses (
  analysisID int(10) unsigned NOT NULL auto_increment,
  title varchar(128) NOT NULL,
  source varchar(128) default NULL,
  analyst varchar(128) NOT NULL,
  nodeSet int(11) NOT NULL,
  date timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (analysisID)
);


CREATE TABLE IF NOT EXISTS edges (
  edgeID int(10) unsigned NOT NULL auto_increment,
  fromID int(10) unsigned NOT NULL,
  toID int(10) unsigned NOT NULL,
  PRIMARY KEY  (edgeID)
);


CREATE TABLE IF NOT EXISTS nodeSetMappings (
  nodeID int(10) unsigned NOT NULL,
  nodeSetID int(10) unsigned NOT NULL,
  PRIMARY KEY  (nodeID,nodeSetID)
);


CREATE TABLE IF NOT EXISTS nodeSets (
  nodeSetID int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (nodeSetID)
);


CREATE TABLE IF NOT EXISTS nodes (
  nodeID int(10) unsigned NOT NULL auto_increment,
  text longtext NOT NULL,
  missing varchar(5) NOT NULL,
  schemeName varchar(45) default NULL,
  refutation varchar(5) NOT NULL,
  type varchar(5) NOT NULL,
  url longtext NOT NULL,
  PRIMARY KEY  (nodeID)
);


CREATE TABLE IF NOT EXISTS participants (
  participantID int(10) unsigned NOT NULL auto_increment,
  firstName varchar(32) NOT NULL,
  surname varchar(32) NOT NULL,
  description text NOT NULL,
  nodeSet int(11) NOT NULL,
  PRIMARY KEY  (participantID)
);


CREATE TABLE IF NOT EXISTS schemeArgumentMappings (
  mapID int(10) unsigned NOT NULL auto_increment,
  schemeID int(10) unsigned NOT NULL,
  argID int(10) unsigned NOT NULL,
  PRIMARY KEY  (mapID)
);


CREATE TABLE IF NOT EXISTS schemeConclusions (
  conclusionID int(10) unsigned NOT NULL auto_increment,
  ofScheme int(11) NOT NULL default -1,
  text longtext NOT NULL,
  hash int(11) NOT NULL,
  PRIMARY KEY  (conclusionID)
);


CREATE TABLE IF NOT EXISTS schemePremises (
  premiseID int(10) unsigned NOT NULL auto_increment,
  ofScheme int(11) NOT NULL default -1,
  premiseText longtext NOT NULL,
  hash int(11) NOT NULL default -1,
  PRIMARY KEY  (premiseID)
);


CREATE TABLE IF NOT EXISTS schemePresumptions (
  presumptionID int(10) unsigned NOT NULL auto_increment,
  ofScheme int(11) NOT NULL default -1,
  text longtext NOT NULL,
  hash int(11) NOT NULL,
  PRIMARY KEY  (presumptionID)
);


CREATE TABLE IF NOT EXISTS schemes (
  schemeID int(10) unsigned NOT NULL auto_increment,
  schemeType varchar(45) NOT NULL,
  schemeName varchar(45) NOT NULL,
  PRIMARY KEY  USING BTREE (schemeID),
  UNIQUE KEY schemes_schemeName (schemeName)
);


CREATE TABLE IF NOT EXISTS user (
  userID int(11) NOT NULL auto_increment,
  username varchar(64) default NULL,
  password varchar(255) default NULL,
  PRIMARY KEY  (userID),
  UNIQUE KEY username (username)
);
