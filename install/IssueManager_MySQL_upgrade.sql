DROP DATABASE IF EXISTS locales;
CREATE TABLE locales (
  locale_name varchar(50) NOT NULL,
  PRIMARY KEY (locale_name)
);

INSERT INTO locales VALUES ('en');
INSERT INTO locales VALUES ('pl');

DROP DATABASE IF EXISTS styles;
CREATE TABLE styles (
  style_name varchar(50) NOT NULL,
  style_transl varchar(50) default NULL,
  PRIMARY KEY(style_name)
);

INSERT INTO styles VALUES ('Compact',NULL);
INSERT INTO styles VALUES ('Blueprint',NULL);
INSERT INTO styles VALUES ('GreenApple',NULL);
INSERT INTO styles VALUES ('Innovation',NULL);
INSERT INTO styles VALUES ('Lollipop',NULL);
INSERT INTO styles VALUES ('Simple',NULL);
INSERT INTO styles VALUES ('Spring',NULL);

CREATE TABLE email_components (
	component_id INTEGER DEFAULT 0 NOT NULL,
	component_name varchar(50) default NULL,
	PRIMARY KEY(component_id)
);

INSERT INTO email_components VALUES (0,'res:im_component_none');
INSERT INTO email_components VALUES (1,'res:im_component_test');
INSERT INTO email_components VALUES (8,'PHP mail()');

ALTER TABLE priorities MODIFY COLUMN priority_desc VARCHAR(100);

UPDATE priorities SET priority_desc='res:im_priority_highest' WHERE priority_desc='Highest';
UPDATE priorities SET priority_desc='res:im_priority_high' WHERE priority_desc='High';
UPDATE priorities SET priority_desc='res:im_priority_normal' WHERE priority_desc='Normal';
UPDATE priorities SET priority_desc='res:im_priority_low' WHERE priority_desc='Low';
UPDATE priorities SET priority_desc='res:im_priority_lowest' WHERE priority_desc='Lowest';

ALTER TABLE statuses MODIFY COLUMN status VARCHAR(100);

UPDATE statuses SET status='res:im_status_open' WHERE status='Open';
UPDATE statuses SET status='res:im_status_on_hold' WHERE status='On hold';
UPDATE statuses SET status='res:im_status_closed' WHERE status='Closed';
UPDATE statuses SET status='res:im_status_in_progress' WHERE status='In progress';
UPDATE statuses SET status='res:im_status_questions' WHERE status='Questions';
UPDATE statuses SET status='res:im_status_proposed' WHERE status='Proposed';
UPDATE statuses SET status='res:im_status_compl_tested' WHERE status='Compl&Tested';
UPDATE statuses SET status='res:im_status_completed' WHERE status='Completed';

ALTER TABLE settings ADD COLUMN email_component INTEGER;
ALTER TABLE settings ADD COLUMN smtp_host VARCHAR(100);
UPDATE settings SET email_component = '8';
UPDATE settings SET file_extensions='*.zip;*.pdf;*.doc;*.rtf;*.gif;*.jpg;*.png;*.txt;';