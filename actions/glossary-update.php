<?php
declare(strict_types=1);
$auth->requireAdmin(); $csrf->verify();
$id=filter_input(INPUT_POST,'id',FILTER_VALIDATE_INT); $term=trim((string)($_POST['begriff']??'')); $explanation=trim((string)($_POST['erklaerung']??''));
if (!$id||$term===''||$explanation==='') $flash->set('error','Der Glossarbegriff enthält ungültige Angaben.'); elseif ($glossaryRepository->update((int)$id,$term,$explanation)) $flash->set('success','Der Glossarbegriff wurde aktualisiert.'); else $flash->set('error','Der Begriff existiert möglicherweise bereits.');
header('Location: index.php?action=admin#glossar'); exit;
