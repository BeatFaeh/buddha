<?php
declare(strict_types=1);
$auth->requireAdmin(); $csrf->verify();
$id=filter_input(INPUT_POST,'id',FILTER_VALIDATE_INT);
if (!$id) $flash->set('error','Ungültige Lernkarten-ID.'); elseif ($cardRepository->delete((int)$id)) $flash->set('success','Die Lernkarte wurde gelöscht.'); else $flash->set('error','Die Lernkarte konnte nicht gelöscht werden.');
header('Location: index.php?action=admin#lernkarten'); exit;
