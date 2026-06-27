create user 'appuser'@'%' identified by 'app';

grant all privileges on consultation.* to 'appuser'@'%';

flush privileges;