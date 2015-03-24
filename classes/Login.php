<?php
class Login
{

    private $db_connection = null;
    private $user_id = null;
    private $user_name = "";
    private $user_email = "";
    public $user_image_url = "";
    private $user_is_logged_in = false;
    private $password_reset_link_is_valid  = false;
    private $password_reset_was_successful = false;
    public $errors = array();
    public $messages = array();

    public function __construct()
    {
        session_start();

        if (isset($_GET["logout"])) {
            $this->doLogout();

        } elseif (!empty($_SESSION['user_name']) && ($_SESSION['user_logged_in'] == 1)) {
            $this->loginWithSessionData();

            if (isset($_POST["user_edit_submit_name"])) {
                $this->editUserName($_POST['user_name']);
            } elseif (isset($_POST["user_edit_submit_email"])) {
                $this->editUserEmail($_POST['user_email']);
            } elseif (isset($_POST["user_edit_submit_password"])) {
                $this->editUserPassword($_POST['user_password_old'], $_POST['user_password_new'], $_POST['user_password_repeat']);
            }

        } elseif (isset($_COOKIE['rememberme'])) {
            $this->loginWithCookieData();

        } elseif (isset($_POST["login"])) {
            if (!isset($_POST['user_rememberme'])) {
                $_POST['user_rememberme'] = null;
            }
            $this->loginWithPostData($_POST['user_name'], $_POST['user_password'], $_POST['user_rememberme']);
        }

        if (isset($_POST["request_password_reset"]) && isset($_POST['user_name'])) {
            $this->setPasswordResetDatabaseTokenAndSendMail($_POST['user_name']);
        } elseif (isset($_GET["user_name"]) && isset($_GET["verification_code"])) {
            $this->checkIfEmailVerificationCodeIsValid($_GET["user_name"], $_GET["verification_code"]);
        } elseif (isset($_POST["submit_new_password"])) {
            $this->editNewPassword($_POST['user_name'], $_POST['user_password_reset_hash'], $_POST['user_password_new'], $_POST['user_password_repeat']);
        }

        if ($this->isUserLoggedIn() == true) {
            $this->getImageUrl($this->user_email);
        }
    }

    private function databaseConnection()
    {
        if ($this->db_connection != null) {
            return true;
        } else {
            try {
                $this->db_connection = new PDO('mysql:host='. DB_HOST .';dbname='. DB_NAME . ';charset=utf8', DB_USER, DB_PASS);
                return true;
            } catch (PDOException $e) {
                $this->errors[] = MESSAGE_DATABASE_ERROR . $e->getMessage();
            }
        }
        return false;
    }

    private function getUserData($user_email)
    {
        if ($this->databaseConnection()) {
            $query_user = $this->db_connection->prepare('SELECT * FROM users WHERE user_email = :user_email');
            $query_user->bindValue(':user_email', $user_email, PDO::PARAM_STR);
            $query_user->execute();
            return $query_user->fetchObject();
        } else {
            return false;
        }
    }

    private function loginWithSessionData()
    {
        $this->user_name = $_SESSION['user_name'];
        $this->user_email = $_SESSION['user_email'];

        $this->user_is_logged_in = true;
    }

    private function loginWithCookieData()
    {
        if (isset($_COOKIE['rememberme'])) {
            list ($user_id, $token, $hash) = explode(':', $_COOKIE['rememberme']);
            if ($hash == hash('sha256', $user_id . ':' . $token . COOKIE_SECRET_KEY) && !empty($token)) {
                
                if ($this->databaseConnection()) {
                    $sth = $this->db_connection->prepare("SELECT user_id, user_name, user_email FROM users WHERE user_id = :user_id
                                                      AND user_rememberme_token = :user_rememberme_token AND user_rememberme_token IS NOT NULL");
                    $sth->bindValue(':user_id', $user_id, PDO::PARAM_INT);
                    $sth->bindValue(':user_rememberme_token', $token, PDO::PARAM_STR);
                    $sth->execute();
                    $result_row = $sth->fetchObject();

                    if (isset($result_row->user_id)) {
                        $_SESSION['user_id'] = $result_row->user_id;
                        $_SESSION['user_name'] = $result_row->user_name;
                        $_SESSION['user_email'] = $result_row->user_email;
                        $_SESSION['user_logged_in'] = 1;

                        $this->user_id = $result_row->user_id;
                        $this->user_name = $result_row->user_name;
                        $this->user_email = $result_row->user_email;
                        $this->user_is_logged_in = true;

                        $this->newRememberMeCookie();
                        return true;
                    }
                }
            }
            $this->deleteRememberMeCookie();
            $this->errors[] = MESSAGE_COOKIE_INVALID;
        }
        return false;
    }


    private function loginWithPostData($user_name, $user_password, $user_rememberme)
    {
        if (empty($user_name) || empty($user_password)) {
            $this->errors[] = MESSAGE_EMAIL_EMPTY;
        } else {
            if (!filter_var($user_name, FILTER_VALIDATE_EMAIL)) {
                $result_row = $this->getUserData(trim($user_name));

            } else if ($this->databaseConnection()) {
                $query_user = $this->db_connection->prepare('SELECT * FROM users WHERE user_email = :user_email');
                $query_user->bindValue(':user_email', trim($user_name), PDO::PARAM_STR);
                $query_user->execute();
                $result_row = $query_user->fetchObject();
            }

            if (! isset($result_row->user_id)) {
                $this->errors[] = MESSAGE_LOGIN_FAILED;
            } else if (($result_row->user_failed_logins >= 3) && ($result_row->user_last_failed_login > (time() - 30))) {
                $this->errors[] = MESSAGE_PASSWORD_WRONG_3_TIMES;
            } else if (! password_verify($user_password, $result_row->user_password_hash)) {
                $sth = $this->db_connection->prepare('UPDATE users '
                        . 'SET user_failed_logins = user_failed_logins+1, user_last_failed_login = :user_last_failed_login '
                        . 'WHERE user_name = :user_name OR user_email = :user_name');
                $sth->execute(array(':user_name' => $user_name, ':user_last_failed_login' => time()));
                $this->errors[] = MESSAGE_PASSWORD_WRONG;
            } else if ($result_row->user_active != 1) {
                $this->errors[] = MESSAGE_ACCOUNT_NOT_ACTIVATED;
            } else {
                $_SESSION['user_id'] = $result_row->user_id;
                $_SESSION['user_name'] = $result_row->user_name;
                $_SESSION['user_email'] = $result_row->user_email;
                $_SESSION['user_logged_in'] = 1;

                $this->user_id = $result_row->user_id;
                $this->user_name = $result_row->user_name;
                $this->user_email = $result_row->user_email;
                $this->user_is_logged_in = true;

                $sth = $this->db_connection->prepare('UPDATE users '
                        . 'SET user_failed_logins = 0, user_last_failed_login = NULL '
                        . 'WHERE user_id = :user_id AND user_failed_logins != 0');
                $sth->execute(array(':user_id' => $result_row->user_id));

                if (isset($user_rememberme)) {
                    $this->newRememberMeCookie();
                } else {
                    $this->deleteRememberMeCookie();
                }

                if (defined('HASH_COST_FACTOR')) {
                    if (password_needs_rehash($result_row->user_password_hash, PASSWORD_DEFAULT, array('cost' => HASH_COST_FACTOR))) {

                        $user_password_hash = password_hash($user_password, PASSWORD_DEFAULT, array('cost' => HASH_COST_FACTOR));

                        $query_update = $this->db_connection->prepare('UPDATE users SET user_password_hash = :user_password_hash WHERE user_id = :user_id');
                        $query_update->bindValue(':user_password_hash', $user_password_hash, PDO::PARAM_STR);
                        $query_update->bindValue(':user_id', $result_row->user_id, PDO::PARAM_INT);
                        $query_update->execute();

                        if ($query_update->rowCount() == 0) {
                        } else {
                        }
                    }
                }
            }
        }
    }

    private function newRememberMeCookie()
    {
        if ($this->databaseConnection()) {
            $random_token_string = hash('sha256', mt_rand());
            $sth = $this->db_connection->prepare("UPDATE users SET user_rememberme_token = :user_rememberme_token WHERE user_id = :user_id");
            $sth->execute(array(':user_rememberme_token' => $random_token_string, ':user_id' => $_SESSION['user_id']));

            $cookie_string_first_part = $_SESSION['user_id'] . ':' . $random_token_string;
            $cookie_string_hash = hash('sha256', $cookie_string_first_part . COOKIE_SECRET_KEY);
            $cookie_string = $cookie_string_first_part . ':' . $cookie_string_hash;

            setcookie('rememberme', $cookie_string, time() + COOKIE_RUNTIME, "/", COOKIE_DOMAIN);
        }
    }

    private function deleteRememberMeCookie()
    {
        if ($this->databaseConnection()) {
            $sth = $this->db_connection->prepare("UPDATE users SET user_rememberme_token = NULL WHERE user_id = :user_id");
            $sth->execute(array(':user_id' => $_SESSION['user_id']));
        }

        setcookie('rememberme', false, time() - (3600 * 3650), '/', COOKIE_DOMAIN);
    }

    public function doLogout()
    {
        $this->deleteRememberMeCookie();

        $_SESSION = array();
        session_destroy();

        $this->user_is_logged_in = false;
        $this->messages[] = MESSAGE_LOGGED_OUT;
    }

    public function newAlbum($album_name, $user_id) {
        if ($this->databaseConnection()) {
            $query_new_album_insert = $this->db_connection->prepare('INSERT INTO albums (name, user_id) VALUES(:album_name, :user_id)');
            $query_new_album_insert->bindValue(':album_name', $album_name, PDO::PARAM_STR);
            $query_new_album_insert->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $query_new_album_insert->execute();

            $album_id = $this->db_connection->lastInsertId();

            if($album_id > 0) {
                return $album_id;
            }
        }
        return false;
    }

    public function isUserLoggedIn()
    {
        return $this->user_is_logged_in;
    }

    public function editUserName($user_name)
    {
        $user_name = substr(trim($user_name), 0, 64);

        if (!empty($user_name) && $user_name == $_SESSION['user_name']) {
            $this->errors[] = MESSAGE_USERNAME_SAME_LIKE_OLD_ONE;

        } elseif (empty($user_name) || !preg_match("/^(?=.{2,64}$)[a-zA-Z][a-zA-Z0-9]*(?: [a-zA-Z0-9]+)*$/", $user_name)) {
            $this->errors[] = MESSAGE_USERNAME_INVALID;

        } else {
            $result_row = $this->getUserData($user_name);

            if (isset($result_row->user_id)) {
                $this->errors[] = MESSAGE_USERNAME_EXISTS;
            } else {
                $query_edit_user_name = $this->db_connection->prepare('UPDATE users SET user_name = :user_name WHERE user_id = :user_id');
                $query_edit_user_name->bindValue(':user_name', $user_name, PDO::PARAM_STR);
                $query_edit_user_name->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
                $query_edit_user_name->execute();

                if ($query_edit_user_name->rowCount()) {
                    $_SESSION['user_name'] = $user_name;
                    $this->messages[] = MESSAGE_USERNAME_CHANGED_SUCCESSFULLY . $user_name;
                } else {
                    $this->errors[] = MESSAGE_USERNAME_CHANGE_FAILED;
                }
            }
        }
    }

    public function editUserEmail($user_email)
    {
        $user_email = substr(trim($user_email), 0, 64);

        if (!empty($user_email) && $user_email == $_SESSION["user_email"]) {
            $this->errors[] = MESSAGE_EMAIL_SAME_LIKE_OLD_ONE;
        } elseif (empty($user_email) || !filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = MESSAGE_EMAIL_INVALID;

        } else if ($this->databaseConnection()) {
            $query_user = $this->db_connection->prepare('SELECT * FROM users WHERE user_email = :user_email');
            $query_user->bindValue(':user_email', $user_email, PDO::PARAM_STR);
            $query_user->execute();
            $result_row = $query_user->fetchObject();

            if (isset($result_row->user_id)) {
                $this->errors[] = MESSAGE_EMAIL_ALREADY_EXISTS;
            } else {
                $query_edit_user_email = $this->db_connection->prepare('UPDATE users SET user_email = :user_email WHERE user_id = :user_id');
                $query_edit_user_email->bindValue(':user_email', $user_email, PDO::PARAM_STR);
                $query_edit_user_email->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
                $query_edit_user_email->execute();

                if ($query_edit_user_email->rowCount()) {
                    $_SESSION['user_email'] = $user_email;
                    $this->messages[] = MESSAGE_EMAIL_CHANGED_SUCCESSFULLY . $user_email;
                } else {
                    $this->errors[] = MESSAGE_EMAIL_CHANGE_FAILED;
                }
            }
        }
    }

    public function editUserPassword($user_password_old, $user_password_new, $user_password_repeat)
    {
        if (empty($user_password_new) || empty($user_password_repeat) || empty($user_password_old)) {
            $this->errors[] = MESSAGE_PASSWORD_EMPTY;
        } elseif ($user_password_new !== $user_password_repeat) {
            $this->errors[] = MESSAGE_PASSWORD_BAD_CONFIRM;
        } elseif (strlen($user_password_new) < 6) {
            $this->errors[] = MESSAGE_PASSWORD_TOO_SHORT;

        } else {
            $result_row = $this->getUserData($_SESSION['user_name']);

            if (isset($result_row->user_password_hash)) {

                if (password_verify($user_password_old, $result_row->user_password_hash)) {

                    $hash_cost_factor = (defined('HASH_COST_FACTOR') ? HASH_COST_FACTOR : null);
                    $user_password_hash = password_hash($user_password_new, PASSWORD_DEFAULT, array('cost' => $hash_cost_factor));

                    $query_update = $this->db_connection->prepare('UPDATE users SET user_password_hash = :user_password_hash WHERE user_id = :user_id');
                    $query_update->bindValue(':user_password_hash', $user_password_hash, PDO::PARAM_STR);
                    $query_update->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
                    $query_update->execute();

                    if ($query_update->rowCount()) {
                        $this->messages[] = MESSAGE_PASSWORD_CHANGED_SUCCESSFULLY;
                    } else {
                        $this->errors[] = MESSAGE_PASSWORD_CHANGE_FAILED;
                    }
                } else {
                    $this->errors[] = MESSAGE_OLD_PASSWORD_WRONG;
                }
            } else {
                $this->errors[] = MESSAGE_USER_DOES_NOT_EXIST;
            }
        }
    }

    public function setPasswordResetDatabaseTokenAndSendMail($user_name)
    {
        $user_name = trim($user_name);

        if (empty($user_name)) {
            $this->errors[] = MESSAGE_USERNAME_EMPTY;

        } else {
            $temporary_timestamp = time();
            $user_password_reset_hash = sha1(uniqid(mt_rand(), true));
            $result_row = $this->getUserData($user_name);

            if (isset($result_row->user_id)) {

                $query_update = $this->db_connection->prepare('UPDATE users SET user_password_reset_hash = :user_password_reset_hash,
                                                               user_password_reset_timestamp = :user_password_reset_timestamp
                                                               WHERE user_name = :user_name');
                $query_update->bindValue(':user_password_reset_hash', $user_password_reset_hash, PDO::PARAM_STR);
                $query_update->bindValue(':user_password_reset_timestamp', $temporary_timestamp, PDO::PARAM_INT);
                $query_update->bindValue(':user_name', $user_name, PDO::PARAM_STR);
                $query_update->execute();

                if ($query_update->rowCount() == 1) {
                    $this->sendPasswordResetMail($user_name, $result_row->user_email, $user_password_reset_hash);
                    return true;
                } else {
                    $this->errors[] = MESSAGE_DATABASE_ERROR;
                }
            } else {
                $this->errors[] = MESSAGE_USER_DOES_NOT_EXIST;
            }
        }
        return false;
    }

    public function sendPasswordResetMail($user_name, $user_email, $user_password_reset_hash)
    {
        $mail = new PHPMailer;

        if (EMAIL_USE_SMTP) {
            $mail->IsSMTP();
            $mail->SMTPAuth = EMAIL_SMTP_AUTH;
            if (defined(EMAIL_SMTP_ENCRYPTION)) {
                $mail->SMTPSecure = EMAIL_SMTP_ENCRYPTION;
            }
            $mail->Host = EMAIL_SMTP_HOST;
            $mail->Username = EMAIL_SMTP_USERNAME;
            $mail->Password = EMAIL_SMTP_PASSWORD;
            $mail->Port = EMAIL_SMTP_PORT;
        } else {
            $mail->IsMail();
        }

        $mail->From = EMAIL_PASSWORDRESET_FROM;
        $mail->FromName = EMAIL_PASSWORDRESET_FROM_NAME;
        $mail->AddAddress($user_email);
        $mail->Subject = EMAIL_PASSWORDRESET_SUBJECT;

        $link    = EMAIL_PASSWORDRESET_URL.'?user_name='.urlencode($user_name).'&verification_code='.urlencode($user_password_reset_hash);
        $mail->Body = EMAIL_PASSWORDRESET_CONTENT . ' ' . $link;

        if(!$mail->Send()) {
            $this->errors[] = MESSAGE_PASSWORD_RESET_MAIL_FAILED . $mail->ErrorInfo;
            return false;
        } else {
            $this->messages[] = MESSAGE_PASSWORD_RESET_MAIL_SUCCESSFULLY_SENT;
            return true;
        }
    }

    public function checkIfEmailVerificationCodeIsValid($user_name, $verification_code)
    {
        $user_name = trim($user_name);

        if (empty($user_name) || empty($verification_code)) {
            $this->errors[] = MESSAGE_LINK_PARAMETER_EMPTY;
        } else {
            $result_row = $this->getUserData($user_name);

            if (isset($result_row->user_id) && $result_row->user_password_reset_hash == $verification_code) {

                $timestamp_one_hour_ago = time() - 3600; 

                if ($result_row->user_password_reset_timestamp > $timestamp_one_hour_ago) {
                    $this->password_reset_link_is_valid = true;
                } else {
                    $this->errors[] = MESSAGE_RESET_LINK_HAS_EXPIRED;
                }
            } else {
                $this->errors[] = MESSAGE_USER_DOES_NOT_EXIST;
            }
        }
    }

    public function editNewPassword($user_name, $user_password_reset_hash, $user_password_new, $user_password_repeat)
    {
        $user_name = trim($user_name);

        if (empty($user_name) || empty($user_password_reset_hash) || empty($user_password_new) || empty($user_password_repeat)) {
            $this->errors[] = MESSAGE_PASSWORD_EMPTY;
        } else if ($user_password_new !== $user_password_repeat) {
            $this->errors[] = MESSAGE_PASSWORD_BAD_CONFIRM;
        } else if (strlen($user_password_new) < 6) {
            $this->errors[] = MESSAGE_PASSWORD_TOO_SHORT;
        } else if ($this->databaseConnection()) {
            $hash_cost_factor = (defined('HASH_COST_FACTOR') ? HASH_COST_FACTOR : null);
            $user_password_hash = password_hash($user_password_new, PASSWORD_DEFAULT, array('cost' => $hash_cost_factor));

            $query_update = $this->db_connection->prepare('UPDATE users SET user_password_hash = :user_password_hash,
                                                           user_password_reset_hash = NULL, user_password_reset_timestamp = NULL
                                                           WHERE user_name = :user_name AND user_password_reset_hash = :user_password_reset_hash');
            $query_update->bindValue(':user_password_hash', $user_password_hash, PDO::PARAM_STR);
            $query_update->bindValue(':user_password_reset_hash', $user_password_reset_hash, PDO::PARAM_STR);
            $query_update->bindValue(':user_name', $user_name, PDO::PARAM_STR);
            $query_update->execute();

            if ($query_update->rowCount() == 1) {
                $this->password_reset_was_successful = true;
                $this->messages[] = MESSAGE_PASSWORD_CHANGED_SUCCESSFULLY;
            } else {
                $this->errors[] = MESSAGE_PASSWORD_CHANGE_FAILED;
            }
        }
    }

    public function passwordResetLinkIsValid()
    {
        return $this->password_reset_link_is_valid;
    }

    public function passwordResetWasSuccessful()
    {
        return $this->password_reset_was_successful;
    }

    public function getUsername()
    {
        return $this->user_name;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function getFolders()
    {
        if ($this->databaseConnection()) {
            $query_albums_select = $this->db_connection->prepare('SELECT * FROM albums where user_id = :user_id');
            $query_albums_select->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $query_albums_select->execute();
            $result = $query_albums_select->fetchAll();
            $_SESSION['user_albums'] = $result;
            return $result;
        }
        return false;
    }

    /**
     * No implementado
     */
    public function getImageUrl($email)
    {
        
    }
}