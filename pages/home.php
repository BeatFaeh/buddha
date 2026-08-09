<?php
declare(strict_types=1);
$mode=(string)($_GET['modus']??'text'); if(!in_array($mode,['text','mc'],true))$mode='text';
$card=$cardRepository->random(); $count=$cardRepository->count(); $quiz=['options'=>[],'correct_key'=>''];
if($mode==='mc'&&$card)$quiz=$quizService->build($card);
$glossary=$glossaryRepository->asMap();
?><!doctype html><html lang="de"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Buddhistische Lernkarten</title><link rel="stylesheet" href="assets/css/main.css"></head><body><main class="page"><div class="wrapper">
<?php require __DIR__.'/../partials/public-hero.php'; ?>
<nav class="mode-switch" aria-label="Lernmodus wählen"><a class="mode-button <?= $mode==='text'?'active':'' ?>" href="index.php?modus=text">📖 Lernkarte</a><a class="mode-button <?= $mode==='mc'?'active':'' ?>" href="index.php?modus=mc">✓ Multiple Choice</a></nav>
<?php if(!$card): ?><section class="learning-card"><div class="card-content"><h2>Keine Lernkarten vorhanden</h2></div></section><?php else: ?>
<section class="learning-card"><div class="card-content"><div class="meta"><span class="badge"><?= $mode==='mc'?'Multiple Choice':'Karte' ?> #<?= (int)$card['id'] ?></span><span class="counter"><?= $count ?> <?= $count===1?'Lernkarte':'Lernkarten' ?></span></div><p class="question-label"><?= $mode==='mc'?'Multiple-Choice-Frage':'Frage' ?></p><h2 class="question"><?= Html::e($card['frage']) ?></h2>
<?php if($mode==='mc'): ?>
<?php if(count($quiz['options'])===4&&$quiz['correct_key']!==''): ?><div class="mc-quiz" data-correct="<?= Html::e($quiz['correct_key']) ?>"><?php foreach($quiz['options'] as $letter=>$answer): ?><button type="button" class="mc-option" data-option="<?= Html::e($letter) ?>"><span class="mc-letter"><?= Html::e($letter) ?></span><span><?= Html::e($answer) ?></span></button><?php endforeach; ?><div class="mc-feedback" aria-live="polite"></div></div><?php else: ?><div class="mc-error">Für Multiple Choice werden mindestens vier unterschiedliche Antworten in <code>tbl_buddhismus.antwort</code> benötigt.</div><?php endif; ?>
<?php else: ?><details class="accordion"><summary>Antwort anzeigen</summary><div class="accordion-content"><?= $glossaryFormatter->format($card['antwort'],$glossary) ?></div></details><?php endif; ?>
<div class="actions">
<a class="button button-primary" href="index.php?modus=<?= Html::e($mode) ?>"><?= $mode==='mc'?'Neue Multiple-Choice-Frage':'Neue Zufallsfrage' ?></a>
<a class="button button-secondary" href="index.php?action=pdf">Alle Lernkarten / PDF</a>
<a class="button button-secondary" href="index.php?action=glossar">Glossar</a>
<a class="button button-primary" href="index.php?action=pruefung">📝 Prüfung · 100 Fragen</a>
<a class="button button-secondary" href="literatur/">📚 Literatur</a>
<a class="button button-secondary" href="lernmodule/">🎓 Lernmodule</a>
<a class="button button-secondary" href="meditation/">🧘 Meditation</a>
<a class="button button-admin" href="index.php?action=admin">Administration</a>
</div></div></section><?php endif; ?>
<?php require __DIR__.'/../partials/site-footer.php'; ?></div></main><script src="assets/js/quiz.js"></script></body></html>
