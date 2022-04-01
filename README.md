_Hinweis: Tipp zur besseren Gestaltung der Readme https://dev.to/rohit19060/how-to-write-stunning-github-readme-md-template-provided-5b09_

# rrze-campo

- Liefert aufbereitete Daten zu Lehrveranstaltungen und Mitarbeitern von Campo
- WordPress Plugin mit Shortcode, Gutenberg-ready, TinyMCE compatible, Widget (Block und Classic)
- Vereint die Funktionalität aller bisheriger Plugins, die UnivIS nutzen: rrze-univis, fau-person
- Aufbau orientiert sich am MVC-Modell (vor allem Datenaufbereitung ist nur an einer Stelle und gekapselt)
- Verwendet PHPUnit (jede Funktion ist gekapselt testbar; Test wird in Settings ein/ausgeschaltet)
- Generiert im Fehlerfall Logs, die rrze-log nutzen kann
- Single site und multisite kompatibel
- Shortcodes sind abwärtskompatibel bis zur aktuellen Version von rrze-univis und fau-person (Stand 01.04.2022)
- Ist auf hohe Performance ausgelegt (KISS, durchdachtes Caching, transient und reduced to the maximum code)
- Funktioniert auch bei Störungen der API zu Campo (Cache mit optionaler Ausgabe des Timestamps)
- Deaktiviert rrze-univis und fau-person, sobald diese ablösbar sind (ab V 0.3)

Vorlage für Shortcode:
|Parameter|Pflichtfeld|Wert|Default|
|---|---|---|---|
|_Mein Parameter_|Ja|positive Ganzzahl|42|

 


