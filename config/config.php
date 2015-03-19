<?php

define("DB_HOST", "mysql.yamahaqro.com");
define("DB_NAME", "dawdatab");
define("DB_USER", "dawm8");
define("DB_PASS", "hailtheking");


define("COOKIE_RUNTIME", 1209600);
define("COOKIE_DOMAIN", ".127.0.0.1");
define("COOKIE_SECRET_KEY", "1gp@TMPS{+$78sfpMJFe-92s");

define("EMAIL_USE_SMTP", true);
define("EMAIL_SMTP_HOST", "ssl://smtp.gmail.com");
define("EMAIL_SMTP_AUTH", true);
define("EMAIL_SMTP_USERNAME", "dominikgbertapelle@gmail.com");
define("EMAIL_SMTP_PASSWORD", "Tisisve123");
define("EMAIL_SMTP_PORT", 465);
define("EMAIL_SMTP_ENCRYPTION", "ssl");

define("EMAIL_PASSWORDRESET_URL", "http://localhost/DAW/password_reset.php");
define("EMAIL_PASSWORDRESET_FROM", "dominikgbertapelle@gmail.com");
define("EMAIL_PASSWORDRESET_FROM_NAME", "Proyecto 2");
define("EMAIL_PASSWORDRESET_SUBJECT", "Recuperar Contraseña");
define("EMAIL_PASSWORDRESET_CONTENT", "Da clic en el link para recuperar tu contraseña:");

define("EMAIL_VERIFICATION_URL", "http://localhost/DAW/register.php");
define("EMAIL_VERIFICATION_FROM", "dominikgbertapelle@gmail.com");
define("EMAIL_VERIFICATION_FROM_NAME", "Proyecto 2");
define("EMAIL_VERIFICATION_SUBJECT", "Activación de cuenta");
define("EMAIL_VERIFICATION_CONTENT", "Da clic en el link para activar tu cuenta:");

define("HASH_COST_FACTOR", "10");