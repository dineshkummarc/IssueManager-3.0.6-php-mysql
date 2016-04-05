DROP TABLE IF EXISTS email_components;
CREATE TABLE email_components (
	component_id INTEGER DEFAULT 0 NOT NULL,
	component_name varchar(50) default NULL,
	PRIMARY KEY(component_id)
);

INSERT INTO email_components VALUES (0,'res:im_component_none');
INSERT INTO email_components VALUES (1,'res:im_component_test');
INSERT INTO email_components VALUES (8,'PHP mail()');

DROP TABLE IF EXISTS files;
CREATE TABLE files (
  file_id int(11) NOT NULL auto_increment,
  file_name varchar(100) default NULL,
  issue_id int(11) default '0',
  date_uploaded datetime default NULL,
  uploaded_by int(11) default '0',
  PRIMARY KEY  (file_id),
  UNIQUE KEY file_id (file_id),
  KEY issue_id (issue_id)
);

DROP TABLE IF EXISTS issues;
CREATE TABLE issues (
  issue_id int(11) NOT NULL auto_increment,
  issue_name varchar(100) default NULL,
  issue_desc text,
  user_id int(11) default '0',
  priority_id int(11) default '0',
  status_id int(11) default '0',
  version varchar(10) default NULL,
  approved tinyint(4) default '0',
  tested tinyint(4) default '0',
  assigned_to int(11) default '0',
  assigned_to_orig int(11) default '0',
  date_submitted datetime default NULL,
  date_resolved datetime default NULL,
  date_modified datetime default NULL,
  modified_by int(11) default '0',
  PRIMARY KEY  (issue_id),
  UNIQUE KEY issue_id (issue_id),
  KEY priority_id (priority_id),
  KEY status_id (status_id)
);

DROP TABLE IF EXISTS locales;
CREATE TABLE locales (
  locale_name varchar(50) NOT NULL default '',
  PRIMARY KEY  (locale_name)
);

INSERT INTO locales VALUES ('en');
INSERT INTO locales VALUES ('pl');

DROP TABLE IF EXISTS priorities;
CREATE TABLE priorities (
  priority_id int(11) NOT NULL auto_increment,
  priority_desc varchar(50) default NULL,
  priority_order int(11) default '0',
  priority_color varchar(30) default NULL,
  PRIMARY KEY  (priority_id),
  UNIQUE KEY priority_order (priority_order),
  KEY priority_id (priority_id)
);

INSERT INTO `priorities` VALUES (1,'res:im_priority_highest',10,'red'),(2,'res:im_priority_high',20,'blue'),(3,'res:im_priority_normal',30,NULL),(4,'res:im_priority_low',40,'#444444'),(5,'res:im_priority_lowest',50,'#dddddd');

DROP TABLE IF EXISTS responses;
CREATE TABLE responses (
  response_id int(11) NOT NULL auto_increment,
  issue_id int(11) default '0',
  response text,
  user_id int(11) default '0',
  priority_id int(11) default '0',
  status_id int(11) default '0',
  version varchar(10) default NULL,
  approved tinyint(4) default '0',
  tested tinyint(4) default '0',
  assigned_to int(11) default '0',
  date_response datetime default NULL,
  PRIMARY KEY  (response_id),
  KEY response_id (response_id),
  KEY issue_id (issue_id),
  KEY priority_id (priority_id),
  KEY status_id (status_id),
  KEY user_id (user_id)
);

DROP TABLE IF EXISTS settings;
CREATE TABLE settings (
  settings_id int(11) NOT NULL auto_increment,
  file_extensions text,
  file_path text,
  notify_new_from varchar(50) default NULL,
  notify_new_subject text,
  notify_new_body text,
  notify_change_from varchar(50) default NULL,
  notify_change_subject text,
  notify_change_body text,
  upload_enabled char(1) default NULL,
  email_component char(1) default NULL,
  smtp_host varchar(100) default NULL,
  PRIMARY KEY  (settings_id),
  KEY settings_id (settings_id)
);

INSERT INTO settings VALUES (1,'*.zip;*.pdf;*.doc;*.rtf;*.gif;*.jpg;*.png;*.txt;','uploads','IssueManager@company.com','New Issue# {issue_no} was Submitted','hi {issue_receiver},\r\n<b>{issue_title}</b> was submitted {issue_url} by <b>{issue_poster}</b>','IssueManager@vcalendar.org','Issue# {issue_no} was Changed','hi {issue_receiver},\r\n<b>{issue_title}</b> was changed {issue_url} by <b>{issue_poster}</b>','1','8','');

DROP TABLE IF EXISTS statuses;
CREATE TABLE statuses (
  status_id int(11) NOT NULL auto_increment,
  status varchar(50) default NULL,
  PRIMARY KEY  (status_id)
);

INSERT INTO `statuses` VALUES (1,'res:im_status_open'),(2,'res:im_status_on_hold'),(3,'res:im_status_closed'),(4,'res:im_status_in_progress'),(5,'res:im_status_questions'),(6,'res:im_status_proposed'),(7,'res:im_status_compl_tested'),(8,'res:im_status_completed');

DROP TABLE IF EXISTS styles;
CREATE TABLE styles (
  style_name varchar(50) NOT NULL default '',
  style_transl varchar(50) default NULL,
  PRIMARY KEY  (style_name)
);

INSERT INTO styles VALUES ('Compact',NULL);
INSERT INTO styles VALUES ('Blueprint',NULL);
INSERT INTO styles VALUES ('GreenApple',NULL);
INSERT INTO styles VALUES ('Innovation',NULL);
INSERT INTO styles VALUES ('Lollipop',NULL);
INSERT INTO styles VALUES ('Simple',NULL);
INSERT INTO styles VALUES ('Spring',NULL);

DROP TABLE IF EXISTS users;
CREATE TABLE users (
  user_id int(11) NOT NULL auto_increment,
  user_name varchar(50) default NULL,
  login varchar(15) default NULL,
  pass varchar(15) default NULL,
  email varchar(50) default NULL,
  security_level tinyint(4) default '0',
  notify_new int(11) default '0',
  notify_original int(11) default '0',
  notify_reassignment int(11) default '0',
  allow_upload int(11) default '0',
  PRIMARY KEY  (user_id),
  KEY user_id (user_id)
);

INSERT INTO users VALUES (5,'Administrator','admin','admin','admin@company.com',3,0,0,0,1);
