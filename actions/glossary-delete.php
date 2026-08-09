<?php
declare(strict_types=1);
$auth->requireAdmin(); $csrf->verify();
$id=filter_input(INPUT_POST,'id',FILTER_VALIDATE_INT);
if (!$id) $flash->set('error','Ungültige Glossar-ID.'); elseif ($glossaryRepository->delete((int)$id)) $flash->set('success','Der Glossarbegriff wurde gelöscht.'); else $flash->set('error','Der Glossarbegriff konnte nicht gelöscht werden.');
header('Location: index.php?action=admin#glossar'); exit;
