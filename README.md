_Hinweis: Tipp zur besseren Gestaltung der Readme https://dev.to/rohit19060/how-to-write-stunning-github-readme-md-template-provided-5b09_

# rrze-campo

- WordPress Plugin mit Shortcode, Gutenberg-ready, TinyMCE compatible, Widget (Block und Classic)
- vereint die Funktionalität aller bisheriger Plugins, die UnivIS nutzen: rrze-univis, fau-person
- liefert aufbereitete Daten zu Lehrveranstaltungen und Mitarbeitern von Campo
- Aufbau orientiert sich am MVC-Modell (vor allem Datenaufbereitung ist nur an einer Stelle und gekapselt)
- Verwendet PHPUnit (jede Funktion ist gekapselt testbar; Test wird in Settings ein/ausgeschaltet)
- Generiert im Fehlerfall Logs, die rrze-log nutzen kann
- single site und multisite kompatibel
- Shortcodes sind abwärtskompatibel bis zur aktuellen Version von rrze-univis und fau-person (Stand 01.04.2022)
- ist auf hohe Performance ausgelegt (KISS, durchdachtes Caching, transient und reduced to the maximum code)
- funktioniert auch bei Störungen der API zu Campo (cache mit optionaler Ausgabe des Timestamps)

Vorlage für Shortcode:
|Parameter|Pflichtfeld|Wert|Default|
|---|---|---|---|
|_Mein Parameter_|Ja|positive Ganzzahl|42|

 


