<?php
declare(strict_types=1);
$auth->requireAdmin(); $csrf->verify();
$term=trim((string)($_POST['begriff']??'')); $explanation=trim((string)($_POST['erklaerung']??''));
if ($term===''||$explanation==='') $flash->set('error','Begriff und Erklärung müssen ausgefüllt sein.'); elseif ($glossaryRepository->add($term,$explanation)) $flash->set('success','Der neue Glossarbegriff wurde gespeichert.'); else $flash->set('error','Der Begriff existiert möglicherweise bereits.');
header('Location: index.php?action=admin#glossar'); exit;
