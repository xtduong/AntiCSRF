AntiCsrf 
USE : 
1 : require 'csrf.php';
2 : $csrf = new CSRF();
3 : <?= $csrf->getInput(); ?>

