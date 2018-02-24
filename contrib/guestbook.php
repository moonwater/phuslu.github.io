<?php
if (!file_exists('guestbook.db')) {
    $db = new PDO("sqlite:guestbook.db");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec("CREATE TABLE IF NOT EXISTS messages(username TEXT, message TEXT, created_at DATETIME);");
    $db = null;
}

$db = new PDO('sqlite:guestbook.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_REQUEST['username']) && isset($_REQUEST['message']) && $_REQUEST['username'] != '' && $_REQUEST['message'] != '') {
    $username = $_REQUEST['username'];
    $message = $_REQUEST['message'];
    if (strlen($username) > 64) {
      die("Name too long!");
    }
    if (strlen($message) > 288) {
      die("Comment too long!");
    }
    $stmt = $db->prepare("INSERT INTO messages (username, message, created_at) VALUES (:username, :message, datetime('now'))");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':message', $message);
    $stmt->execute();
}

?><!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Guest Book</title>
<style>
body, td {
  font-family: Arial, Helvetica, sans-serif;
  font-size: 14px;
}
#GuestBook {
  width:500px;
  margin: 0 auto;
}
#GuestBook td {
  font-family: Arial, Helvetica, sans-serif;
  font-size: 12px;
}
#GuestBook #Messages {
  margin-bottom: 10px;
}
#GuestBook #Messages div {
  margin-bottom: 10px;
  border-bottom: 2px solid #CCC
}
#GuestBook #Messages h2 {
  font-family: Arial, Helvetica, sans-serif;
  font-size: 12px;
  font-weight: bold;
}
#GuestBook #Messages p {
  font-family: Arial, Helvetica, sans-serif;
  font-size: 12px;
}
#GuestBook #Messages span {
  font-family: Arial, Helvetica, sans-serif;
  font-size: 11px;
  font-style: italic;
}
</style>
<style></style></head>

<body>
<div id="GuestBook"><div id="Messages">
<div>
  <form method="post">
  <table width="100%" border="0" cellspacing="0" cellpadding="5"><tbody>
  <tr>
    <td colspan="2"><strong>Add your message</strong></td>
    </tr>
  <tr>
    <td>Name</td>
    <td><label>
      <input type="text" name="username" id="username">
    </label></td>
  </tr>
  <tr>
    <td>Comment</td>
    <td><label>
      <textarea name="message" cols="40" rows="5" id="message"></textarea>
    </label></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><label>
      <input type="submit" name="button" id="button" value="Submit">
    </label></td>
  </tr>
  </tbody></table>
  </form>
  </div>
<?php
$results = $db->query('SELECT username, message, created_at FROM messages ORDER BY created_at DESC LIMIT 10');
foreach($results as $row) {
  $username = htmlspecialchars($row['username']);
  $created_at = $row['created_at'];
  $message = htmlspecialchars($row['message']);
  echo "<strong>$username</strong> <span>posted on $created_at</span><p>$message</p><br/>\n";
}
?>
</div></div>
</body>
</html>