<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
</head>
<body>
<?php
if ($this->_error->get('code') == '404') {
header("HTTP/1.0 404 Not Found");
header('Location: /error.html');
exit;
}
?>
</body>
</html>