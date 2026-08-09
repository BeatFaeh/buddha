<?php
declare(strict_types=1);

$settingsRepository->save(
    'admin_password_hash',
    $settingsRepository->get('admin_password_hash', (string)$appConfig['admin_password_hash'])
);

$seedGlossary = [
    'Anicca' => 'Vergänglichkeit: Alle bedingten Erscheinungen entstehen, verändern sich und vergehen.',
    'Dukkha' => 'Leiden bzw. Unzulänglichkeit: Bedingtes Dasein kann keine dauerhafte Befriedigung geben.',
    'Anattā' => 'Nicht-Selbst: In den fünf Aggregaten ist kein dauerhaftes, unabhängiges Selbst zu finden.',
    'Anatta' => 'Nicht-Selbst: In den fünf Aggregaten ist kein dauerhaftes, unabhängiges Selbst zu finden.',
    'Dhamma' => 'Die Lehre des Buddha, die Wahrheit und das Gesetz der Wirklichkeit.',
    'Kamma' => 'Absichtsvolles Handeln in Gedanken, Worten und Taten; Sanskrit: Karma.',
    'Karma' => 'Absichtsvolles Handeln in Gedanken, Worten und Taten; Pāli: Kamma.',
    'Cetanā' => 'Absicht oder Willensregung; der entscheidende karmische Faktor.',
    'Sati' => 'Achtsamkeit: klares, gegenwärtiges Gewahrsein.',
    'Samādhi' => 'Geistige Sammlung und Vertiefung.',
    'Paññā' => 'Weisheit bzw. befreiende Einsicht.',
    'Nibbāna' => 'Das Erlöschen von Gier, Hass und Verblendung.',
    'Nirvāṇa' => 'Das Erlöschen von Gier, Hass und Verblendung.',
    'Jhāna' => 'Eine meditative Vertiefungsstufe starker Sammlung.',
    'Jhānas' => 'Meditative Vertiefungsstufen starker Sammlung.',
];
$stmt = $db->prepare('INSERT IGNORE INTO tbl_buddhismus_glossar (begriff, erklaerung) VALUES (?, ?)');
if ($stmt) {
    foreach ($seedGlossary as $term => $explanation) {
        $stmt->bind_param('ss', $term, $explanation);
        $stmt->execute();
    }
    $stmt->close();
}
